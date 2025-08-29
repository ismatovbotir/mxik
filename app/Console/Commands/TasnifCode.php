<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Product; // Assuming you have a Product model

use App\Models\Group; // Assuming you have a Group model
use App\Models\GroupName; // Assuming you have a GroupName model
use App\Models\Unit;

class TasnifCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasnif-code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting TasnifCode command...');
        $url = 'https://tasnif.soliq.uz/api/cl-api/integration-mxik/get/all/history/time?page=0&size=100'; // your static URL

        $response = Http::get($url);

        if ($response->successful()) {
            //$this->info($response->body());
            $jsonArr = json_decode($response->body(), true);

            foreach ($jsonArr["data"] as $item) {
                Product::upsert(
                    [
                        'id' => $item['mxik'],
                        'status' => 0,
                        'product_id' => null,
                        'mxikNameUz' => $item['mxikNameUz'],
                        'mxikNameRu' => $item['mxikNameRu'],
                        'mxikNameLat' => $item['mxikNameLat'],
                        'label' => 0,
                        'gtin' => $item['internationalCode'],
                        'updated_at' => $item['updatedAt'] ?? $item['createdAt'],
                        'created_at' => $item['createdAt'],
                    ],
                    ['id'], // Unique key to avoid duplicates
                    ['status', 'product_id', 'mxikNameUz', 'mxikNameRu', 'mxikNameLat', 'label', 'gtin', 'created_at', 'updated_at'] // Fields to update if the record exists
                );
                $unitArray = [];
                // dd($item);
                foreach ($item['packages'] as $unit) {
                    $unitAray[] = [
                        'id' => $unit['code'],
                        'product_id' => $unit['mxikCode'],
                        'nameUz' => $unit['nameUz'],
                        'nameRu' => $unit['nameRu'],
                        'nameLat' => $unit['nameLat'],
                        'packageType' => $unit['packageType'],
                    ];
                }
                Unit::upsert(
                    $unitArray,
                    ['id'], // Unique key to avoid duplicates
                    ['nameUz', 'nameRu', 'nameLat'] // Fields to update if the record exists
                );
            };

            $this->info('Data upserted successfully.');
        } else {
            $this->error("Request failed with status: {$response->status()}");
        }
    }
}
