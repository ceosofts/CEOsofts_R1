<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FixSQLiteCompatibilityCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrations:fix-sqlite {--check : ตรวจสอบแต่ไม่แก้ไข} {--path= : ระบุพาธเฉพาะที่จะตรวจสอบ}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'แก้ไขปัญหาความเข้ากันได้ของ SQLite ในไฟล์ migrations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->option('path') ?? database_path('migrations');

        $this->info('กำลังตรวจสอบปัญหาความเข้ากันได้ของ SQLite ในไฟล์ migrations...');

        // ค้นหาไฟล์ทั้งหมด
        $files = is_dir($path) ? File::glob("{$path}/*.php") : [$path];
        $count = 0;
        $issues = 0;

        foreach ($files as $file) {
            if (!File::exists($file) || !File::isFile($file)) {
                continue;
            }

            $content = File::get($file);
            $hasChanges = false;
            $hasIssues = false;

            // ตรวจหาปัญหา MySQL-specific SQL
            $issues = $this->detectSQLiteIssues($content, $file, $hasIssues);

            if ($hasIssues) {
                $issues++;

                if (!$this->option('check')) {
                    // แก้ไขไฟล์ถ้ามีปัญหาและไม่ได้เป็นโหมด check
                    $newContent = $this->fixSQLiteCompatibility($content, $hasChanges);

                    if ($hasChanges) {
                        File::put($file, $newContent);
                        $this->line("✓ แก้ไขไฟล์ " . basename($file));
                        $count++;
                    }
                }
            }
        }

        if ($this->option('check')) {
            if ($issues > 0) {
                $this->warn("พบปัญหาความเข้ากันไม่ได้กับ SQLite ใน {$issues} ไฟล์");
                $this->line("รัน `php artisan migrations:fix-sqlite` เพื่อแก้ไขปัญหา");
            } else {
                $this->info("ไม่พบปัญหาความเข้ากันไม่ได้กับ SQLite");
            }
        } else {
            if ($count > 0) {
                $this->info("แก้ไข {$count} ไฟล์เรียบร้อยแล้ว");
            } else {
                $this->info("ไม่มีไฟล์ที่จำเป็นต้องแก้ไข");
            }
        }

        return 0;
    }

    /**
     * ตรวจหาปัญหาความเข้ากันได้กับ SQLite
     * 
     * @param string $content เนื้อหาไฟล์
     * @param string $file ชื่อไฟล์
     * @param bool &$hasIssues มีปัญหาหรือไม่
     * @return int จำนวนปัญหาที่พบ
     */
    private function detectSQLiteIssues(string $content, string $file, bool &$hasIssues): int
    {
        $issues = 0;
        $filename = basename($file);

        // ตรวจหา information_schema (MySQL-specific)
        if (Str::contains($content, 'information_schema')) {
            $this->warn("พบการใช้ information_schema (MySQL-specific) ใน {$filename}");
            $issues++;
            $hasIssues = true;
        }

        // ตรวจหาคำสั่ง MySQL-specific อื่นๆ
        $mysqlSpecific = ['ENGINE=', 'CHARACTER SET', 'COLLATE', 'AUTO_INCREMENT', 'UNSIGNED', 'ZEROFILL'];
        foreach ($mysqlSpecific as $term) {
            if (Str::contains($content, $term)) {
                $this->warn("พบคำสั่ง MySQL-specific '{$term}' ใน {$filename}");
                $issues++;
                $hasIssues = true;
            }
        }

        // ตรวจหา DB::statement ที่อาจมีปัญหา
        if (preg_match_all('/DB::statement\([\'"](.+?)[\'"]\)/s', $content, $matches)) {
            foreach ($matches[1] as $sql) {
                if (Str::contains($sql, ['ALTER TABLE', 'DROP INDEX', 'CREATE INDEX'])) {
                    $this->warn("พบคำสั่ง DB::statement ที่อาจมีปัญหากับ SQLite ใน {$filename}");
                    $issues++;
                    $hasIssues = true;
                    break;
                }
            }
        }

        return $issues;
    }

    /**
     * แก้ไขปัญหาความเข้ากันได้กับ SQLite
     * 
     * @param string $content เนื้อหาไฟล์
     * @param bool &$hasChanges มีการแก้ไขหรือไม่
     * @return string เนื้อหาที่แก้ไขแล้ว
     */
    private function fixSQLiteCompatibility(string $content, bool &$hasChanges): string
    {
        // เพิ่มการตรวจสอบ driver ก่อนใช้คำสั่ง MySQL-specific
        if (Str::contains($content, 'information_schema') && !Str::contains($content, '$driver = DB::connection()->getDriverName()')) {
            // เพิ่มการตรวจสอบ driver
            $driverCheck = "\$driver = DB::connection()->getDriverName();\n\n        // เช็ค driver เพื่อความเข้ากันได้กับ SQLite\n        if (\$driver === 'sqlite') {\n            // SQLite ไม่สนับสนุน information_schema\n            Log::info('SQLite ไม่สนับสนุน information_schema - ข้ามขั้นตอนนี้');\n            return;\n        }\n\n        ";

            // แทรกหลัง up() หรือตำแหน่งที่เหมาะสม
            if (preg_match('/public function up\(\): void\s*\{/', $content, $matches, PREG_OFFSET_CAPTURE)) {
                $pos = $matches[0][1] + strlen($matches[0][0]);
                $content = substr($content, 0, $pos) . "\n        " . $driverCheck . substr($content, $pos);
                $hasChanges = true;
            }
        }

        // แทรก import DB และ Log ถ้าจำเป็น
        if (Str::contains($content, '$driver = DB::connection()->getDriverName()') && !Str::contains($content, 'use Illuminate\Support\Facades\DB;')) {
            $content = preg_replace('/(use Illuminate\\\\[^;]+;)/', "$1\nuse Illuminate\\Support\\Facades\\DB;", $content, 1);
            $hasChanges = true;
        }

        if (Str::contains($content, 'Log::') && !Str::contains($content, 'use Illuminate\Support\Facades\Log;')) {
            $content = preg_replace('/(use Illuminate\\\\[^;]+;)/', "$1\nuse Illuminate\\Support\\Facades\\Log;", $content, 1);
            $hasChanges = true;
        }

        return $content;
    }
}
