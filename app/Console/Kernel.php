<?php

namespace App\Console;

use App\Http\Tasks\BirthDayReward;
use App\Http\Tasks\MembershipReminder;
use App\Http\Tasks\PointExpiryReminder;
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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('telescope:prune --hours=48')->daily();
        $schedule->call(new BirthDayReward)->dailyAt('09:00');
        $schedule->call(new PointExpiryReminder)->dailyAt('09:00');
        $schedule->call(new MembershipReminder)->dailyAt('09:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
