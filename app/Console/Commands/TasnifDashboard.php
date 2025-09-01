<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

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
        $product = Product::with(['group', 'gtin'])->paginate(10)->toArray();
        $this->info(json_encode($product));
    }
}
