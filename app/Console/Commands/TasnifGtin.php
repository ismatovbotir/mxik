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
            ['id' => '302', 'nameEn' => 'France'],
            ['id' => '303', 'nameEn' => 'France'],
            ['id' => '304', 'nameEn' => 'France'],
            ['id' => '305', 'nameEn' => 'France'],
            ['id' => '306', 'nameEn' => 'France'],
            ['id' => '307', 'nameEn' => 'France'],
            ['id' => '308', 'nameEn' => 'France'],
            ['id' => '309', 'nameEn' => 'France'],
            ['id' => '310', 'nameEn' => 'France'],
            ['id' => '311', 'nameEn' => 'France'],
            ['id' => '312', 'nameEn' => 'France'],
            ['id' => '313', 'nameEn' => 'France'],
            ['id' => '314', 'nameEn' => 'France'],
            ['id' => '315', 'nameEn' => 'France'],
            ['id' => '316', 'nameEn' => 'France'],
            ['id' => '317', 'nameEn' => 'France'],
            ['id' => '318', 'nameEn' => 'France'],
            ['id' => '319', 'nameEn' => 'France'],

            ['id' => '320', 'nameEn' => 'France and Monaco'],
            ['id' => '321', 'nameEn' => 'France and Monaco'],
            ['id' => '322', 'nameEn' => 'France and Monaco'],
            ['id' => '323', 'nameEn' => 'France and Monaco'],
            ['id' => '324', 'nameEn' => 'France and Monaco'],
            ['id' => '325', 'nameEn' => 'France and Monaco'],
            ['id' => '326', 'nameEn' => 'France and Monaco'],
            ['id' => '327', 'nameEn' => 'France and Monaco'],
            ['id' => '328', 'nameEn' => 'France and Monaco'],
            ['id' => '329', 'nameEn' => 'France and Monaco'],
            ['id' => '330', 'nameEn' => 'France and Monaco'],
            ['id' => '331', 'nameEn' => 'France and Monaco'],
            ['id' => '332', 'nameEn' => 'France and Monaco'],
            ['id' => '333', 'nameEn' => 'France and Monaco'],
            ['id' => '334', 'nameEn' => 'France and Monaco'],
            ['id' => '335', 'nameEn' => 'France and Monaco'],
            ['id' => '336', 'nameEn' => 'France and Monaco'],
            ['id' => '337', 'nameEn' => 'France and Monaco'],
            ['id' => '338', 'nameEn' => 'France and Monaco'],
            ['id' => '339', 'nameEn' => 'France and Monaco'],

            ['id' => '340', 'nameEn' => 'Belgium & Luxembourg'],
            ['id' => '341', 'nameEn' => 'Belgium & Luxembourg'],
            ['id' => '342', 'nameEn' => 'Belgium & Luxembourg'],
            ['id' => '343', 'nameEn' => 'Belgium & Luxembourg'],
            ['id' => '344', 'nameEn' => 'Belgium & Luxembourg'],
            ['id' => '345', 'nameEn' => 'Belgium & Luxembourg'],
            ['id' => '346', 'nameEn' => 'Belgium & Luxembourg'],
            ['id' => '347', 'nameEn' => 'Belgium & Luxembourg'],
            ['id' => '348', 'nameEn' => 'Belgium & Luxembourg'],
            ['id' => '349', 'nameEn' => 'Belgium & Luxembourg'],

            ['id' => '350', 'nameEn' => 'Spain'],
            ['id' => '351', 'nameEn' => 'Spain'],
            ['id' => '352', 'nameEn' => 'Spain'],
            ['id' => '353', 'nameEn' => 'Spain'],
            ['id' => '354', 'nameEn' => 'Spain'],
            ['id' => '355', 'nameEn' => 'Spain'],
            ['id' => '356', 'nameEn' => 'Spain'],
            ['id' => '357', 'nameEn' => 'Spain'],
            ['id' => '358', 'nameEn' => 'Spain'],
            ['id' => '359', 'nameEn' => 'Spain'],

            ['id' => '360', 'nameEn' => 'Portugal'],
            ['id' => '361', 'nameEn' => 'Portugal'],
            ['id' => '362', 'nameEn' => 'Portugal'],
            ['id' => '363', 'nameEn' => 'Portugal'],
            ['id' => '364', 'nameEn' => 'Portugal'],
            ['id' => '365', 'nameEn' => 'Portugal'],
            ['id' => '366', 'nameEn' => 'Portugal'],
            ['id' => '367', 'nameEn' => 'Portugal'],
            ['id' => '368', 'nameEn' => 'Portugal'],
            ['id' => '369', 'nameEn' => 'Portugal'],

            ['id' => '370', 'nameEn' => 'Greece'],
            ['id' => '371', 'nameEn' => 'Greece'],
            ['id' => '372', 'nameEn' => 'Greece'],
            ['id' => '373', 'nameEn' => 'Greece'],
            ['id' => '374', 'nameEn' => 'Greece'],
            ['id' => '375', 'nameEn' => null],
            ['id' => '376', 'nameEn' => 'Bulgaria'],
            // ...
            ['id' => '379', 'nameEn' => 'France'],

            ['id' => '380', 'nameEn' => 'Bulgaria'],
            ['id' => '383', 'nameEn' => 'Slovenia'],
            ['id' => '385', 'nameEn' => 'Croatia'],
            ['id' => '387', 'nameEn' => 'Bosnia and Herzegovina'],
            // ...

            ['id' => '400', 'nameEn' => 'Germany'],
            ['id' => '401', 'nameEn' => 'Germany'],
            ['id' => '402', 'nameEn' => 'Germany'],
            ['id' => '403', 'nameEn' => 'Germany'],
            ['id' => '404', 'nameEn' => 'Germany'],
            ['id' => '405', 'nameEn' => 'Germany'],
            ['id' => '406', 'nameEn' => 'Germany'],
            ['id' => '407', 'nameEn' => 'Germany'],
            ['id' => '408', 'nameEn' => 'Germany'],
            ['id' => '409', 'nameEn' => 'Germany'],
            ['id' => '410', 'nameEn' => 'Germany'],
            ['id' => '411', 'nameEn' => 'Germany'],
            ['id' => '412', 'nameEn' => 'Germany'],
            ['id' => '413', 'nameEn' => 'Germany'],
            ['id' => '414', 'nameEn' => 'Germany'],
            ['id' => '415', 'nameEn' => 'Germany'],
            ['id' => '416', 'nameEn' => 'Germany'],
            ['id' => '417', 'nameEn' => 'Germany'],
            ['id' => '418', 'nameEn' => 'Germany'],
            ['id' => '419', 'nameEn' => 'Germany'],
            ['id' => '420', 'nameEn' => 'Germany'],
            ['id' => '421', 'nameEn' => 'Germany'],
            ['id' => '422', 'nameEn' => 'Germany'],
            ['id' => '423', 'nameEn' => 'Germany'],
            ['id' => '424', 'nameEn' => 'Germany'],
            ['id' => '425', 'nameEn' => 'Germany'],
            ['id' => '426', 'nameEn' => 'Germany'],
            ['id' => '427', 'nameEn' => 'Germany'],
            ['id' => '428', 'nameEn' => 'Germany'],
            ['id' => '429', 'nameEn' => 'Germany'],
            ['id' => '430', 'nameEn' => 'Germany'],
            ['id' => '431', 'nameEn' => 'Germany'],
            ['id' => '432', 'nameEn' => 'Germany'],
            ['id' => '433', 'nameEn' => 'Germany'],
            ['id' => '434', 'nameEn' => 'Germany'],
            ['id' => '435', 'nameEn' => 'Germany'],
            ['id' => '436', 'nameEn' => 'Germany'],
            ['id' => '437', 'nameEn' => 'Germany'],
            ['id' => '438', 'nameEn' => 'Germany'],
            ['id' => '439', 'nameEn' => 'Germany'],

            ['id' => '440', 'nameEn' => 'Germany'],

            ['id' => '460', 'nameEn' => 'Russia'],
            ['id' => '461', 'nameEn' => 'Russia'],
            ['id' => '462', 'nameEn' => 'Russia'],
            ['id' => '463', 'nameEn' => 'Russia'],
            ['id' => '464', 'nameEn' => 'Russia'],
            ['id' => '465', 'nameEn' => 'Russia'],
            ['id' => '466', 'nameEn' => 'Russia'],
            ['id' => '467', 'nameEn' => 'Russia'],
            ['id' => '468', 'nameEn' => 'Russia'],
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
            ['id' => '590', 'nameEn' => 'Poland'],
            ['id' => '690', 'nameEn' => 'China'],
            ['id' => '691', 'nameEn' => 'China'],
            ['id' => '692', 'nameEn' => 'China'],
            ['id' => '693', 'nameEn' => 'China'],
            ['id' => '694', 'nameEn' => 'China'],
            ['id' => '695', 'nameEn' => 'China'],
            ['id' => '696', 'nameEn' => 'China'],
            ['id' => '697', 'nameEn' => 'China'],
            ['id' => '698', 'nameEn' => 'China'],

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
            ['id' => '850', 'nameEn' => 'Cuba'],
            ['id' => '858', 'nameEn' => 'Slovakia'],
            ['id' => '859', 'nameEn' => 'Czech Republic'],
            ['id' => '860', 'nameEn' => 'Serbia'],
            ['id' => '861', 'nameEn' => 'Mongolia'],
            ['id' => '862', 'nameEn' => 'Morocco'],
            ['id' => '863', 'nameEn' => 'Côte d\'Ivoire'],
            ['id' => '864', 'nameEn' => 'Syria'],
            ['id' => '865', 'nameEn' => 'Mongolia'],
            ['id' => '866', 'nameEn' => 'North Korea'],
            ['id' => '867', 'nameEn' => 'Bangladesh'],
            ['id' => '868', 'nameEn' => 'Türkiye'],
            ['id' => '869', 'nameEn' => 'Türkiye'],

            ['id' => '870', 'nameEn' => 'Netherlands'],
            ['id' => '871', 'nameEn' => 'Netherlands'],
            ['id' => '872', 'nameEn' => 'Netherlands Antilles'],
            ['id' => '873', 'nameEn' => 'Netherlands Antilles'],
            ['id' => '874', 'nameEn' => 'Netherlands Antilles'],
            ['id' => '875', 'nameEn' => 'Netherlands Antilles'],
            ['id' => '876', 'nameEn' => 'Saint Pierre and Miquelon'],
            ['id' => '877', 'nameEn' => 'Netherlands Antilles'],
            ['id' => '878', 'nameEn' => 'Netherlands Antilles'],
            ['id' => '879', 'nameEn' => 'Netherlands Antilles'],
            ['id' => '880', 'nameEn' => 'South Korea'],
            ['id' => '881', 'nameEn' => 'South Korea'],
            ['id' => '882', 'nameEn' => 'South Korea'],
            ['id' => '883', 'nameEn' => 'South Korea'],
            ['id' => '884', 'nameEn' => 'Cambodia'],
            ['id' => '885', 'nameEn' => 'Thailand'],
            ['id' => '886', 'nameEn' => 'Taiwan'],
            ['id' => '887', 'nameEn' => 'Bangladesh'],
            ['id' => '888', 'nameEn' => 'Singapore'],
            ['id' => '889', 'nameEn' => 'Singapore'],


            ['id' => '890', 'nameEn' => 'India'],
            ['id' => '891', 'nameEn' => 'India'],
            ['id' => '892', 'nameEn' => 'India'],
            ['id' => '893', 'nameEn' => 'India'],
            ['id' => '894', 'nameEn' => 'India'],
            ['id' => '895', 'nameEn' => 'India'],
            ['id' => '896', 'nameEn' => 'India'],
            ['id' => '897', 'nameEn' => 'India'],
            ['id' => '898', 'nameEn' => 'India'],
            ['id' => '899', 'nameEn' => 'India'],

            ['id' => '900', 'nameEn' => 'Austria'],

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
