<?php

namespace App\Console\Commands;

use App\Models\Product;
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
        $size = 5;
        $currentPage = 2;

        $last = Product::latest()->first();
        $lastDate = $last['updated_at'];
        $this->telegramSend($lastDate . ' : ' . $last['id'] . ' : ' . $last['name']);
        $date = Carbon::now();
        $startOfToday = $lastDate->timestamp * 1000;
        $endOfToday   = $lastDate->addDays(1)->timestamp * 1000;

        //$endOfToday   = Carbon::now()->subDays(1)->endOfDay()->timestamp * 1000;
        //dd($startOfToday, $endOfToday);
        $url = 'https://tasnif.soliq.uz/api/cl-api/integration-mxik/get/history/time?page=' . $currentPage . '&size=' . $size . '&startDate=' . $startOfToday . '&endDate=' . $endOfToday; // your static URL
        try {
            $response = Http::get($url);

            if ($response->successful()) {
                //$this->info($response->body());
                $jsonArr = json_decode($response->body(), true);
                //dd($jsonArr);
                //$newC = Carbon::createFromTimestamp($jsonArr['data'][0]['createdAt'] / 1000)->toDateTimeString();
                //$newU = Carbon::createFromTimestamp($jsonArr['data'][0]['createdAt'] / 1000 + 2)->toDateTimeString();
                foreach ($jsonArr["data"] as $item) {
                    if ($item['status'] != "3") {
                        $this->telegramSend($item['mxik'] . ' - ' . $item['status']);
                    }
                }
                //dd($newC, $newU);
                //$this->telegramSend('Data upserted successfully.' . ($size * ($currentPage + 1)));
            } else {
                // $this->telegramSend("Request failed with status: {$response->status()} : {{$response->body()}}");
            }
        } catch (\Exception $e) {
            $this->info($e->getMessage());
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
}
