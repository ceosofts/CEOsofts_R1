<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class MigrateCheckSyntaxCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:check-syntax {file? : Optional specific migration file to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ตรวจสอบไวยากรณ์ของไฟล์ migration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $specificFile = $this->argument('file');
        
        if ($specificFile) {
            $this->checkFile(database_path('migrations/' . $specificFile));
        } else {
            $this->checkAllFiles();
        }
        
        return Command::SUCCESS;
    }
    
    /**
     * ตรวจสอบไฟล์ทั้งหมดในโฟลเดอร์ migrations
     */
    protected function checkAllFiles()
    {
        $this->info('กำลังตรวจสอบไวยากรณ์ของไฟล์ migration ทั้งหมด...');
        
        $files = (new Finder())
            ->files()
            ->name('*.php')
            ->in(database_path('migrations'));
        
        $hasProblems = false;
        $problemFiles = [];
        
        foreach ($files as $file) {
            $result = $this->checkFile($file->getRealPath(), false);
            
            if (!$result) {
                $hasProblems = true;
                $problemFiles[] = $file->getFilename();
            }
        }
        
        if ($hasProblems) {
            $this->warn("\nพบปัญหาในไฟล์ต่อไปนี้:");
            foreach ($problemFiles as $file) {
                $this->line("- {$file}");
            }
            
            $this->info("\nคุณสามารถแก้ไขไฟล์เหล่านี้ด้วยคำสั่ง:");
            $this->line("php artisan migrate:fix-files");
        } else {
            $this->info('ไม่พบปัญหาไวยากรณ์ในไฟล์ migration');
        }
    }
    
    /**
     * ตรวจสอบไฟล์เดี่ยว
     * 
     * @param string $filePath
     * @param bool $showOutput
     * @return bool
     */
    protected function checkFile($filePath, $showOutput = true)
    {
        if (!File::exists($filePath)) {
            if ($showOutput) {
                $this->error("ไม่พบไฟล์: {$filePath}");
            }
            return false;
        }
        
        $filename = basename($filePath);
        $content = File::get($filePath);
        $hasProblems = false;
        
        // ตรวจสอบ Schema::create ที่ไม่มีการตรวจสอบการมีอยู่ของตาราง
        if (Str::contains($content, 'Schema::create') && !Str::contains($content, 'Schema::hasTable')) {
            $hasProblems = true;
            
            if ($showOutput) {
                $this->warn("ไฟล์ {$filename} ใช้ Schema::create โดยไม่ตรวจสอบการมีอยู่ของตารางก่อน");
                $this->info("แนะนำให้แก้ไขเป็น:");
                $this->line("if (!Schema::hasTable('table_name')) {");
                $this->line("    Schema::create('table_name', function (Blueprint \$table) {");
                $this->line("        // ...");
                $this->line("    });");
                $this->line("}");
            }
        }
        
        // ตรวจสอบการตั้งชื่อคอลัมน์ที่อาจจะเป็นคำสงวน
        $reservedWords = ['key', 'group', 'order', 'default', 'limit', 'password', 'index', 'value'];
        foreach ($reservedWords as $word) {
            if (Str::contains($content, "\$table->{$word}(") || 
                Str::contains($content, "\$table->string('{$word}")
            ) {
                $hasProblems = true;
                
                if ($showOutput) {
                    $this->warn("ไฟล์ {$filename} มีการใช้คำสงวน '{$word}' เป็นชื่อคอลัมน์");
                    $this->info("อาจทำให้เกิดปัญหากับบาง database drivers");
                }
            }
        }
        
        // ตรวจสอบ foreign key constraints ที่ขาด onDelete หรือ onUpdate
        if (Str::contains($content, 'foreign') && !Str::contains($content, 'onDelete')) {
            $hasProblems = true;
            
            if ($showOutput) {
                $this->warn("ไฟล์ {$filename} มีการใช้ foreign key constraints โดยไม่ระบุ onDelete");
                $this->info("แนะนำให้ระบุ onDelete เช่น:");
                $this->line("\$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');");
            }
        }
        
        if (!$hasProblems && $showOutput) {
            $this->info("ไม่พบปัญหาไวยากรณ์ในไฟล์ {$filename}");
        }
        
        return !$hasProblems;
    }
}
