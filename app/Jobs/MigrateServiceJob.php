<?php

namespace App\Jobs;

use App\Models\AppointmentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

/**
 * MigrateServiceJob
 *
 * Reassigns appointment_services.service_id for a given list of IDs
 * to a new target category ID.
 *
 * Progress is tracked in the cache under the key "taxonomy_progress_{batchId}"
 * so the frontend can poll /taxonomy/progress?batch_id=xxx.
 *
 * Queue setup (for real progress):
 *   1. Set QUEUE_CONNECTION=database in .env
 *   2. php artisan queue:table && php artisan migrate
 *   3. php artisan queue:work
 *
 * With QUEUE_CONNECTION=sync the job runs immediately and
 * the progress bar will jump straight to 100% on first poll.
 */
class MigrateServiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $batchId;
    public array  $ids;
    public int    $targetServiceId;

    /** Retry once on failure */
    public int $tries = 2;

    public function __construct(string $batchId, array $ids, int $targetServiceId)
    {
        $this->batchId         = $batchId;
        $this->ids             = $ids;
        $this->targetServiceId = $targetServiceId;
    }

    public function handle(): void
    {
        $total     = count($this->ids);
        $processed = 0;

        // Mark as running
        $this->saveProgress($processed, $total, 'running');

        foreach ($this->ids as $id) {
            AppointmentService::where('id', $id)
                ->update(['service_id' => $this->targetServiceId]);

            $processed++;
            $this->saveProgress($processed, $total, 'running');
        }

        // Mark done
        $this->saveProgress($total, $total, 'done');
    }

    public function failed(\Throwable $exception): void
    {
        Cache::put(
            "taxonomy_progress_{$this->batchId}",
            [
                'total'     => count($this->ids),
                'processed' => 0,
                'status'    => 'failed',
                'error'     => $exception->getMessage(),
            ],
            now()->addMinutes(30)
        );
    }

    private function saveProgress(int $processed, int $total, string $status): void
    {
        Cache::put(
            "taxonomy_progress_{$this->batchId}",
            [
                'total'     => $total,
                'processed' => $processed,
                'status'    => $status,
            ],
            now()->addMinutes(30)
        );
    }
}
