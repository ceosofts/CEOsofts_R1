<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FixSQLiteMigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sqlite:fix-migration {--skip= : ข้ามการ migrate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'แก้ไขปัญหา migrations ที่มีปัญหากับ SQLite และดำเนินการ migrate ต่อ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // ตรวจสอบว่าใช้ SQLite หรือไม่
        $driver = DB::connection()->getDriverName();
        if ($driver !== 'sqlite') {
            $this->info('คำสั่งนี้ใช้ได้เฉพาะกับ SQLite เท่านั้น');
            return 0;
        }

        $this->info('กำลังตรวจสอบและแก้ไขปัญหา SQLite migrations...');

        // ดึงข้อมูล migration ล่าสุดที่รันสำเร็จ
        $lastMigration = null;
        try {
            $lastMigration = DB::table('migrations')->orderBy('id', 'desc')->first();
            if ($lastMigration) {
                $this->info("Migration ล่าสุดที่รันสำเร็จ: {$lastMigration->migration}");
            }
        } catch (\Exception $e) {
            $this->warn("ไม่สามารถดึงข้อมูล migration: " . $e->getMessage());
        }

        // ดึงรายชื่อไฟล์ migration ที่ยังไม่ได้รัน
        $migrationFiles = File::glob(database_path('migrations/*.php'));
        $pendingMigrations = [];

        if ($lastMigration) {
            foreach ($migrationFiles as $file) {
                $filename = pathinfo($file, PATHINFO_FILENAME);
                if ($filename > $lastMigration->migration) {
                    $pendingMigrations[$filename] = $file;
                }
            }
        } else {
            foreach ($migrationFiles as $file) {
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $pendingMigrations[$filename] = $file;
            }
        }

        // แสดงรายการ migrations ที่ยังไม่ได้รัน
        ksort($pendingMigrations);
        $this->info('พบ ' . count($pendingMigrations) . ' migration ที่ยังไม่ได้รัน');

        // ตรวจสอบ migration ที่มีปัญหากับ SQLite
        $problematicFiles = [];
        foreach ($pendingMigrations as $migration => $file) {
            $content = File::get($file);

            // ตรวจสอบการใช้ MySQL-specific syntax
            if (
                str_contains($content, 'MODIFY') ||
                str_contains($content, 'ALTER TABLE') && str_contains($content, 'CHANGE') ||
                str_contains($content, 'information_schema')
            ) {
                $problematicFiles[$migration] = $file;
                $this->warn("พบปัญหากับ SQLite ใน {$migration}");
            }
        }

        // ถ้าไม่มีไฟล์ที่มีปัญหา
        if (empty($problematicFiles)) {
            $this->info('ไม่พบ migration ที่มีปัญหากับ SQLite');
            return 0;
        }

        // ทางเลือกในการแก้ไข
        $this->warn('เลือกวิธีการจัดการ migrations ที่มีปัญหา:');
        $this->line('1. ข้าม migrations ที่มีปัญหาและดำเนินการต่อ');
        $this->line('2. แก้ไขไฟล์ migrations แบบอัตโนมัติ');
        $this->line('3. ยกเลิก');

        $choice = $this->choice('เลือกทางเลือก:', ['1', '2', '3'], '1');

        if ($choice === '3') {
            $this->info('ยกเลิกการดำเนินการ');
            return 0;
        }

        if ($choice === '1') {
            // ข้าม migrations ที่มีปัญหา
            foreach ($problematicFiles as $migration => $file) {
                $this->call('migrate:skip', ['migration' => $migration]);
            }

            $this->info('ดำเนินการ migrate ต่อ...');
            $this->call('migrate');
        } else if ($choice === '2') {
            // แก้ไขไฟล์ migrations
            foreach ($problematicFiles as $migration => $file) {
                $this->fixMigrationFile($file);
            }

            $this->info('ดำเนินการ migrate ต่อ...');
            $this->call('migrate');
        }

        return 0;
    }

    /**
     * แก้ไขไฟล์ migration ให้เข้ากับ SQLite
     * 
     * @param string $file ไฟล์ที่ต้องการแก้ไข
     */
    private function fixMigrationFile(string $file): void
    {
        $content = File::get($file);
        $modified = false;
        $filename = basename($file);

        // ตรวจสอบว่ามีการเพิ่มการเช็ค driver หรือยัง
        if (!str_contains($content, '$driver = DB::connection()->getDriverName()')) {
            $driverCheck = '$driver = DB::connection()->getDriverName();';

            // แทรกหลัง up() หรือตำแหน่งที่เหมาะสม
            if (preg_match('/public function up\(\): void\s*\{/', $content, $matches, PREG_OFFSET_CAPTURE)) {
                $pos = $matches[0][1] + strlen($matches[0][0]);
                $content = substr($content, 0, $pos) . "\n        " . $driverCheck . "\n" . substr($content, $pos);
                $modified = true;
            }
        }

        // เพิ่ม import สำหรับ DB และ Log
        if (!str_contains($content, 'use Illuminate\Support\Facades\DB;')) {
            $content = preg_replace('/(use Illuminate\\\\Database\\\\Schema\\\\Blueprint;)/', "$1\nuse Illuminate\\Support\\Facades\\DB;", $content);
            $modified = true;
        }

        if (!str_contains($content, 'use Illuminate\Support\Facades\Log;') && str_contains($content, 'Log::')) {
            $content = preg_replace('/(use Illuminate\\\\Database\\\\Schema\\\\Blueprint;)/', "$1\nuse Illuminate\\Support\\Facades\\Log;", $content);
            $modified = true;
        }

        // แก้ไข MODIFY SQL ให้รองรับ SQLite
        if (str_contains($content, 'MODIFY')) {
            // เพิ่มเงื่อนไขตรวจสอบ driver ก่อนใช้ MODIFY
            $modifyPattern = '/DB::statement\([\'"]ALTER TABLE (\w+) MODIFY ([^)]+)\)/';

            // ตัวอย่างการแทนที่: 
            // DB::statement('ALTER TABLE table MODIFY column_name ...')
            // เป็น:
            // if ($driver === 'mysql') {
            //     DB::statement('ALTER TABLE table MODIFY column_name ...');
            // } else if ($driver === 'sqlite') {
            //     // SQLite ไม่สนับสนุนคำสั่ง MODIFY
            //     Log::warning('SQLite ไม่สนับสนุนการแก้ไขคอลัมน์โดยตรง');
            // }

            $replacement = 'if ($driver === \'mysql\') {
            DB::statement(\'ALTER TABLE $1 MODIFY $2);
        } else if ($driver === \'sqlite\') {
            // SQLite ไม่สนับสนุนคำสั่ง MODIFY
            Log::warning(\'SQLite ไม่สนับสนุนการแก้ไขคอลัมน์โดยตรง - กรุณาใช้วิธีอื่น\');
        }';

            $content = preg_replace($modifyPattern, $replacement, $content);
            $modified = true;
        }

        if ($modified) {
            File::put($file, $content);
            $this->info("แก้ไขไฟล์ {$filename} แล้ว");
        } else {
            $this->line("ไม่มีการเปลี่ยนแปลงในไฟล์ {$filename}");
        }
    }
}
