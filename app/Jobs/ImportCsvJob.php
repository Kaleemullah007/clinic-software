<?php
namespace App\Jobs;

use App\Models\Appointment;
use App\Models\AppointmentService;
use App\Models\Category;
use App\Models\ImportLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ImportCsvJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;
    public int $tries   = 1;

    /**
     * @param int   $importLogId
     * @param array $mapping         Step 2: logical_field → csv_column_header
     * @param array $searchCriteria  Step 3: per-table lookup conditions
     *   Structure:
     *   [
     *     'users'        => [['csv_column' => 'Phone', 'db_column' => 'phone'], ...],
     *     'categories'   => [['csv_column' => 'Service', 'db_column' => 'name'], ...],
     *     'appointments' => [['csv_column' => 'Date', 'db_column' => 'date'], ...],
     *   ]
     */
    public function __construct(
        public readonly int   $importLogId,
        public readonly array $mapping,
        public readonly array $searchCriteria = []
    ) {}

    public function handle(): void
    {
        $log = ImportLog::find($this->importLogId);
        if (!$log) return;

        $log->update(['status' => 'running']);
        $cacheKey = "import_progress_{$this->importLogId}";

        $fullPath = storage_path('app/' . $log->disk_path);
        if (!file_exists($fullPath)) {
            $log->update(['status' => 'failed']);
            return;
        }

        $handle  = fopen($fullPath, 'r');
        $headers = fgetcsv($handle);
        $headers = array_map(fn($h) => trim(preg_replace('/^\xEF\xBB\xBF/', '', $h)), $headers);

        $allRows = [];
        while (($row = fgetcsv($handle)) !== false) {
            $allRows[] = $row;
        }
        fclose($handle);

        $total      = count($allRows);
        $imported   = 0;
        $failed     = 0;
        $skipped    = 0;
        $failedRows = [];

        Cache::put($cacheKey, [
            'total'     => $total,
            'processed' => 0,
            'imported'  => 0,
            'failed'    => 0,
            'skipped'   => 0,
            'status'    => 'running',
        ], now()->addHours(2));

        foreach ($allRows as $rowIndex => $row) {
            // Skip completely empty rows
            if (empty(array_filter($row))) {
                $total--;
                continue;
            }

            try {
                if (count($headers) !== count($row)) {
                    throw new \Exception('Column count mismatch');
                }

                // $rawRow  = full CSV row keyed by original file headers
                // $mapped  = extracted logical fields (from Step 2 mapping)
                $rawRow = array_combine($headers, $row);
                $mapped = $this->applyMapping($rawRow);

                // ── 1. Find or create User ────────────────────────────────
                $user = $this->findOrCreateUser($rawRow, $mapped);

                // ── 2. Find or create Category (service) ──────────────────
                $category = $this->findOrCreateCategory($rawRow, $mapped);

                // ── 3. Find or create Appointment ─────────────────────────
                $appointment = $this->findOrCreateAppointment($rawRow, $mapped, $user);

                // ── 4. Create AppointmentService record ───────────────────
                $price           = is_numeric($mapped['service_price'] ?? null)
                    ? (float) $mapped['service_price']
                    : ($category->price ?? 0);
                $discountedPrice = is_numeric($mapped['service_discounted_price'] ?? null)
                    ? (float) $mapped['service_discounted_price']
                    : $price;
                $discount        = is_numeric($mapped['service_discount'] ?? null)
                    ? (float) $mapped['service_discount']
                    : max(0, $price - $discountedPrice);

                AppointmentService::create([
                    'appointment_id'   => $appointment->id,
                    'service_id'       => $category->id,
                    'name'             => $category->name,
                    'price'            => $price,
                    'discounted_price' => $discountedPrice,
                    'discount'         => $discount,
                    'import_log_id'    => $this->importLogId,
                ]);

                $imported++;

            } catch (\Throwable $e) {
                $failed++;
                $failedRows[] = [
                    'row'    => $rowIndex + 2,
                    'data'   => implode(',', $row),
                    'reason' => $e->getMessage(),
                ];
            }

            // Push progress every 5 rows or on last row
            if (($rowIndex + 1) % 5 === 0 || ($rowIndex + 1) === $total) {
                Cache::put($cacheKey, [
                    'total'     => $total,
                    'processed' => $rowIndex + 1,
                    'imported'  => $imported,
                    'failed'    => $failed,
                    'skipped'   => $skipped,
                    'status'    => 'running',
                ], now()->addHours(2));
            }
        }

        $log->update([
            'status'         => 'completed',
            'total_rows'     => $total,
            'imported_count' => $imported,
            'failed_count'   => $failed,
            'skipped_count'  => $skipped,
            'failed_rows'    => $failedRows,
        ]);

        Cache::put($cacheKey, [
            'total'     => $total,
            'processed' => $total,
            'imported'  => $imported,
            'failed'    => $failed,
            'skipped'   => $skipped,
            'status'    => 'completed',
        ], now()->addHours(2));
    }

    /* ══════════════════════════════════════════════════════════════
       STEP 2 MAPPING — extract logical field values from raw CSV row
    ══════════════════════════════════════════════════════════════ */
    private function applyMapping(array $rawRow): array
    {
        $result = [];
        foreach ($this->mapping as $logicalField => $csvHeader) {
            if ($csvHeader && array_key_exists($csvHeader, $rawRow)) {
                $result[$logicalField] = trim($rawRow[$csvHeader]);
            }
        }
        return $result;
    }

    /* ══════════════════════════════════════════════════════════════
       DYNAMIC LOOKUP — build WHERE query from user-defined conditions
    ══════════════════════════════════════════════════════════════ */

    /**
     * Build an Eloquent query using the search_criteria conditions for a given table.
     * Each condition: csv_column value from rawRow is matched against db_column.
     */
    private function buildLookupQuery(string $table, array $rawRow): ?\Illuminate\Database\Eloquent\Builder
    {
        $conditions = $this->searchCriteria[$table] ?? [];
        if (empty($conditions)) return null;

        $modelMap = [
            'users'        => User::class,
            'categories'   => Category::class,
            'appointments' => Appointment::class,
        ];

        $modelClass = $modelMap[$table] ?? null;
        if (!$modelClass) return null;

        $query      = $modelClass::query();
        $hasValue   = false;

        foreach ($conditions as $cond) {
            $csvHeader = $cond['csv_column'] ?? null;
            $dbColumn  = $cond['db_column']  ?? null;

            if (!$csvHeader || !$dbColumn) continue;

            $value = trim($rawRow[$csvHeader] ?? '');
            if ($value === '') continue;

            // Special handling for date columns
            if (str_contains(strtolower($dbColumn), 'date')) {
                try {
                    $value = Carbon::parse($value)->format('Y-m-d');
                    $query->whereDate($dbColumn, $value);
                } catch (\Throwable) {
                    $query->where($dbColumn, $value);
                }
            } else {
                $query->where($dbColumn, $value);
            }
            $hasValue = true;
        }

        return $hasValue ? $query : null;
    }

    /* ══════════════════════════════════════════════════════════════
       FIND OR CREATE — Users
    ══════════════════════════════════════════════════════════════ */
    private function findOrCreateUser(array $rawRow, array $mapped): User
    {
        // Try dynamic search criteria first
        $query = $this->buildLookupQuery('users', $rawRow);
        $user  = $query?->first();

        // Fallback: search by mapped phone or email
        if (!$user) {
            $phone = $mapped['patient_phone'] ?? null;
            $email = $mapped['patient_email'] ?? null;

            if ($phone) $user = User::where('phone', $phone)->first();
            if (!$user && $email) $user = User::where('email', $email)->first();
        }

        if ($user) return $user;

        // Create new patient
        $phone = $mapped['patient_phone'] ?? null;
        $email = $mapped['patient_email'] ?? null;
        $name  = $mapped['patient_name']  ?? ($phone ?? $email ?? 'Unknown');

        if (!$phone && !$email) {
            throw new \Exception('Cannot create user: no phone or email value found in row');
        }

        $user = User::create([
            'name'              => $name,
            'phone'             => $phone ?: null,
            'email'             => $email ?: null,
            'role'              => 'patient',
            'password'          => bcrypt(Str::random(12)),
            'email_verified_at' => now(),
            'remember_token'    => Str::random(10),
        ]);

        $patientRole = \Spatie\Permission\Models\Role::where('name', 'patient')->first();
        if ($patientRole) $user->roles()->sync([$patientRole->id]);

        return $user;
    }

    /* ══════════════════════════════════════════════════════════════
       FIND OR CREATE — Categories (services)
    ══════════════════════════════════════════════════════════════ */
    private function findOrCreateCategory(array $rawRow, array $mapped): Category
    {
        // Try dynamic search criteria first
        $query    = $this->buildLookupQuery('categories', $rawRow);
        $category = $query?->first();

        // Fallback: search by mapped service_name
        if (!$category) {
            $name = $mapped['service_name'] ?? null;
            if ($name) $category = Category::where('name', $name)->first();
        }

        if ($category) return $category;

        // Resolve name for creation
        $name = $mapped['service_name'] ?? null;

        // If name still empty, try to pull it from search_criteria 'name' db_column
        if (!$name) {
            foreach ($this->searchCriteria['categories'] ?? [] as $cond) {
                if (($cond['db_column'] ?? '') === 'name') {
                    $name = trim($rawRow[$cond['csv_column']] ?? '');
                    break;
                }
            }
        }

        if (!$name) {
            throw new \Exception('Cannot create category: no service/category name found in row');
        }

        $price    = is_numeric($mapped['service_price'] ?? null) ? (float) $mapped['service_price'] : 0;

        return Category::create([
            'name'   => $name,
            'price'  => $price,
            'status' => 1,
        ]);
    }

    /* ══════════════════════════════════════════════════════════════
       FIND OR CREATE — Appointments
    ══════════════════════════════════════════════════════════════ */
    private function findOrCreateAppointment(array $rawRow, array $mapped, User $user): Appointment
    {
        // Always scope to this user
        $query = Appointment::where('user_id', $user->id);

        // Apply dynamic search criteria (appointments)
        $conditions  = $this->searchCriteria['appointments'] ?? [];
        $hasCondition = false;

        foreach ($conditions as $cond) {
            $csvHeader = $cond['csv_column'] ?? null;
            $dbColumn  = $cond['db_column']  ?? null;
            if (!$csvHeader || !$dbColumn) continue;

            $value = trim($rawRow[$csvHeader] ?? '');
            if ($value === '') continue;

            if (str_contains(strtolower($dbColumn), 'date')) {
                try {
                    $value = Carbon::parse($value)->format('Y-m-d');
                    $query->whereDate($dbColumn, $value);
                } catch (\Throwable) {
                    $query->where($dbColumn, $value);
                }
            } else {
                $query->where($dbColumn, $value);
            }
            $hasCondition = true;
        }

        // Fallback: use mapped appointment_date if no conditions defined
        if (!$hasCondition) {
            $rawDate = $mapped['appointment_date'] ?? null;
            if ($rawDate) {
                try {
                    $date = Carbon::parse($rawDate)->format('Y-m-d');
                    $query->whereDate('date', $date);
                    $hasCondition = true;
                } catch (\Throwable) {}
            }
        }

        $appt = $hasCondition ? $query->first() : null;
        if ($appt) return $appt;

        // ── Create new appointment ────────────────────────────────
        $rawDate = $mapped['appointment_date'] ?? null;

        // Try to get date from search criteria if not in mapping
        if (!$rawDate) {
            foreach ($conditions as $cond) {
                if (str_contains(strtolower($cond['db_column'] ?? ''), 'date')) {
                    $rawDate = trim($rawRow[$cond['csv_column']] ?? '');
                    break;
                }
            }
        }

        if (!$rawDate) throw new \Exception('No appointment date provided');

        try {
            $date = Carbon::parse($rawDate)->format('Y-m-d');
        } catch (\Throwable) {
            throw new \Exception("Invalid appointment date: {$rawDate}");
        }

        $months = [
            1=>'JAN',2=>'FEB',3=>'MAR',4=>'APR',5=>'MAY',6=>'JUN',
            7=>'JUL',8=>'AUG',9=>'SEP',10=>'OCT',11=>'NOV',12=>'DEC',
        ];
        $month  = (int) date('m');
        $series = ($months[$month] ?? 'IMP') . date('Y');
        $serial = (Appointment::where('serial', $series)->max('serial_number') ?? 0) + 1;

        return Appointment::create([
            'user_id'                                  => $user->id,
            'name'                                     => $user->name,
            'phone'                                    => $user->phone,
            'email'                                    => $user->email,
            'date'                                     => $date,
            'is_paid'                                  => 'unpaid',
            'serial'                                   => $series,
            'serial_number'                            => $serial,
            'serial_series'                            => $series . '-' . $serial,
            'price'                                    => 0,
            'discounted_price'                         => 0,
            'subtotal_price'                           => 0,
            'subtotal_discounted_price'                => 0,
            'subtotal_price_after_discount'            => 0,
            'subtotal_discounted_price_after_discount' => 0,
            'paid_amount'                              => 0,
            'remaining_amount'                         => 0,
        ]);
    }

    /* ══════════════════════════════════════════════════════════════
       JOB FAILURE HOOK
    ══════════════════════════════════════════════════════════════ */
    public function failed(\Throwable $exception): void
    {
        ImportLog::where('id', $this->importLogId)->update(['status' => 'failed']);
        Cache::put("import_progress_{$this->importLogId}", [
            'status'  => 'failed',
            'message' => $exception->getMessage(),
        ], now()->addHours(2));
    }
}
