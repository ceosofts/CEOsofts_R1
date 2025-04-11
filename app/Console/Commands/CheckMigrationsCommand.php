<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CheckMigrationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrations:check {--fix : แก้ไขปัญหาอัตโนมัติ}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ตรวจสอบปัญหาทั่วไปในไฟล์ migrations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("กำลังตรวจสอบไฟล์ migrations...");
        
        $migrationPaths = glob(database_path("migrations/*.php"));
        $problemsFound = false;
        $problems = [];
        
        foreach ($migrationPaths as $migrationPath) {
            $content = file_get_contents($migrationPath);
            $filename = basename($migrationPath);
            
            // เพิ่มการตรวจสอบ DB facade
            if (preg_match('/\\\\DB::|DB::|\$this->db->|->table\(/', $content) && !Str::contains($content, 'use Illuminate\Support\Facades\DB;')) {
                $problems[$migrationPath][] = "ใช้ DB แต่ไม่มีการ import: use Illuminate\Support\Facades\DB;";
                $problemsFound = true;
            }
            
            // ตรวจสอบการใช้ Log โดยไม่มี import
            if (preg_match('/\\\\Log::|Log::|->log\(/i', $content) && !Str::contains($content, 'use Illuminate\Support\Facades\Log;')) {
                $problems[$migrationPath][] = "ใช้ Log แต่ไม่มีการ import: use Illuminate\Support\Facades\Log;";
                $problemsFound = true;
            }
            
            // ตรวจสอบการใช้ Validator โดยไม่มี import
            if (preg_match('/\\\\Validator::|Validator::/', $content) && !Str::contains($content, 'use Illuminate\Support\Facades\Validator;')) {
                $problems[$migrationPath][] = "ใช้ Validator แต่ไม่มีการ import: use Illuminate\Support\Facades\Validator;";
                $problemsFound = true;
            }
            
            // ตรวจสอบการใช้ Route โดยไม่มี import
            if (preg_match('/\\\\Route::|Route::/', $content) && !Str::contains($content, 'use Illuminate\Support\Facades\Route;')) {
                $problems[$migrationPath][] = "ใช้ Route แต่ไม่มีการ import: use Illuminate\Support\Facades\Route;";
                $problemsFound = true;
            }
            
            // ตรวจสอบการใช้ Artisan โดยไม่มี import
            if (preg_match('/\\\\Artisan::|Artisan::/', $content) && !Str::contains($content, 'use Illuminate\Support\Facades\Artisan;')) {
                $problems[$migrationPath][] = "ใช้ Artisan แต่ไม่มีการ import: use Illuminate\Support\Facades\Artisan;";
                $problemsFound = true;
            }
            
            // ตรวจสอบการใช้ Hash โดยไม่มี import
            if (preg_match('/\\\\Hash::|Hash::/', $content) && !Str::contains($content, 'use Illuminate\Support\Facades\Hash;')) {
                $problems[$migrationPath][] = "ใช้ Hash แต่ไม่มีการ import: use Illuminate\Support\Facades\Hash;";
                $problemsFound = true;
            }
        }
        
        if ($problemsFound) {
            $this->warn("พบปัญหาในไฟล์ migration:");
            
            foreach ($problems as $path => $fileProblems) {
                $filename = basename($path);
                $this->line("- $filename:");
                
                foreach ($fileProblems as $problem) {
                    $this->line("  • $problem");
                }
                
                // แก้ไขปัญหาอัตโนมัติหากเลือก option --fix
                if ($this->option('fix')) {
                    $this->fixMigration($path, $fileProblems);
                    $this->info("  ✓ แก้ไขปัญหาแล้ว");
                }
            }
            
            if (!$this->option('fix')) {
                $this->line("\nรันคำสั่งด้วย --fix เพื่อแก้ไขปัญหาโดยอัตโนมัติ");
            }
            
            return 1;
        } else {
            $this->info("ตรวจสอบเสร็จสิ้น ไม่พบปัญหา");
            return 0;
        }
    }
    
    /**
     * แก้ไขไฟล์ migration ที่มีปัญหา
     */
    private function fixMigration(string $path, array $problems): void
    {
        $content = file_get_contents($path);
        $imports = [];
        
        // รวบรวม imports ที่ต้องเพิ่ม
        foreach ($problems as $problem) {
            if (preg_match('/use (.*);/', $problem, $matches)) {
                $imports[] = $matches[1];
            }
        }
        
        // เพิ่ม imports ที่จำเป็นลงในไฟล์
        if (!empty($imports)) {
            $useStatements = '';
            foreach ($imports as $import) {
                $useStatements .= "use $import;\n";
            }
            
            // เพิ่ม use statements หลัง use ตัวสุดท้ายที่มีอยู่
            if (preg_match('/use [^;]+;(?:\s*use [^;]+;)*/', $content, $matches, PREG_OFFSET_CAPTURE)) {
                $lastUseEndPos = $matches[0][1] + strlen($matches[0][0]);
                $content = substr($content, 0, $lastUseEndPos) . "\n" . $useStatements . substr($content, $lastUseEndPos);
            } else {
                // หรือเพิ่มหลัง <?php
                $content = preg_replace('/(<\?php)/', '$1' . "\n\n" . $useStatements, $content);
            }
            
            file_put_contents($path, $content);
        }
    }
}
