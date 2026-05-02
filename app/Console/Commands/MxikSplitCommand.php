<?php

namespace App\Console\Commands;

use App\Services\MxikSyncService;
use Illuminate\Console\Command;

class MxikSplitCommand extends Command
{
    protected $signature = 'mxik:split';
    protected $description = 'Split public/mxik.json into batch files of 500 records in public/json/';

    public function handle(MxikSyncService $service): int
    {
        $source = public_path('mxik.json');
        $outputDir = public_path('json');

        if (! file_exists($source)) {
            $this->error("File not found: {$source}");
            return self::FAILURE;
        }

        $size = round(filesize($source) / 1024 / 1024, 1);
        $this->info("Splitting {$source} ({$size} MB) → {$outputDir}/batch_*.json");

        $bar = $this->output->createProgressBar();
        $bar->setFormat(' %current% records, %message% files written [%bar%] %elapsed:6s%');
        $bar->start();

        $stats = $service->splitToFiles($source, $outputDir, 10000, function (int $filesWritten, int $processed) use ($bar) {
            $bar->setMessage((string) $filesWritten);
            $bar->setProgress($processed);
        });

        $bar->finish();
        $this->newLine();

        if (! $stats['success']) {
            $this->error('Split failed: ' . ($stats['error'] ?? 'unknown error'));
            return self::FAILURE;
        }

        $this->info("Written {$stats['files']} batch files ({$stats['processed']} records total) to {$outputDir}/");
        return self::SUCCESS;
    }
}
