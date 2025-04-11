<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixMigrationFiles extends Command
{
    protected $signature = 'migrate:fix-files {file? : ไฟล์ migration ที่ต้องการแก้ไข}';

    protected $description = 'แก้ไขไฟล์ migration ที่มีปัญหา';

    public function handle()
    {
        $file = $this->argument('file');
        
        if ($file) {
            // แก้ไขไฟล์เดียว
            $path = database_path('migrations/' . $file);
            if (!File::exists($path)) {
                $this->error("ไม่พบไฟล์: {$path}");
                return 1;
            }
            
            $this->fixFile($path);
        } else {
            // แสดงรายการไฟล์ migration ทั้งหมด
            $files = File::glob(database_path('migrations/*.php'));
            $options = [];
            
            foreach ($files as $index => $filePath) {
                $fileName = pathinfo($filePath, PATHINFO_BASENAME);
                $options[$fileName] = $fileName;
                $this->line(($index + 1) . ". {$fileName}");
            }
            
            $fileToFix = $this->choice('เลือกไฟล์ที่ต้องการแก้ไข:', $options);
            $path = database_path('migrations/' . $fileToFix);
            $this->fixFile($path);
        }
        
        return 0;
    }
    
    private function fixFile($path)
    {
        $fileName = pathinfo($path, PATHINFO_BASENAME);
        $this->info("กำลังแก้ไขไฟล์: {$fileName}");
        
        // สำรองไฟล์ก่อนแก้ไข
        $backupPath = $path . '.backup';
        File::copy($path, $backupPath);
        $this->info("สำรองไฟล์ไว้ที่: {$backupPath}");
        
        // อ่านเนื้อหาไฟล์
        $content = File::get($path);
        
        // แก้ไขข้อผิดพลาดทั่วไป
        $fixedContent = $this->applyCommonFixes($content);
        
        // บันทึกการแก้ไข
        File::put($path, $fixedContent);
        $this->info("บันทึกการแก้ไขเรียบร้อยแล้ว");
        
        // ตรวจสอบว่าการแก้ไขถูกต้องหรือไม่
        $this->call('migrate:check-syntax', [
            'file' => $fileName
        ]);
    }
    
    private function applyCommonFixes($content)
    {
        // แก้ไข syntax error ทั่วไป
        
        // 1. ตรวจสอบว่ามี class declaration ที่ถูกต้อง
        if (!preg_match('/return\s+new\s+class\s+extends\s+Migration\s*\{/', $content)) {
            // แก้ไข class declaration
            $content = preg_replace(
                '/(use\s+[^;]+;\s*)(class\s+[^\s]+\s+extends\s+Migration\s*\{)/',
                '$1return new class extends Migration {',
                $content
            );
        }
        
        // 2. ตรวจสอบว่าปิด } ครบหรือไม่
        $openBraces = substr_count($content, '{');
        $closeBraces = substr_count($content, '}');
        
        if ($openBraces > $closeBraces) {
            // เพิ่ม } ตอนท้าย
            $content .= "\n};\n";
        } elseif ($closeBraces > $openBraces) {
            // ลบ } ที่เกิน
            $lastPos = strrpos($content, '}');
            if ($lastPos !== false) {
                $content = substr_replace($content, '', $lastPos, 1);
            }
        }
        
        // 3. ตรวจสอบการเว้นวรรคและบรรทัด
        $content = preg_replace('/\}\s*public\s+function/', "}\n\n    public function", $content);
        
        // 4. แก้ไขเรื่องการใช้ namespace
        $content = preg_replace('/(namespace\s+[^;]+);\s*class/', '$1;\n\nclass', $content);
        
        // 5. เพิ่ม semicolons ที่อาจขาดหาย
        $content = preg_replace('/\$table->([^;]+)(\s*\/\/|\s*\n)/', '$table->$1;$2', $content);
        
        return $content;
    }
}
