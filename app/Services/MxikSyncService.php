<?php

namespace App\Services;

use App\Models\ClassCode;
use App\Models\ClassGroup;
use App\Models\PackageCode;
use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use JsonMachine\Items;
use JsonMachine\JsonDecoder\ExtJsonDecoder;

class MxikSyncService
{
    private const HISTORY_URL = 'https://tasnif.soliq.uz/api/cl-api/integration-mxik/get/history/time';
    private const GROUPS_URL  = 'https://tasnif.soliq.uz/api/cl-api/integration-mxik/references/group/list';
    private const PAGE_SIZE   = 1000;

    /**
     * @param callable|null $onProgress fn(int $page, int $totalPages, int $processed, int $total)
     */
    public function sync(?callable $onProgress = null): array
    {
        $groupsStats = $this->syncGroups();

        // Даты в БД хранятся в секундах, API принимает миллисекунды
        $lastSync  = Setting::get('last_sync_at');

        $startTimestamp = $lastSync ? (int) $lastSync : now()->subDays(6)->timestamp;
        $startDate = $startTimestamp * 1000;
        $endDate   = ($startTimestamp + 6 * 86400) * 1000;

        $stats = [
            'groups'      => $groupsStats,
            'total'       => 0,
            'processed'   => 0,
            'pages'       => 0,
            'success'     => true,
            'last_sync_at' => $lastSync ? Carbon::createFromTimestamp((int) $lastSync)->format('Y-m-d H:i:s') : null,
        ];

        // Шаг 1: узнаём общее количество записей
        $probe = Http::timeout(30)->get(self::HISTORY_URL, [
            'page'      => 0,
            'size'      => 1,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ]);

        if (! $probe->successful() || ! $probe->json('success')) {
            Log::error('MXIK history probe failed', ['body' => $probe->body()]);
            $stats['success'] = false;
            return $stats;
        }

        $recordTotal      = (int) $probe->json('recordTotal', 0);
        $stats['total']   = $recordTotal;
        $totalPages       = $recordTotal > 0 ? (int) ceil($recordTotal / self::PAGE_SIZE) : 0;

        // Шаг 2: итерация по страницам
        for ($page = 0; $page < $totalPages; $page++) {
            $response = Http::timeout(30)->get(self::HISTORY_URL, [
                'page'      => $page,
                'size'      => self::PAGE_SIZE,
                'startDate' => $startDate,
                'endDate'   => $endDate,
            ]);

            if (! $response->successful() || ! $response->json('success')) {
                Log::error('MXIK history sync failed', [
                    'page'   => $page,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                $stats['success'] = false;
                break;
            }

            $data = $response->json('data', []);
            $this->saveItems($data);

            $stats['processed'] += count($data);
            $stats['pages']++;

            if ($onProgress) {
                $onProgress($page + 1, $totalPages, $stats['processed'], $recordTotal);
            }
        }

        if ($stats['success']) {
            // Сохраняем в секундах
            Setting::set('last_sync_at', (string) ($endDate / 1000));
        }

        return $stats;
    }

    public function syncGroups(): array
    {
        $response = Http::timeout(30)->get(self::GROUPS_URL);

        if (! $response->successful() || ! $response->json('success')) {
            Log::error('MXIK groups sync failed', ['body' => $response->body()]);
            return ['processed' => 0, 'success' => false];
        }

        $groups = array_map(fn($g) => [
            'id'         => $g['groupCode'],
            'name_uz'    => $g['nameUZ'] ?? '',
            'name_ru'    => $g['nameRU'] ?? '',
            'name_lat'   => $g['nameLAT'] ?? '',
            'updated_at' => now(),
            'created_at' => now(),
        ], $response->json('data', []));

        if (! empty($groups)) {
            ClassGroup::upsert($groups, ['id'], ['name_uz', 'name_ru', 'name_lat', 'updated_at']);
        }

        return ['processed' => count($groups), 'success' => true];
    }

    /**
     * Split a large JSON file into batch files of $batchSize records each.
     * @param callable|null $onProgress fn(int $filesWritten, int $processed)
     */
    public function splitToFiles(string $sourcePath, string $outputDir, int $batchSize = 5, ?callable $onProgress = null): array
    {
        if (! file_exists($sourcePath)) {
            return ['files' => 0, 'processed' => 0, 'success' => false, 'error' => 'Source file not found'];
        }

        if (! is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $stats = ['files' => 0, 'processed' => 0, 'success' => true];
        $batch = [];
        $drop = ['mxikNameRu', 'mxikNameLat', 'nameRu', 'nameLat'];

        try {
            $items = Items::fromFile($sourcePath, ['decoder' => new ExtJsonDecoder(true)]);

            foreach ($items as $item) {
                $item = array_diff_key((array) $item, array_flip($drop));

                if (isset($item['createdAt']) && $item['createdAt'] < 0) {
                    $item['createdAt'] = 0;
                }

                if (! empty($item['packages'])) {
                    $item['packages'] = array_map(
                        fn($pkg) => array_diff_key((array) $pkg, array_flip($drop)),
                        (array) $item['packages']
                    );
                }

                $batch[] = $item;

                if (count($batch) >= $batchSize) {
                    $this->writeBatchFile($outputDir, $stats['files'] + 1, $batch);
                    $stats['processed'] += \count($batch);
                    $stats['files']++;
                    $batch = [];

                    if ($onProgress) {
                        $onProgress($stats['files'], $stats['processed']);
                    }
                }
            }

            if (! empty($batch)) {
                $this->writeBatchFile($outputDir, $stats['files'] + 1, $batch);
                $stats['processed'] += \count($batch);
                $stats['files']++;

                if ($onProgress) {
                    $onProgress($stats['files'], $stats['processed']);
                }
            }
        } catch (\Throwable $e) {
            Log::error('MXIK split to files failed', ['error' => $e->getMessage()]);
            $stats['success'] = false;
            $stats['error'] = $e->getMessage();
        }

        return $stats;
    }

    private function writeBatchFile(string $dir, int $index, array $batch): void
    {
        $filename = \sprintf('%s/batch_%05d.json', rtrim($dir, '/'), $index);
        file_put_contents($filename, json_encode(array_values($batch), JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param callable|null $onProgress fn(int $processed)
     */
    public function importFromFile(string $path, ?callable $onProgress = null): array
    {
        if (! file_exists($path)) {
            return ['processed' => 0, 'success' => false, 'error' => 'File not found'];
        }

        $stats = ['processed' => 0, 'success' => true];
        $batch = [];
        $batchSize = 500;

        try {
            $items = Items::fromFile($path, ['decoder' => new ExtJsonDecoder(true)]);

            foreach ($items as $item) {
                $batch[] = $item;

                if (count($batch) >= $batchSize) {

                    $this->saveFileItems($batch);
                    $stats['processed'] += count($batch);
                    $batch = [];

                    if ($onProgress) {
                        $onProgress($stats['processed']);
                    }
                }
            }

            if (! empty($batch)) {
                $this->saveFileItems($batch);
                $stats['processed'] += count($batch);

                if ($onProgress) {
                    $onProgress($stats['processed']);
                }
            }
        } catch (\Throwable $e) {
            Log::error('MXIK file import failed', ['error' => $e->getMessage()]);
            $stats['success'] = false;
            $stats['error'] = $e->getMessage();
        }

        return $stats;
    }

    private function saveFileItems(array $items): void
    {
        $classCodes   = [];
        $packageCodes = [];

        foreach ($items as $item) {
            $item = (array) $item;

            $createdAt = isset($item['createdAt']) && $item['createdAt']
                ? Carbon::createFromTimestamp((int) ($item['createdAt'] / 1000))
                : now();
            $updatedAt = isset($item['updateAt']) && $item['updateAt']
                ? Carbon::createFromTimestamp((int) ($item['updateAt'] / 1000))
                : $createdAt;

            $mxik = $item['mxik'];

            $classCodes[] = [
                'id'             => $mxik,
                'class_group_id' => (int) substr($mxik, 0, 3),

                'status'         => (string) ($item['status'] ?? '3'),
                'name'           => $item['mxikNameUz'] ?? $item['name'] ?? null,
                'gtin'           => $item['internationalCode'] ?? null,
                'label'          => (bool) ($item['label'] ?? false),
                'labelForCheck'  => (bool) ($item['labelForCheck'] ?? false),
                'usePackage'     => (bool) ($item['usePackage'] ?? false),
                'cashSale'       => true,
                'created_at'     => $createdAt,
                'updated_at'     => $updatedAt,
            ];

            foreach ($item['packages'] ?? [] as $pkg) {
                $pkg = (array) $pkg;
                $packageCodes[] = [
                    'id'            => $pkg['code'],
                    'class_code_id' => $mxik,
                    'name'          => $pkg['nameUz'] ?? null,
                    'package_type'  => $pkg['packageType'] ?? null,
                ];
            }
        }

        if (! empty($classCodes)) {
            ClassCode::upsert(
                $classCodes,
                ['id'],
                ['class_group_id', 'new_mxik_code', 'status', 'name', 'gtin', 'label', 'labelForCheck', 'usePackage', 'cashSale', 'updated_at']
            );
        }

        if (! empty($packageCodes)) {
            PackageCode::upsert(
                $packageCodes,
                ['id'],
                ['class_code_id', 'name', 'package_type']
            );
        }
    }

    private function saveItems(array $items): void
    {
        $classCodes   = [];
        $packageCodes = [];

        foreach ($items as $item) {
            $createdAt = isset($item['createdAt']) && $item['createdAt']
                ? Carbon::createFromTimestamp((int) ($item['createdAt'] / 1000))
                : now();
            $updatedAt = isset($item['updateAt']) && $item['updateAt']
                ? Carbon::createFromTimestamp((int) ($item['updateAt'] / 1000))
                : $createdAt;

            $classCodes[] = [
                'id'             => $item['mxik'],
                'class_group_id' => (int) substr($item['mxik'], 0, 3),
                'new_mxik_code'  => $item['newMxikCOde'] ?? null,
                'status'         => (string) ($item['status'] ?? '3'),
                'name'           => $item['name'],
                'gtin'           => $item['internationalCode'] ?? null,
                'label'          => (bool) ($item['label'] ?? false),
                'labelForCheck'  => (bool) ($item['labelForCheck'] ?? false),
                'usePackage'     => (bool) ($item['usePackage'] ?? false),
                'cashSale'       => true,
                'created_at'     => $createdAt,
                'updated_at'     => $updatedAt,
            ];

            foreach ($item['packages'] ?? [] as $pkg) {
                $packageCodes[] = [
                    'id'            => $pkg['code'],
                    'class_code_id' => $item['mxik'],
                    'name'       => $pkg['nameUz'] ?? null,

                    'package_type'  => $pkg['packageType'] ?? null,
                ];
            }
        }

        if (! empty($classCodes)) {
            ClassCode::upsert(
                $classCodes,
                ['id'],
                ['class_group_id', 'new_mxik_code', 'status', 'name', 'gtin', 'label', 'labelForCheck', 'usePackage', 'cashSale', 'updated_at']
            );
        }

        if (! empty($packageCodes)) {
            PackageCode::upsert(
                $packageCodes,
                ['id'],
                ['class_code_id', 'name', 'package_type']
            );
        }
    }
}
