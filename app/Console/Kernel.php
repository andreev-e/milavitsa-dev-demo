<?php

namespace App\Console;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Console\Scheduling\Schedule;

class Kernel extends ConsoleKernel
{
    protected $commands = [
    // Commands\Inspire::class,
    ];

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('mailing:work')->everyMinute();
    }
}
