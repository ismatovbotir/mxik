<?php

namespace App\Console\Commands;

use App\Services\DashboardService;
use Illuminate\Console\Command;

class CacheWarmCommand extends Command
{
    protected $signature = 'cache:warm';
    protected $description = 'Pre-build dashboard statistics cache';

    public function handle(DashboardService $service): int
    {
        $this->info('Building dashboard cache...');

        $start = microtime(true);
        $service->stats();
        $elapsed = round((microtime(true) - $start) * 1000);

        $this->info("Done in {$elapsed}ms. Cache valid until next mxik:sync.");

        return self::SUCCESS;
    }
}
