<?php
namespace App\Http\Controllers;

use App\Jobs\ImportCsvJob;
use App\Models\ImportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ImportController extends Controller
{
    /**
     * List all import logs (DataTable AJAX or blade view).
     */
    public function index(Request $request)
    {
        $this->authorize('imports.view');

        if ($request->ajax()) {
            $query = ImportLog::with('uploader')->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('status_badge', function (ImportLog $log) {
                    return match ($log->status) {
                        'pending'     => '<span class="badge bg-secondary">Pending</span>',
                        'running'     => '<span class="badge bg-primary"><span class="spinner-border spinner-border-sm me-1" role="status"></span>Running</span>',
                        'completed'   => '<span class="badge bg-success">Completed</span>',
                        'failed'      => '<span class="badge bg-danger">Failed</span>',
                        'rolled_back' => '<span class="badge bg-warning text-dark">Rolled Back</span>',
                        default       => '<span class="badge bg-secondary">' . ucfirst($log->status) . '</span>',
                    };
                })
                ->addColumn('uploader_name', fn(ImportLog $log) => optional($log->uploader)->name ?? '—')
                ->addColumn('created_at_formatted', fn(ImportLog $log) => $log->created_at?->format('d M Y, H:i') ?? '—')
                ->filterColumn('uploader_name', fn($q, $k) =>
                    $q->whereHas('uploader', fn($s) => $s->where('name', 'like', "%{$k}%"))
                )
                ->addColumn('action', function (ImportLog $log) {
                    $btns = '';

                    if ($log->failed_count > 0 && in_array($log->status, ['completed', 'failed'])) {
                        $btns .= '<a href="' . route('imports.download-failed', $log->id) . '" class="btn btn-sm btn-outline-warning me-1" title="Download Failed Rows"><i class="bi bi-download"></i></a>';
                    }

                    if ($log->status === 'completed') {
                        $btns .= '<button class="btn btn-sm btn-outline-danger me-1 btn-rollback" data-id="' . $log->id . '" data-token="' . csrf_token() . '" title="Rollback Import"><i class="bi bi-arrow-counterclockwise"></i></button>';
                    }

                    $btns .= '<button class="btn btn-sm btn-outline-secondary btn-delete-import" data-id="' . $log->id . '" data-token="' . csrf_token() . '" title="Delete"><i class="bi bi-trash3"></i></button>';

                    return $btns;
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('admin.imports.index');
    }

    /**
     * Upload a CSV file and return its headers for column mapping.
     */
    public function upload(Request $request)
    {
        $this->authorize('imports.create');

        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ]);

        $file     = $request->file('csv_file');
        $filename = $file->getClientOriginalName();
        $path     = $file->store('imports');   // storage/app/imports/

        // Parse headers from the first row
        $fullPath = storage_path('app/' . $path);
        $handle   = fopen($fullPath, 'r');
        $headers  = fgetcsv($handle);
        fclose($handle);

        // Trim BOM & whitespace
        $headers = array_map(fn($h) => trim(preg_replace('/^\xEF\xBB\xBF/', '', $h)), $headers);

        $log = ImportLog::create([
            'filename'    => $filename,
            'disk_path'   => $path,
            'status'      => 'pending',
            'uploaded_by' => auth()->id(),
        ]);

        return response()->json([
            'import_log_id' => $log->id,
            'csv_columns'   => array_values(array_filter($headers)),
            'filename'      => $filename,
        ]);
    }

    /**
     * Start the import job after column mapping is confirmed.
     */
    public function start(Request $request)
    {
        $this->authorize('imports.create');

        $request->validate([
            'import_log_id'   => ['required', 'integer', 'exists:import_logs,id'],
            'mapping'         => ['required', 'array'],
            'search_criteria' => ['nullable', 'array'],
        ]);

        $log = ImportLog::findOrFail($request->import_log_id);

        if (!in_array($log->status, ['pending', 'failed'])) {
            return response()->json(['error' => 'Import already running or completed.'], 422);
        }

        $searchCriteria = $request->input('search_criteria', []);

        $log->update([
            'column_mapping'  => $request->mapping,
            'search_criteria' => $searchCriteria ?: null,
        ]);

        ImportCsvJob::dispatch($log->id, $request->mapping, $searchCriteria);

        return response()->json(['batch_id' => $log->id]);
    }

    /**
     * Poll the import progress from cache.
     */
    public function progress(Request $request)
    {
        $request->validate(['import_log_id' => ['required', 'integer']]);

        $cacheKey = "import_progress_{$request->import_log_id}";
        $progress = Cache::get($cacheKey);

        if (!$progress) {
            // Fallback to DB
            $log = ImportLog::find($request->import_log_id);
            if ($log) {
                $progress = [
                    'total'     => $log->total_rows,
                    'processed' => $log->total_rows,
                    'imported'  => $log->imported_count,
                    'failed'    => $log->failed_count,
                    'skipped'   => $log->skipped_count,
                    'status'    => $log->status,
                ];
            } else {
                $progress = ['status' => 'not_found'];
            }
        }

        return response()->json($progress);
    }

    /**
     * Download a CSV file of the failed rows for a given import.
     */
    public function downloadFailed(ImportLog $importLog)
    {
        $this->authorize('imports.view');

        $failedRows = $importLog->failed_rows ?? [];

        $csvLines   = [];
        $csvLines[] = implode(',', ['Row Number', 'Row Data', 'Failure Reason']);

        foreach ($failedRows as $fr) {
            $csvLines[] = implode(',', [
                $fr['row']    ?? '',
                '"' . str_replace('"', '""', $fr['data']   ?? '') . '"',
                '"' . str_replace('"', '""', $fr['reason'] ?? '') . '"',
            ]);
        }

        $csvContent = implode("\n", $csvLines);
        $safeFilename = 'failed_rows_import_' . $importLog->id . '.csv';

        return response($csvContent, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $safeFilename . '"',
        ]);
    }

    /**
     * Rollback a completed import — deletes all AppointmentService rows tied to this import.
     */
    public function rollback(ImportLog $importLog)
    {
        $this->authorize('imports.rollback');

        if ($importLog->status !== 'completed') {
            return response()->json(['error' => 'Only completed imports can be rolled back.'], 422);
        }

        \App\Models\AppointmentService::where('import_log_id', $importLog->id)->delete();

        $importLog->update(['status' => 'rolled_back']);

        return response()->json(['success' => true, 'message' => 'Import rolled back successfully.']);
    }

    /**
     * Delete an import log record and its stored CSV file.
     */
    public function destroy(ImportLog $importLog)
    {
        $this->authorize('imports.view');

        // Delete the stored file
        if ($importLog->disk_path && Storage::exists($importLog->disk_path)) {
            Storage::delete($importLog->disk_path);
        }

        $importLog->delete();

        return response()->json(['success' => true]);
    }
}
