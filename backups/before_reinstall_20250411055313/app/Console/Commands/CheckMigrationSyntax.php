<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class CheckMigrationSyntax extends Command
{
    protected $signature = 'migrate:check-syntax {file? : ไฟล์ migration ที่ต้องการตรวจสอบ}';

    protected $description = 'ตรวจสอบไวยากรณ์ของไฟล์ migration';

    public function handle()
    {
        $file = $this->argument('file');
        
        if ($file) {
            // ตรวจสอบไฟล์เดียว
            $path = database_path('migrations/' . $file);
            if (!File::exists($path)) {
                $this->error("ไม่พบไฟล์: {$path}");
                return 1;
            }
            
            $this->checkFile($path);
        } else {
            // ตรวจสอบทุกไฟล์ migration
            $files = File::glob(database_path('migrations/*.php'));
            $this->info("พบไฟล์ migration ทั้งหมด " . count($files) . " ไฟล์");
            
            $errorCount = 0;
            foreach ($files as $file) {
                if (!$this->checkFile($file, false)) {
                    $errorCount++;
                }
            }
            
            if ($errorCount === 0) {
                $this->info("✓ ทุกไฟล์ผ่านการตรวจสอบ");
            } else {
                $this->error("พบข้อผิดพลาดในไฟล์ {$errorCount} ไฟล์");
                return 1;
            }
        }
        
        return 0;
    }
    
    private function checkFile($path, $verbose = true)
    {
        $fileName = pathinfo($path, PATHINFO_BASENAME);
        
        if ($verbose) {
            $this->info("กำลังตรวจสอบไฟล์: {$fileName}");
        }
        
        // รัน PHP Lint
        $process = Process::fromShellCommandline('php -l ' . escapeshellarg($path));
        $process->run();
        
        if ($process->isSuccessful()) {
            if ($verbose) {
                $this->info("✓ ไฟล์ {$fileName} ไม่พบข้อผิดพลาดทางไวยากรณ์");
            }
            return true;
        } else {
            $this->error("✗ ไฟล์ {$fileName} พบข้อผิดพลาดทางไวยากรณ์:");
            $this->line($process->getErrorOutput());
            
            // แนะนำวิธีแก้ไข
            $this->info("แนะนำให้เปิดไฟล์ {$path} และตรวจสอบโครงสร้างของไฟล์");
            
            return false;
        }
    }
}
