<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Gtin;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 999; $i++) {
            //  echo str_pad($i, 3, '0', STR_PAD_LEFT) . "\n";
            $code = str_pad($i, 3, '0', STR_PAD_LEFT);

            Gtin::updateOrCreate(
                ['id' => $code],
                [
                    'id' => $code,
                    'nameUz' => null,
                    'nameRu' => null,
                    'nameEn' => null
                ]
            );
        }
    }
}
