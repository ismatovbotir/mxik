<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Product; // Assuming you have a Product model

use App\Models\Group; // Assuming you have a Group model
use App\Models\GroupName; // Assuming you have a GroupName model
use App\Models\Record;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;

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
        $page = Record::first();
        if ($page == null) {
            Record::create(['page' => 0]);
            $currentPage = 0;
        } else {
            $currentPage = $page->page + 1;
        };
        //dd($currentPage);
        $this->info('Starting TasnifCode command...');
        $url = 'https://tasnif.soliq.uz/api/cl-api/integration-mxik/get/all/history/time?page=' . $currentPage . '&size=100'; // your static URL

        $response = Http::get($url);

        if ($response->successful()) {
            //$this->info($response->body());
            $jsonArr = json_decode($response->body(), true);

            foreach ($jsonArr["data"] as $item) {
                //dd($item);
                $item['createdAt'] = Carbon::parse($item['createdAt'])->toDateTimeString();
                $item['updateAt'] = $item['updateAt'] ? Carbon::parse($item['updateAt'])->toDateTimeString() : $item['createdAt'];
                Product::updateOrCreate(
                    ['id' => $item['mxik']],
                    [
                        'id' => $item['mxik'],
                        'status' => 0,
                        'product_id' => null,
                        'mxikNameUz' => $item['mxikNameUz'],
                        'mxikNameRu' => $item['mxikNameRu'],
                        'mxikNameLat' => $item['mxikNameLat'],
                        'label' => 0,
                        'gtin' => $item['internationalCode'],
                        'updated_at' => $item['updateAt'],
                        'created_at' => $item['createdAt'],
                    ],
                );
                $unitArray = [];
                // dd($item);
                foreach ($item['packages'] as $unit) {
                    $unitArray[] = [
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
            $page->update(['page' => $currentPage]);
            $this->info('Data upserted successfully.');
        } else {
            $this->error("Request failed with status: {$response->status()}");
        }
    }
}
