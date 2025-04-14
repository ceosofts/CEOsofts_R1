<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearViewCacheCommand extends Command
{
    protected $signature = 'views:clear';
    protected $description = 'ล้างไฟล์แคช Blade views ทั้งหมด';

    public function handle()
    {
        $viewsPath = storage_path('framework/views');
        
        if (File::exists($viewsPath)) {
            $fileCount = count(File::files($viewsPath));
            $size = $this->getDirectorySize($viewsPath);
            
            // ล้างไฟล์แคชทั้งหมด แต่รักษาโครงสร้างโฟลเดอร์ไว้
            foreach (File::allFiles($viewsPath) as $file) {
                File::delete($file->getPathname());
            }
            
            $this->info("ล้างแคช blade templates จำนวน {$fileCount} ไฟล์ (ขนาดรวม {$this->formatBytes($size)}) เรียบร้อยแล้ว");
            $this->info("หมายเหตุ: แอปพลิเคชันอาจทำงานช้าลงชั่วคราวเนื่องจากต้องคอมไพล์ view ใหม่");
        } else {
            $this->error('ไม่พบโฟลเดอร์ framework/views');
        }
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
