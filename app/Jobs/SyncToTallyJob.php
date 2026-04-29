<?php
namespace App\Jobs;

use App\Models\TallySyncLog;
use App\Services\TallyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncToTallyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(public TallySyncLog $log) {}

    public function handle(TallyService $tallyService): void
    {
        $maxRetries = (int) \App\Models\SystemSetting::get('tally_max_retries', 3);

        if ($this->log->retry_count >= $maxRetries) {
            Log::warning("Tally sync log #{$this->log->id} exceeded max retries ({$maxRetries}). Abandoning.");
            return;
        }

        $success = $tallyService->sync($this->log);

        if (!$success && $this->log->fresh()->retry_count < $maxRetries) {
            // Exponential back-off: 2^retry_count minutes
            $delay = now()->addMinutes(pow(2, $this->log->retry_count));
            self::dispatch($this->log->fresh())->delay($delay)->onQueue('tally');
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SyncToTallyJob failed for log #{$this->log->id}: " . $exception->getMessage());
        $this->log->update([
            'status'        => 'failed',
            'error_message' => $exception->getMessage(),
            'retry_count'   => $this->log->retry_count + 1,
        ]);
    }
}
