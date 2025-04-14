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
        
        // ล้าง debugbar ทุกวันตอนเที่ยงคืน
        $schedule->command('debugbar:clear')->daily();
        
        // ล้างแคช view ทุกวันอาทิตย์หรือเมื่อขนาดเกิน threshold
        $schedule->call(function () {
            $viewsPath = storage_path('framework/views');
            $sizeInMB = $this->getDirectorySizeInMB($viewsPath);
            
            // ล้างเมื่อขนาดเกิน 50MB หรือเป็นวันอาทิตย์
            if ($sizeInMB > 50 || date('w') == 0) {
                \Artisan::call('views:clear');
            }
        })->dailyAt('01:00');
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
     * คำนวณขนาดของไดเร็กทอรีเป็น MB
     */
    private function getDirectorySizeInMB($path)
    {
        if (!file_exists($path)) {
            return 0;
        }
        
        $size = 0;
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        
        return round($size / 1048576, 2); // แปลงเป็น MB
    }

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\OptimizeDatabaseCommand::class,
        \App\Console\Commands\ProjectStructureCommand::class,
        \App\Console\Commands\ClearDebugbarCommand::class, 
        \App\Console\Commands\ClearViewCacheCommand::class,
    ];
}
