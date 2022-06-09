<?php

namespace App\Console;

use App\Console\Commands\Count;
use App\Console\Commands\LotteryOrders;
use App\Console\Commands\OpenWin;
use App\Console\Commands\UpdateCount;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        OpenWin::class,LotteryOrders::class,UpdateCount::class,Count::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
	$schedule->command('UpdateCount')->daily(); 
       $schedule->command('LotteryOrders')->daily();
        $schedule->command('OpenWin')->everyThreeMinutes();
   	$schedule->command('Count')->dailyAt('23:50');
	 }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
