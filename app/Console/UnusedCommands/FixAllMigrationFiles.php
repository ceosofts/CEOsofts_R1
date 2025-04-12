<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class FixAllMigrationFiles extends Command
{
    protected $signature = 'migrate:fix-all-files {--dry-run : เพียงแค่แสดงไฟล์ที่จะถูกแก้ไขโดยไม่ทำการแก้ไขจริง}';

    protected $description = 'แก้ไขไฟล์ migration ทั้งหมดที่มีปัญหา';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        // ดึงรายการไฟล์ migration ทั้งหมด
        $files = File::glob(database_path('migrations/*.php'));
        $this->info("พบไฟล์ migration ทั้งหมด " . count($files) . " ไฟล์");
        
        $problemFiles = [];
        $this->info("กำลังตรวจสอบไวยากรณ์ของไฟล์...");
        
        $bar = $this->output->createProgressBar(count($files));
        $bar->start();
        
        foreach ($files as $file) {
            if (!$this->checkFileSyntax($file)) {
                $problemFiles[] = $file;
            }
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        if (empty($problemFiles)) {
            $this->info("ไม่พบไฟล์ที่มีปัญหา");
            return 0;
        }
        
        $this->warn("พบไฟล์ที่มีปัญหาทั้งหมด " . count($problemFiles) . " ไฟล์:");
        foreach ($problemFiles as $index => $file) {
            $this->line(($index+1) . ". " . basename($file));
        }
        
        if ($dryRun) {
            $this->info("กำลังทำงานในโหมด dry run จึงไม่มีการแก้ไขไฟล์");
            return 0;
        }
        
        if (!$this->confirm('ต้องการแก้ไขไฟล์ทั้งหมดที่มีปัญหาหรือไม่?')) {
            $this->info("ยกเลิกการแก้ไข");
            return 0;
        }
        
        $fixedCount = 0;
        foreach ($problemFiles as $file) {
            $this->info("กำลังแก้ไขไฟล์: " . basename($file));
            if ($this->fixFile($file)) {
                $fixedCount++;
            }
        }
        
        $this->info("แก้ไขไฟล์สำเร็จ {$fixedCount} ไฟล์จากทั้งหมด " . count($problemFiles) . " ไฟล์");
        
        return 0;
    }
    
    private function checkFileSyntax($path)
    {
        $process = Process::fromShellCommandline('php -l ' . escapeshellarg($path));
        $process->run();
        return $process->isSuccessful();
    }
    
    private function fixFile($path)
    {
        // สำรองไฟล์
        $backupPath = $path . '.backup.' . time();
        File::copy($path, $backupPath);
        
        // อ่านเนื้อหาไฟล์
        $content = File::get($path);
        
        // แก้ไขปัญหาไวยากรณ์ทั่วไป
        $fixed = false;
        $newContent = $content;
        
        // 1. ตรวจสอบคำประกาศคลาส
        if (preg_match('/^(.*?)(class\s+[a-zA-Z0-9_]+\s+extends\s+Migration\s*\{)/s', $content, $matches)) {
            $newContent = $matches[1] . 'return new class extends Migration {';
            $fixed = true;
            $this->line("- แก้ไขประกาศคลาสให้เป็นแบบ anonymous class");
        }
        
        // 2. ตรวจสอบว่ามีการปิดคลาสด้วย }; หรือไม่
        if (!preg_match('/\};[\s\r\n]*$/s', $newContent)) {
            // แก้ไขให้ปิดด้วย };
            $newContent = preg_replace('/}[\s\r\n]*$/', '};', $newContent);
            $fixed = true;
            $this->line("- เพิ่ม semicolon หลัง closing brace");
        }
        
        // 3. ตรวจสอบการเว้นวรรค/บรรทัด และ formatting อื่นๆ
        $newContent = preg_replace('/\}[\s\r\n]*public\s+function/', "}\n\n    public function", $newContent);
        
        // 4. ตรวจสอบ namespace
        if (strpos($newContent, 'namespace') !== false && !preg_match('/namespace.*?;\s*(?:use|class|return)/s', $newContent)) {
            $newContent = preg_replace('/(namespace\s+[^;]+);/', '$1;', $newContent);
            $fixed = true;
            $this->line("- แก้ไข namespace declaration");
        }
        
        if ($fixed || $newContent !== $content) {
            // บันทึกการแก้ไข
            File::put($path, $newContent);
            
            // ตรวจสอบว่าแก้ไขสำเร็จหรือไม่
            if ($this->checkFileSyntax($path)) {
                $this->info("✓ แก้ไขไฟล์ " . basename($path) . " สำเร็จ");
                // ลบไฟล์สำรอง
                File::delete($backupPath);
                return true;
            } else {
                $this->error("✗ แก้ไขไฟล์ไม่สำเร็จ คืนค่าจากไฟล์สำรอง");
                File::copy($backupPath, $path);
                return false;
            }
        }
        
        $this->warn("! ไม่พบรูปแบบที่ต้องแก้ไขในไฟล์ " . basename($path));
        File::delete($backupPath);
        return false;
    }
}
