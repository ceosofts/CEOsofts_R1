<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixDatabaseSchema extends Command
{
    protected $signature = 'db:fix-schema {table? : ชื่อตารางที่ต้องการแก้ไข}';

    protected $description = 'ตรวจสอบและแก้ไขโครงสร้างตารางในฐานข้อมูล';

    public function handle()
    {
        $table = $this->argument('table');

        if ($table) {
            $this->fixTable($table);
        } else {
            // แก้ไขทุกตารางที่มีปัญหา
            $this->fixTranslationsTable();
            $this->fixFileAttachmentsTable();
            
            $this->info('ตรวจสอบและแก้ไขโครงสร้างตารางเสร็จสิ้น!');
        }
    }

    /**
     * แก้ไขตารางตามชื่อที่ระบุ
     */
    private function fixTable($tableName)
    {
        if (!Schema::hasTable($tableName)) {
            $this->error("ไม่พบตาราง: {$tableName}");
            return;
        }

        $this->info("กำลังตรวจสอบตาราง: {$tableName}");
        $columns = Schema::getColumnListing($tableName);
        
        $this->line("คอลัมน์ที่มีอยู่: " . implode(', ', $columns));

        // ตรวจสอบว่าเป็นตารางใด และแก้ไขตามความเหมาะสม
        switch ($tableName) {
            case 'translations':
                $this->fixTranslationsTable();
                break;
            
            case 'file_attachments':
                $this->fixFileAttachmentsTable();
                break;
                
            default:
                $this->warn("ไม่มีการกำหนดวิธีแก้ไขสำหรับตาราง: {$tableName}");
                break;
        }
    }

    /**
     * แก้ไขตาราง translations
     */
    private function fixTranslationsTable()
    {
        if (!Schema::hasTable('translations')) {
            $this->error("ไม่พบตาราง translations");
            return;
        }

        $this->info("กำลังตรวจสอบตาราง translations");
        $columns = Schema::getColumnListing('translations');
        
        // คอลัมน์ที่จำเป็นสำหรับตาราง translations
        $requiredColumns = [
            'company_id', 'locale', 'group', 'key', 'field', 'value', 
            'translatable_type', 'translatable_id', 'deleted_at'
        ];
        
        $missingColumns = array_diff($requiredColumns, $columns);
        
        if (count($missingColumns) > 0) {
            $this->warn("พบคอลัมน์ที่ขาดหายไป: " . implode(', ', $missingColumns));
            
            if ($this->confirm('ต้องการเพิ่มคอลัมน์ที่ขาดหายไปหรือไม่?')) {
                $this->call('migrate', [
                    '--path' => '/database/migrations/2024_08_02_000002_add_missing_columns_to_translations_table.php'
                ]);
            }
        } else {
            $this->info("ตาราง translations มีคอลัมน์ครบถ้วน");
        }
    }

    /**
     * แก้ไขตาราง file_attachments
     */
    private function fixFileAttachmentsTable()
    {
        if (!Schema::hasTable('file_attachments')) {
            $this->error("ไม่พบตาราง file_attachments");
            return;
        }

        $this->info("กำลังตรวจสอบตาราง file_attachments");
        $columns = Schema::getColumnListing('file_attachments');
        
        if (!in_array('deleted_at', $columns)) {
            $this->warn("ไม่พบคอลัมน์ deleted_at ในตาราง file_attachments");
            
            if ($this->confirm('ต้องการเพิ่มคอลัมน์ deleted_at หรือไม่?')) {
                $this->call('migrate', [
                    '--path' => '/database/migrations/2024_08_02_000003_add_deleted_at_to_file_attachments_table.php'
                ]);
            }
        } else {
            $this->info("ตาราง file_attachments มีคอลัมน์ deleted_at แล้ว");
        }
    }
}
