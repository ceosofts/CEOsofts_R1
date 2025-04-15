<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ClearAllCacheCommand extends Command
{
    protected $signature = 'cache:all-clear';
    protected $description = 'ล้างแคชทุกประเภทของ Laravel เช่น views, routes, config, debugbar';

    public function handle()
    {
        $start = microtime(true);
        $this->info('🧹 เริ่มล้างแคชทั้งหมด...');

        // 1. ล้าง Laravel Cache
        $this->info('🔍 ล้างแคช Application...');
        Artisan::call('cache:clear');
        $this->info('  ✅ ล้าง Application cache เรียบร้อยแล้ว');

        // 2. ล้าง Config Cache
        $this->info('🔍 ล้างแคช Config...');
        Artisan::call('config:clear');
        $this->info('  ✅ ล้าง Config cache เรียบร้อยแล้ว');

        // 3. ล้าง Route Cache
        $this->info('🔍 ล้างแคช Route...');
        Artisan::call('route:clear');
        $this->info('  ✅ ล้าง Route cache เรียบร้อยแล้ว');

        // 4. ล้างแคช Blade Views
        $this->info('🔍 ล้างแคช Blade Views...');
        $viewsPath = storage_path('framework/views');
        if (File::exists($viewsPath)) {
            $fileCount = count(File::files($viewsPath));
            $size = $this->getDirectorySize($viewsPath);
            
            if ($fileCount > 0) {
                foreach (File::files($viewsPath) as $file) {
                    File::delete($file->getPathname());
                }
                $this->info("  ✅ ล้าง {$fileCount} ไฟล์แคช Views (ขนาด {$this->formatBytes($size)}) เรียบร้อยแล้ว");
            } else {
                $this->info('  ℹ️ ไม่มีไฟล์แคช Views ที่ต้องล้าง');
            }
        } else {
            $this->warn('  ⚠️ ไม่พบโฟลเดอร์ framework/views');
        }

        // 5. ล้างแคช Debugbar
        $this->info('🔍 ล้างแคช Debugbar...');
        $debugbarPath = storage_path('debugbar');
        if (File::exists($debugbarPath)) {
            $fileCount = count(File::files($debugbarPath));
            $size = $this->getDirectorySize($debugbarPath);
            
            if ($fileCount > 0) {
                foreach (File::files($debugbarPath) as $file) {
                    File::delete($file->getPathname());
                }
                $this->info("  ✅ ล้าง {$fileCount} ไฟล์แคช Debugbar (ขนาด {$this->formatBytes($size)}) เรียบร้อยแล้ว");
            } else {
                $this->info('  ℹ️ ไม่มีไฟล์แคช Debugbar ที่ต้องล้าง');
            }
        } else {
            // สร้างไดเร็กทอรี debugbar ถ้าไม่มี
            File::makeDirectory($debugbarPath, 0755, true, true);
            $this->info('  ✅ สร้างโฟลเดอร์ debugbar ใหม่เรียบร้อยแล้ว');
        }

        // 6. ล้าง Laravel Log (ถ้าขนาดใหญ่เกิน)
        $this->info('🔍 ตรวจสอบขนาด Laravel Log...');
        $logPath = storage_path('logs/laravel.log');
        if (File::exists($logPath)) {
            $logSize = File::size($logPath);
            if ($logSize > 10 * 1024 * 1024) { // มากกว่า 10MB
                // เซฟล็อกเก่าไว้
                $backupPath = storage_path('logs/laravel.log.backup-' . date('Y-m-d-His'));
                File::copy($logPath, $backupPath);
                
                // ล้างล็อกปัจจุบัน
                File::put($logPath, "Log cleared on " . date('Y-m-d H:i:s') . "\n");
                
                $this->info("  ✅ สำรองล็อกขนาดใหญ่ ({$this->formatBytes($logSize)}) ไว้ที่ " . basename($backupPath) . " และล้างล็อกเดิมเรียบร้อยแล้ว");
            } else {
                $this->info("  ℹ️ ไฟล์ล็อกมีขนาด {$this->formatBytes($logSize)} ยังไม่จำเป็นต้องล้าง");
            }
        }
        
        $time = round(microtime(true) - $start, 2);
        $this->info('');
        $this->info("✨ ล้างแคชทั้งหมดเรียบร้อยแล้ว (ใช้เวลา {$time} วินาที)");
        $this->info('💡 คำแนะนำ: หลังล้างแคช Views แอปอาจทำงานช้าลงชั่วคราวเนื่องจากต้องคอมไพล์ view ใหม่');
        return Command::SUCCESS;
    }
    
    private function getDirectorySize($path)
    {
        $size = 0;
        foreach (File::allFiles($path) as $file) {
            $size += $file->getSize();
        }
        return $size;
    }
    
    private function formatBytes($size, $precision = 2)
    {
        if ($size == 0) {
            return "0 B";
        }
        $base = log($size, 1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }
}
