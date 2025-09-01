<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Group; // Assuming you have a Group model
use App\Models\GroupName; // Assuming you have a GroupName model
class TasnifGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasnif:group';

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
        $url = 'https://tasnif.soliq.uz/api/cl-api/integration-mxik/references/group/list'; // your static URL

        $response = Http::get($url);

        if ($response->successful()) {
            //$arr =  $response->json();
            $jsonArr = json_decode($response->body(), true);
            foreach ($jsonArr["data"] as $item) {
                //$this->info(json_encode($item));
                $prefix = str_pad((string) $item['groupCode'], 3, '0', STR_PAD_LEFT);

                Group::upsert(
                    ['id' => $item['groupCode'], 'prefix' => $prefix, 'name' => $item['nameUZ']],
                    ['id'], // Unique key to avoid duplicates
                    ['name', 'prefix'] // Fields to update if the record exists
                );
                GroupName::upsert(
                    ['group_id' => $item['groupCode'], 'name' => $item['nameUZ'], 'lang' => 'uz'],
                    ['group_id', 'lang'], // Unique key to avoid duplicates
                    ['name'] // Fields to update if the record exists
                );
                GroupName::upsert(
                    ['group_id' => $item['groupCode'], 'name' => $item['nameRU'], 'lang' => 'ru'],
                    ['group_id', 'lang'], // Unique key to avoid duplicates
                    ['name'] // Fields to update if the record exists
                );
                GroupName::upsert(
                    ['group_id' => $item['groupCode'], 'name' => $item['nameLAT'], 'lang' => 'lat'],
                    ['group_id', 'lang'], // Unique key to avoid duplicates
                    ['name'] // Fields to update if the record exists
                );
            };
            $this->info('Data upserted successfully.');
        } else {
            $this->error("Request failed with status: {$response->status()}");
        }
    }
}
