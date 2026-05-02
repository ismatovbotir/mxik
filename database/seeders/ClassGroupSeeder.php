<?php

namespace Database\Seeders;

use App\Services\MxikSyncService;
use Illuminate\Database\Seeder;

class ClassGroupSeeder extends Seeder
{
    public function run(MxikSyncService $service): void
    {
        $this->command->info('Fetching class groups from API...');

        $result = $service->syncGroups();

        if (! $result['success']) {
            $this->command->error('Failed to fetch class groups.');
            return;
        }

        $this->command->info("Seeded {$result['processed']} class groups.");
    }
}
