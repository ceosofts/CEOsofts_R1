<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class MigrateFixFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:fix-files {file? : Optional specific migration file to fix}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'แก้ไขไฟล์ migration ที่มีปัญหา';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $specificFile = $this->argument('file');
        
        if ($specificFile) {
            $this->fixFile(database_path('migrations/' . $specificFile));
        } else {
            $this->fixAllFiles();
        }
        
        return Command::SUCCESS;
    }
    
    /**
     * แก้ไขไฟล์ทั้งหมดในโฟลเดอร์ migrations
     */
    protected function fixAllFiles()
    {
        $this->info('กำลังตรวจสอบและแก้ไขไฟล์ migration ทั้งหมด...');
        
        $files = (new Finder())
            ->files()
            ->name('*.php')
            ->in(database_path('migrations'));
        
        $fixedFiles = [];
        
        foreach ($files as $file) {
            $result = $this->fixFile($file->getRealPath(), false);
            
            if ($result) {
                $fixedFiles[] = $file->getFilename();
            }
        }
        
        if (count($fixedFiles) > 0) {
            $this->info("\nไฟล์ที่ได้รับการแก้ไขแล้ว:");
            foreach ($fixedFiles as $file) {
                $this->line("- {$file}");
            }
        } else {
            $this->info('ไม่มีไฟล์ที่จำเป็นต้องแก้ไข');
        }
    }
    
    /**
     * แก้ไขไฟล์เดี่ยว
     * 
     * @param string $filePath
     * @param bool $showOutput
     * @return bool
     */
    protected function fixFile($filePath, $showOutput = true)
    {
        if (!File::exists($filePath)) {
            if ($showOutput) {
                $this->error("ไม่พบไฟล์: {$filePath}");
            }
            return false;
        }
        
        $filename = basename($filePath);
        $content = File::get($filePath);
        $originalContent = $content;
        $modified = false;
        
        // แก้ไข Schema::create ให้มีการตรวจสอบการมีอยู่ของตาราง
        if (Str::contains($content, 'Schema::create') && !Str::contains($content, 'Schema::hasTable')) {
            // หา table name จาก Schema::create
            $pattern = "/Schema::create\(['\"]([^'\"]+)['\"]/";
            preg_match_all($pattern, $content, $matches);
            
            if (isset($matches[1]) && count($matches[1]) > 0) {
                foreach ($matches[1] as $tableName) {
                    $search = "Schema::create('$tableName', function (Blueprint \$table) {";
                    $replace = "if (!Schema::hasTable('$tableName')) {\n            Schema::create('$tableName', function (Blueprint \$table) {";
                    $content = str_replace($search, $replace, $content);
                    
                    // เพิ่ม } ปิด if statement ท้าย function
                    $search = "});\n";
                    $replace = "});\n        }\n";
                    $content = str_replace($search, $replace, $content);
                    
                    $modified = true;
                }
            }
        }
        
        // แก้ไขคำสงวนใน column names โดยเพิ่ม _ ต่อท้าย
        $reservedWords = ['key', 'group', 'order', 'default', 'limit', 'password', 'index', 'value'];
        foreach ($reservedWords as $word) {
            $pattern = "/\\$table->string\\('$word'\\)/i";
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "\$table->string('{$word}_col')", $content);
                $modified = true;
            }
            
            // Function ที่ตั้งชื่อตรงกับคำสงวน
            $pattern = "/\\$table->$word\\(/i";
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "\$table->{$word}_col(", $content);
                $modified = true;
            }
        }
        
        // เพิ่ม onDelete('cascade') ให้กับ foreign key constraints
        $pattern = "/(\\$table->foreign\\([^)]+\\)->references\\([^)]+\\)->on\\([^)]+\\))(;|->)/i";
        if (preg_match($pattern, $content) && !Str::contains($content, 'onDelete')) {
            $content = preg_replace($pattern, "$1->onDelete('cascade')$2", $content);
            $modified = true;
        }
        
        // บันทึกการเปลี่ยนแปลงถ้ามีการแก้ไข
        if ($modified) {
            // สำรองไฟล์เดิม
            File::put($filePath . '.bak', $originalContent);
            
            // บันทึกไฟล์ที่แก้ไขแล้ว
            File::put($filePath, $content);
            
            if ($showOutput) {
                $this->info("แก้ไขไฟล์ {$filename} เรียบร้อยแล้ว (สำรองไว้ที่ {$filename}.bak)");
            }
            
            return true;
        } else {
            if ($showOutput) {
                $this->info("ไฟล์ {$filename} ไม่จำเป็นต้องแก้ไข");
            }
            
            return false;
        }
    }
}
