<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ตรวจสอบและแก้ไขไฟล์ migration ที่อาจมีปัญหา
        $this->fixConflictingMigrations();
        
        // ลงรายการ migration ที่จะไม่ถูกรันเพื่อให้ระบบเข้าใจว่าไฟล์เหล่านี้ได้ถูกรันไปแล้ว
        $this->markMigrationsAsRun();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ไม่จำเป็นต้องทำอะไรเพราะเป็นการแก้ไขที่ควรคงอยู่ถาวร
    }
    
    /**
     * แก้ไขไฟล์ migration ที่มีปัญหา
     */
    private function fixConflictingMigrations()
    {
        // รายชื่อไฟล์ migration ที่อาจมีปัญหา
        $problematicFiles = [
            'database/migrations/2024_08_01_000066_create_translations_table.php',
            'database/migrations/2024_08_01_000022_create_translations_and_file_attachments_tables.php'
        ];
        
        foreach ($problematicFiles as $file) {
            $path = base_path($file);
            if (File::exists($path)) {
                $currentContent = File::get($path);
                
                // หาและแทนที่โค้ดในส่วน up() ที่สร้างตารางโดยไม่ตรวจสอบ
                $updatedContent = $this->addTableExistsCheck($currentContent, 'translations');
                $updatedContent = $this->addTableExistsCheck($updatedContent, 'file_attachments');
                
                // บันทึกการเปลี่ยนแปลง
                if ($currentContent !== $updatedContent) {
                    File::put($path, $updatedContent);
                    echo "แก้ไขไฟล์ {$file} สำเร็จ\n";
                } else {
                    echo "ไม่มีการเปลี่ยนแปลงในไฟล์ {$file}\n";
                }
            } else {
                echo "ไม่พบไฟล์ {$file}\n";
            }
        }
    }
    
    /**
     * แก้ไข content ของไฟล์ migration โดยเพิ่มการตรวจสอบการมีอยู่ของตาราง
     */
    private function addTableExistsCheck($content, $tableName)
    {
        // กรณีที่มีการสร้างตารางโดยตรง (Schema::create)
        $pattern = "/Schema::create\s*\(\s*['\"]".$tableName."['\"]\s*,\s*function\s*\(\s*Blueprint\s*\\\$table\s*\)\s*\{/";
        $replacement = "if (!Schema::hasTable('".$tableName."')) {".PHP_EOL.
                     "            Schema::create('".$tableName."', function (Blueprint \$table) {";
        $content = preg_replace($pattern, $replacement, $content);
        
        // ปิด block if ที่เพิ่มเข้าไป
        $pattern = "/\}\)\s*;(\s*\/\/\s*สร้างตาราง\s*".$tableName."|\s*\/\/\s*Create\s*".$tableName."\s*table|\s*$)/";
        $replacement = "});\n        } else {\n            echo \"ตาราง {$tableName} มีอยู่แล้ว\\n\";\n        }$1";
        $content = preg_replace($pattern, $replacement, $content);
        
        return $content;
    }
    
    /**
     * ทำเครื่องหมายว่า migration เหล่านี้ได้ถูกรันไปแล้ว
     */
    private function markMigrationsAsRun()
    {
        // รายชื่อ migration ที่มีปัญหาและควรทำเครื่องหมายว่าได้ถูกรันแล้ว
        $migrations = [
            '2024_08_01_000066_create_translations_table',
            '2024_08_01_000067_update_translations_table',
            '2024_08_01_000068_add_key_column_to_translations_table',
            '2024_08_01_000069_add_translatable_fields_to_translations_table',
            '2024_08_01_000070_update_translatable_id_in_translations_table',
            '2024_08_01_000071_add_field_and_deleted_at_to_translations_table',
            '2024_08_01_000072_fix_translations_unique_constraints',
            '2024_08_01_000073_recreate_translations_table',
            '2024_08_01_000074_cleanup_translations_migrations',
            '2024_08_01_000075_setup_translations_table',
            '2024_08_01_000076_create_file_attachments_table',
            '2024_08_02_000001_fix_duplicate_translations_tables',
            '2024_08_02_000005_add_required_columns_to_file_attachments_table',
        ];
        
        // ดึงค่า batch ล่าสุด
        $latestBatch = DB::table('migrations')->max('batch') ?: 1;
        $latestBatch++;
        
        foreach ($migrations as $migration) {
            // ตรวจสอบว่ามีรายการนี้อยู่แล้วหรือไม่
            $exists = DB::table('migrations')
                ->where('migration', $migration)
                ->exists();
            
            if (!$exists) {
                DB::table('migrations')->insert([
                    'migration' => $migration,
                    'batch' => $latestBatch
                ]);
                echo "ทำเครื่องหมายว่ารายการ {$migration} ได้ถูกรันแล้ว\n";
            } else {
                echo "รายการ {$migration} มีอยู่ในตาราง migrations แล้ว\n";
            }
        }
    }
};
