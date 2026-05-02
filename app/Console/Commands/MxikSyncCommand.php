<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Services\DashboardService;
use App\Services\MxikSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class MxikSyncCommand extends Command
{
    protected $signature = 'mxik:sync';
    protected $description = 'Sync MXIK classifier from external source';

    public function handle(MxikSyncService $syncService, DashboardService $dashboardService): int
    {
        $this->info('Syncing groups...');

        $bar = null;
        $lastSync  = Setting::get('last_sync_at');
        $this->info('Last sync: ' . ($lastSync ? Carbon::createFromTimestamp((int) $lastSync)->format('Y-m-d H:i:s') : 'Never'));
        $stats = $syncService->sync(function (int $page, int $totalPages, int $processed, int $total) use (&$bar) {
            if ($bar === null) {
                $bar = $this->output->createProgressBar($total);
                $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% — page %message%');
                $bar->setMessage("{$page}/{$totalPages}");
                $bar->start();
            }

            $bar->setMessage("{$page}/{$totalPages}");
            $bar->setProgress($processed);
        });

        if ($bar) {
            $bar->finish();
            $this->newLine();
        }

        $groupStatus = $stats['groups']['success'] ? 'ok' : 'failed';

        $this->info("Groups:  {$stats['groups']['processed']} records ({$groupStatus})");
        $this->info("History: {$stats['processed']}/{$stats['total']} records in {$stats['pages']} page(s)");

        if (! $stats['success']) {
            $this->error('Sync completed with errors. Check logs.');
            return self::FAILURE;
        }

        Cache::forget('dashboard_stats');
        $dashboardService->stats();
        $this->info('Dashboard cache refreshed.');

        return self::SUCCESS;
    }
}
