<?php

namespace App\Console\Commands;

use App\Models\DashboardGroup;
use App\Models\DashboardMain;
use App\Models\Product;
use App\Models\Group;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TasnifDashboard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasnif:dashboard';

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
        //DashboardMain
        //  'items_count'  => Product::count(),
        //         'groups_count' => Group::count(),
        //         'asl_count'    => Product::where('label', 1)->count(),
        //         'gtin_count'   => Product::whereNotNull('gtin')->count(),
        //         'productsByCountry' => $productsByCountry,


        $data = [
            [
                'id' => 1,
                'title' => 'Guruhlar soni',
                'description' => 'Nashrdan o\'chirilgan mahsulotlar soni',
                'value' => Group::count(),
                'unit' => ''
            ],
            [
                'id' => 2,
                'title' => 'Umumiy mahsulotlar soni',
                'description' => 'Barcha mahsulotlar soni',
                'value' => Product::count(),
                'unit' => ''
            ],
            [
                'id' => 3,
                'title' => 'AslBelgi (Markirovka) mahsulotlar soni',
                'description' => 'Nashrdagi mahsulotlar soni',
                'value' => Product::where('label', 1)->count(),
                'unit' => ''
            ],
            [
                'id' => 4,
                'title' => 'GTIN mavjud mahsulotlar soni',
                'description' => 'Nashrdan o\'chirilgan mahsulotlar soni',
                'value' => Product::whereNotNull('gtin')->count(),
                'unit' => ''
            ]

        ];

        DashboardMain::upsert(
            $data,
            ['id'], // Unique key to avoid duplicates
            ['title', 'description', 'unit', 'value'] // Fields to update if the record exists
        );




        //Dashboardgroup
        $stats = DB::table('products')
            ->join('groups', 'products.group_id', '=', 'groups.id')
            ->join('gtins', 'products.gtin_id', '=', 'gtins.id')
            ->select('groups.id as group_id', 'groups.name as name', 'gtins.id as gtin_id', 'gtins.nameEn as country', 'products.label as label', DB::raw('COUNT(products.id) as total'))
            ->groupBy('groups.id', 'groups.name', 'gtins.id', 'gtins.nameEn', 'products.label')
            ->get();

        //dd($stats);
        foreach ($stats as $key => $stat) {
            //dd($stat);
            $data = [
                'gtin_id' => $stat->gtin_id,
                'country' => $stat->country,
                'group_id' => $stat->group_id,
                'name' => $stat->name,
                'label' => $stat->label,
                'total' => $stat->total,
            ];
            DashboardGroup::upsert(
                [$data],
                ['gtin_id', 'group_id'], // Unique key to avoid duplicates
                ['total'] // Fields to update if the record exists
            );
        }
    }
}
