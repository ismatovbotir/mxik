<?php

namespace App\Console\Commands;

use App\Models\ClassCode;
use App\Models\Setting;
use App\Services\MxikSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class MxikImportBatchCommand extends Command
{
    protected $signature = 'mxik:import-batch
                            {--dir= : Override the scan directory (default: public/json)}
                            {--continue : Skip files already imported (tracks progress in settings)}';

    protected $description = 'Scan public/json folder and import every .json file into the database';

    public function handle(MxikSyncService $service): int
    {
        $dir = $this->option('dir') ?? public_path('json');

        if (! is_dir($dir)) {
            $this->error("Directory not found: {$dir}");
            return self::FAILURE;
        }

        $files = glob(rtrim($dir, '/') . '/*.json');

        if (empty($files)) {
            $this->warn("No .json files found in: {$dir}");
            return self::SUCCESS;
        }

        sort($files);

        $lastImported = $this->option('continue') ? (Setting::get('last_batch_file') ?? '') : '';

        $pending = $lastImported
            ? array_filter($files, fn($f) => basename($f) > basename($lastImported))
            : $files;

        $pending = array_values($pending);
        $total   = count($pending);

        if ($total === 0) {
            $this->info('All files already imported. Use without --continue to re-import.');
            return self::SUCCESS;
        }

        $this->info(sprintf('Found %d file(s) to import in: %s', $total, $dir));

        $grandTotal = 0;
        $failed     = 0;

        foreach ($pending as $index => $path) {
            $filename = basename($path);
            $size     = round(filesize($path) / 1024 / 1024, 2);
            $label    = sprintf('[%d/%d] %s (%.2f MB)', $index + 1, $total, $filename, $size);

            $this->line($label);

            $bar = $this->output->createProgressBar();
            $bar->setFormat(' %current% records [%bar%] %elapsed:6s%');
            $bar->start();

            $stats = $service->importFromFile($path, function (int $processed) use ($bar) {
                $bar->setProgress($processed);
            });

            $bar->finish();
            $this->newLine();

            if (! $stats['success']) {
                $this->error('  Failed: ' . ($stats['error'] ?? 'unknown error'));
                $failed++;
                continue;
            }

            $grandTotal += $stats['processed'];
            unlink($path);
            $this->line(sprintf('  Imported %d records. File deleted.', $stats['processed']));

            if ($this->option('continue')) {
                Setting::set('last_batch_file', $filename);
            }
        }

        $maxCreatedAt = ClassCode::max('created_at');
        if ($maxCreatedAt) {
            Setting::set('last_sync_at', (string) Carbon::parse($maxCreatedAt)->timestamp);
        }

        $this->newLine();
        $this->info(sprintf(
            'Done. Imported %d total records from %d file(s). Failed: %d.',
            $grandTotal,
            $total - $failed,
            $failed
        ));

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
