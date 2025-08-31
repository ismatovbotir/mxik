<?php

namespace App\Console\Commands;

use App\Models\Product;
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
        $gtins = Product::whereRaw('LENGTH(gtin)<13')->get();
        foreach ($gtins as $gtin) {

            $this->info($gtin['gtin']);
        }
    }
}
