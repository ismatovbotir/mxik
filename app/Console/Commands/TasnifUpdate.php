<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TasnifUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasnif:update';

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
        $size = 10;




        $lastDate = Carbon::yesterday()->startOfDay();
        //$endOfToday = Carbon::yesterday()->endOfDay();
        //$this->info($lastDate . ' : ' . $last['id'] . ' : ' . $last['name']);
        //$date = Carbon::now();
        $startOfToday = $lastDate->timestamp * 1000;
        $endOfToday   = $lastDate->endOfDay()->timestamp * 1000;

        $this->telegramSend(Carbon::createFromTimestamp($startOfToday / 1000) . ' - ' . Carbon::createFromTimestamp($endOfToday / 1000));
        //dd('done');
        $totalRecords = $this->checkUpdates(100, 0, $startOfToday, $endOfToday);
        //dd($totalRecords);

        $totalRecord = $totalRecords['data']['recordTotal'];
        //$this->telegramSend($totalRecord);
        //$this->info('Total records to process: ' . ceil($totalRecord  / $size));
        for ($i = 0; $i < ceil($totalRecord / $size); $i++) {
            //$this->info('Processing page ' . ($i) . ' of ' . floor($totalRecord / $size));
            $newRecords = $this->checkUpdates($size, $i, $startOfToday, $endOfToday);
            if ($newRecords['status'] == 'error') {
                $this->telegramSend($newRecords['message']);
                continue;
            }
            $data = $newRecords['data']['data'];
            foreach ($data as $item) {
                //$this->createItem($item);
                if ($item['status'] == '3') {
                    $this->telegramSend($item['mxik'] . ' - ' . $item['name']);
                }
            }
            //sleep(1);
        }



        //$url = "integration-mxik/get/history/time123123";
    }
    public function telegramSend($text = "hi")
    {
        Http::post('https://api.telegram.org/bot5246861020:AAHrvj1A_AJh7tesQxoos8dZtwUv06SnMkw/sendMessage', [
            'chat_id' => 1936361,
            'text' => $text,
        ]);
    }
    public function checkUpdates($size = 1, $currentPage = 0, $startOfToday, $endOfToday)
    {
        $url = 'https://tasnif.soliq.uz/api/cl-api/integration-mxik/get/history/time?page=' . $currentPage . '&size=' . $size . '&startDate=' . $startOfToday . '&endDate=' . $endOfToday; // your static URL
        try {
            $response = Http::get($url);

            if ($response->successful()) {
                //$this->info($response->body());
                $jsonArr = json_decode($response->body(), true);
                return [
                    'status' => 'success',
                    'data' => $jsonArr,
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => "Request failed with status: {$response->status()} : {{$response->body()}}",
                ];
            }
        } catch (\Exception $e) {
            $res = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
        return $res;
    }
    public function createItem($item)
    {
        if ($item['status'] == '3') {
            $createdAt  = Carbon::createFromTimestamp($item['createdAt'] / 1000);
            $updatedAt = Carbon::createFromTimestamp(($item['createdAt'] / 1000) + 2); //$this->info($createdAt);
            // $this->telegramSend($createdAt, $updatedAt);
            $group = (int)substr($item['mxik'], 0, 3);
            try {
                $gtin_id = Null;
                $gtin = strlen($item['internationalCode']) > 14 ? Null : $item['internationalCode'];
                if ($gtin !== Null) {
                    $gtin_id = substr(trim($gtin), 0, 3);
                    //$this->info($gtin_id);
                }
                Product::updateOrCreate(
                    ['id' => $item['mxik']],
                    [
                        'id' => $item['mxik'],
                        'group_id' => $group,
                        'status' => 3,
                        'product_id' => null,
                        'name' => $item['name'],
                        //'mxikNameRu' => $item['mxikNameRu'],
                        //'mxikNameLat' => $item['mxikNameLat'],
                        'label' => $item['label'],
                        'gtin' => $gtin,
                        'gtin_id' => $gtin_id,
                        'updated_at' => $updatedAt,
                        'created_at' => $createdAt,
                    ],
                );
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
                    ['name', 'product_id', 'packageType'] // Fields to update if the record exists
                );
                //$this->info('Product ' . $item['mxik'] . ' created');
            } catch (\Exception $e) {
                $this->telegramSend($e->getMessage() . ' ' . strlen($gtin));
                $this->info($e->getMessage());
            }
        }
    }
}
