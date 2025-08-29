<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\TasnifCode; // Import the Commands namespace

class Kernel extends ConsoleKernel
{
  protected $commands = [
    TasnifCode::class,
  ];
  protected function schedule(Schedule $schedule): void
  {
    // Add your scheduled commands here
    $schedule->command('tasnif:code')->everyMinute();
  }

  protected function commands(): void
  {
    $this->load(__DIR__ . '/Commands');

    require base_path('routes/console.php');
  }
}
