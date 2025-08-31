<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Gtin;
use Illuminate\Console\Command;

class TasnifGtin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasnif:gtin';

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

        $data = [
            ['id' => '000', 'nameEn' => 'USA & Canada'],
            ['id' => '001', 'nameEn' => 'USA & Canada'],
            ['id' => '002', 'nameEn' => 'USA & Canada'],
            // ...
            ['id' => '019', 'nameEn' => 'USA & Canada'],

            ['id' => '030', 'nameEn' => 'USA & Canada (Drugs, Coupons)'],
            ['id' => '031', 'nameEn' => 'USA & Canada (Drugs, Coupons)'],
            // ...
            ['id' => '039', 'nameEn' => 'USA & Canada (Drugs, Coupons)'],

            ['id' => '060', 'nameEn' => 'USA & Canada'],
            // ...
            ['id' => '139', 'nameEn' => 'USA & Canada'],

            ['id' => '300', 'nameEn' => 'France'],
            ['id' => '301', 'nameEn' => 'France'],
            // ...
            ['id' => '379', 'nameEn' => 'France'],

            ['id' => '380', 'nameEn' => 'Bulgaria'],
            ['id' => '383', 'nameEn' => 'Slovenia'],
            ['id' => '385', 'nameEn' => 'Croatia'],
            ['id' => '387', 'nameEn' => 'Bosnia and Herzegovina'],
            // ...

            ['id' => '400', 'nameEn' => 'Germany'],
            ['id' => '401', 'nameEn' => 'Germany'],
            // ...
            ['id' => '440', 'nameEn' => 'Germany'],

            ['id' => '460', 'nameEn' => 'Russia'],
            ['id' => '461', 'nameEn' => 'Russia'],
            // ...
            ['id' => '469', 'nameEn' => 'Russia'],

            ['id' => '470', 'nameEn' => 'Kyrgyzstan'],
            ['id' => '471', 'nameEn' => 'Taiwan'],
            ['id' => '474', 'nameEn' => 'Estonia'],
            ['id' => '475', 'nameEn' => 'Latvia'],
            ['id' => '476', 'nameEn' => 'Azerbaijan'],
            ['id' => '477', 'nameEn' => 'Lithuania'],
            ['id' => '478', 'nameEn' => 'Uzbekistan'],
            ['id' => '479', 'nameEn' => 'Sri Lanka'],
            ['id' => '480', 'nameEn' => 'Philippines'],
            ['id' => '481', 'nameEn' => 'Belarus'],
            ['id' => '482', 'nameEn' => 'Ukraine'],
            ['id' => '483', 'nameEn' => 'Turkmenistan'],
            ['id' => '484', 'nameEn' => 'Moldova'],
            ['id' => '485', 'nameEn' => 'Armenia'],
            ['id' => '486', 'nameEn' => 'Georgia'],
            ['id' => '487', 'nameEn' => 'Kazakhstan'],
            ['id' => '488', 'nameEn' => 'Tajikistan'],
            ['id' => '489', 'nameEn' => 'Hong Kong'],

            ['id' => '690', 'nameEn' => 'China'],
            ['id' => '691', 'nameEn' => 'China'],
            // ...
            ['id' => '699', 'nameEn' => 'China'],

            ['id' => '729', 'nameEn' => 'Israel'],

            ['id' => '750', 'nameEn' => 'Mexico'],

            ['id' => '789', 'nameEn' => 'Brazil'],
            ['id' => '790', 'nameEn' => 'Brazil'],

            ['id' => '800', 'nameEn' => 'Italy'],
            // ...
            ['id' => '839', 'nameEn' => 'Italy'],

            ['id' => '840', 'nameEn' => 'Spain'],
            // ...
            ['id' => '849', 'nameEn' => 'Spain'],

            ['id' => '880', 'nameEn' => 'South Korea'],

            ['id' => '890', 'nameEn' => 'India'],

            ['id' => '930', 'nameEn' => 'Australia'],
            // ...
            ['id' => '939', 'nameEn' => 'Australia'],

            ['id' => '940', 'nameEn' => 'New Zealand'],
            // ...
            ['id' => '949', 'nameEn' => 'New Zealand'],

            ['id' => '955', 'nameEn' => 'Malaysia'],
            ['id' => '958', 'nameEn' => 'Macau'],
        ];

        foreach ($data as $row) {
            Gtin::updateOrCreate(['id' => $row['id']], $row);
        }
    }
}
