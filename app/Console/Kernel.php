<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        // ตรวจสอบว่าคลาส ConsoleCustomColors มีอยู่หรือไม่ก่อนเรียกใช้
        if (class_exists(ConsoleCustomColors::class)) {
            ConsoleCustomColors::setupStyles();
        }
        
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\OptimizeDatabaseCommand::class,
        \App\Console\Commands\ProjectStructureCommand::class,
    ];
}
