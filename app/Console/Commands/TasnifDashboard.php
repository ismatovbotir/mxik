<?php

namespace App\Console\Commands;

use App\Models\Product;
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
        $stats = DB::table('products')
            ->join('groups', 'products.group_id', '=', 'groups.id')
            ->join('gtins', 'products.gtin_id', '=', 'gtins.id')
            ->select('products.nameUz', 'gtins.id', DB::raw('COUNT(products.id) as total'))
            ->groupBy('products.nameUz', 'gtins.id')
            ->get();

        dd($stats);
    }
}
