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
    protected $signature = 'tasnif:code';

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
        $size = 1000;
        $page = Record::first();
        if ($page == null) {
            $page = Record::create(['page' => 0, 'total' => 0, 'size' => $size, 'record_total' => 0]);
            $currentPage = 0;
        } else {
            //dd($page);
            if ($page->total <= $page->record_total) {
                $this->info('All records have been processed. Exiting command.');
                return;
            }
            $currentPage = $page->page + 1;
            $size = $page->size;
        };
        //dd($currentPage);
        $this->info('Starting TasnifCode command...');
        $this->telegramSend('Starting TasnifCode command...');
        $url = 'https://tasnif.soliq.uz/api/cl-api/integration-mxik/get/all/history/time?page=' . $currentPage . '&size=' . $size; // your static URL
        try {
            $response = Http::get($url);

            if ($response->successful()) {
                $jsonArr = json_decode($response->body(), true);

                $this->telegramSend("Recourds: " .  count($jsonArr['data']));

                foreach ($jsonArr["data"] as $item) {
                    //dd($item);
                    $temp = $item['createdAt'];
                    $item['createdAt'] = $item['createdAt'] == '01.01.1970 00:00:00' ? Null : Carbon::parse($item['createdAt'])->addHours(6)->toDateTimeString();
                    $item['updateAt'] = $item['updateAt'] ? Carbon::parse($item['updateAt'])->toDateTimeString() : $item['createdAt'];
                    $group = (int)substr($item['mxik'], 0, 3);
                    try {
                        Product::updateOrCreate(
                            ['id' => $item['mxik']],
                            [
                                'id' => $item['mxik'],
                                'group_id' => $group,
                                'status' => 0,
                                'product_id' => null,
                                'name' => $item['mxikNameUz'],
                                //'mxikNameRu' => $item['mxikNameRu'],
                                //'mxikNameLat' => $item['mxikNameLat'],
                                'label' => $item['label'],
                                'gtin' => $item['internationalCode'],
                                'updated_at' => $item['updateAt'],
                                'created_at' => $item['createdAt'],
                            ],
                        );
                    } catch (\Exception $e) {
                        $this->telegramSend($e->getMessage());
                        $this->info($e->getMessage());
                    }

                    $unitArray = [];
                    // dd($item);
                    foreach ($item['packages'] as $unit) {
                        $unitArray[] = [
                            'id' => $unit['code'],
                            'product_id' => $unit['mxikCode'],
                            'name' => $unit['nameUz'],
                            //'nameRu' => $unit['nameRu'],
                            //'nameLat' => $unit['nameLat'],
                            'packageType' => $unit['packageType'],
                        ];
                    }
                    Unit::upsert(
                        $unitArray,
                        ['id'], // Unique key to avoid duplicates
                        ['name'] // Fields to update if the record exists
                    );
                };
                $page->update(
                    [
                        'page' => $currentPage,
                        'total' => $jsonArr['recordTotal'],
                        'record_total' => count($jsonArr["data"]) + $page->record_total
                    ]
                );
                $this->telegramSend('Data upserted successfully : ' . $page->record_total);
            } else {
                $this->telegramSend("Request failed with status: {$response->status()} : {{$response->body()}}");
            }
        } catch (\Exception $e) {
            $this->telegramSend($e->getMessage());
            $this->info($e->getMessage());
        }
    }
    public function telegramSend($text = "hi")
    {
        Http::post('https://api.telegram.org/bot5246861020:AAHrvj1A_AJh7tesQxoos8dZtwUv06SnMkw/sendMessage', [
            'chat_id' => 1936361,
            'text' => $text,
        ]);
    }
}
