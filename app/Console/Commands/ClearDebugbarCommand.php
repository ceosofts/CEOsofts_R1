<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearDebugbarCommand extends Command
{
    protected $signature = 'debugbar:clear';
    protected $description = 'ล้างไฟล์ทั้งหมดใน storage/debugbar';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $debugbarPath = storage_path('debugbar');
        
        if (File::exists($debugbarPath)) {
            $fileCount = count(File::files($debugbarPath));
            $size = $this->getDirectorySize($debugbarPath);
            
            // แทนที่จะลบและสร้าง directory ใหม่ เราจะลบเฉพาะไฟล์ข้างในแทน
            foreach (File::files($debugbarPath) as $file) {
                File::delete($file->getPathname());
            }
            
            $this->info("ลบไฟล์ debugbar จำนวน {$fileCount} ไฟล์ (ขนาดรวม {$this->formatBytes($size)}) เรียบร้อยแล้ว");
        } else {
            // ถ้าไม่มี directory ให้สร้างใหม่
            File::makeDirectory($debugbarPath, 0755, true, true);
            $this->info('สร้างโฟลเดอร์ debugbar ใหม่เรียบร้อยแล้ว');
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
