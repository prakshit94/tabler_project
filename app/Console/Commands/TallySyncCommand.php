<?php
namespace App\Console\Commands;

use App\Services\TallyService;
use Illuminate\Console\Command;

class TallySyncCommand extends Command
{
    protected $signature   = 'tally:sync {--retry : Also retry failed syncs}';
    protected $description = 'Sync all pending Tally transactions to Tally ERP';

    public function handle(TallyService $tallyService): int
    {
        $this->info('Starting Tally sync...');

        $synced = $tallyService->syncAllPending();
        $this->info("Synced {$synced} pending transaction(s).");

        if ($this->option('retry')) {
            $queued = $tallyService->retryFailed();
            $this->info("Queued {$queued} failed transaction(s) for retry.");
        }

        $this->info('Tally sync complete.');
        return Command::SUCCESS;
    }
}
