<?php

namespace App\Console\Commands;

use App\Models\ClassCode;
use App\Models\PackageCode;
use App\Models\Setting;
use App\Services\MxikSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class MxikImportCommand extends Command
{
    protected $signature = 'mxik:import';
    protected $description = 'Import MXIK classifier from public/mxik.json (streaming, handles 400MB+)';

    public function handle(MxikSyncService $service): int
    {
        $path = public_path('mxik.json');

        if (! file_exists($path)) {
            $this->error("File not found: {$path}");
            return self::FAILURE;
        }

        $size = round(filesize($path) / 1024 / 1024, 1);
        $this->info("Importing from: {$path} ({$size} MB)");


        $bar = $this->output->createProgressBar();
        $bar->setFormat(' %current% records [%bar%] %elapsed:6s%');
        $bar->start();

        $stats = $service->importFromFile($path, function (int $processed) use ($bar) {
            $bar->setProgress($processed);
        });

        $bar->finish();
        $this->newLine();

        if (! $stats['success']) {
            $this->error('Import failed: ' . ($stats['error'] ?? 'unknown error'));
            return self::FAILURE;
        }

        $maxCreatedAt = ClassCode::max('created_at');
        if ($maxCreatedAt) {
            Setting::set('last_sync_at', (string) Carbon::parse($maxCreatedAt)->timestamp);
        }

        $this->info("Imported {$stats['processed']} records successfully.");
        return self::SUCCESS;
    }
}
