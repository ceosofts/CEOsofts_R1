<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixTranslationsUniqueConstraints extends Command
{
    protected $signature = 'db:fix-translations-constraints';

    protected $description = 'แก้ไข unique constraints ในตาราง translations';

    public function handle()
    {
        $this->info('เริ่มแก้ไข unique constraints ในตาราง translations...');

        if (!Schema::hasTable('translations')) {
            $this->error('ไม่พบตาราง translations');
            return 1;
        }

        // แสดงข้อมูล indexes ทั้งหมดในตาราง
        $indexes = $this->getTableIndexes('translations');
        $this->info('Indexes ทั้งหมดในตาราง translations:');
        foreach ($indexes as $index) {
            $this->line("- {$index->Key_name}: {$index->Column_name} (ประเภท: " . ($index->Non_unique ? 'ไม่ unique' : 'unique') . ")");
        }

        // แก้ไข unique constraints ที่มีปัญหา
        $this->fixUniqueConstraints();

        // ตรวจสอบ duplicates
        $this->checkDuplicates();

        $this->info('เสร็จสิ้นการแก้ไข unique constraints');
        return 0;
    }

    private function getTableIndexes($table)
    {
        return DB::select("SHOW INDEX FROM {$table}");
    }

    private function fixUniqueConstraints()
    {
        // ลบ index เดิมที่มีปัญหา
        $indexesToDrop = [
            'translations_unique_fields',
            'translations_translatable_type_translatable_id_locale_field_unique',
            'translations_unique_identity'
        ];

        foreach ($indexesToDrop as $index) {
            try {
                DB::statement("ALTER TABLE translations DROP INDEX {$index}");
                $this->info("ลบ index {$index} สำเร็จ");
            } catch (\Exception $e) {
                $this->warn("ไม่พบ index {$index} หรือไม่สามารถลบได้: " . $e->getMessage());
            }
        }

        // สร้าง index ใหม่ที่ถูกต้อง
        try {
            DB::statement('ALTER TABLE translations ADD UNIQUE INDEX translations_company_locale_group_key_unique (company_id, locale, `group`, `key`)');
            $this->info("สร้าง index translations_company_locale_group_key_unique สำเร็จ");
        } catch (\Exception $e) {
            $this->error("ไม่สามารถสร้าง index ได้: " . $e->getMessage());

            // ถ้าไม่สามารถสร้าง index ได้เพราะมีข้อมูลซ้ำ ให้แก้ไขข้อมูลซ้ำก่อน
            if (str_contains($e->getMessage(), 'Duplicate')) {
                $this->info("กำลังแก้ไขข้อมูลซ้ำ...");
                $this->fixDuplicateData();
                
                // พยายามสร้าง index อีกครั้ง
                try {
                    DB::statement('ALTER TABLE translations ADD UNIQUE INDEX translations_company_locale_group_key_unique (company_id, locale, `group`, `key`)');
                    $this->info("สร้าง index translations_company_locale_group_key_unique สำเร็จหลังจากแก้ไขข้อมูลซ้ำ");
                } catch (\Exception $e2) {
                    $this->error("ยังไม่สามารถสร้าง index ได้: " . $e2->getMessage());
                }
            }
        }
    }

    private function fixDuplicateData()
    {
        // หาข้อมูลซ้ำโดยใช้ company_id, locale, group, key เป็นเกณฑ์
        $duplicates = DB::select("
            SELECT company_id, locale, `group`, `key`, COUNT(*) as count
            FROM translations
            GROUP BY company_id, locale, `group`, `key`
            HAVING COUNT(*) > 1
        ");

        $this->info("พบข้อมูลซ้ำทั้งหมด " . count($duplicates) . " รายการ");

        foreach ($duplicates as $duplicate) {
            // ดึงรายการที่ซ้ำกัน
            $rows = DB::select("
                SELECT id, company_id, locale, `group`, `key`, value, updated_at
                FROM translations
                WHERE company_id = ? AND locale = ? AND `group` = ? AND `key` = ?
                ORDER BY updated_at DESC
            ", [$duplicate->company_id, $duplicate->locale, $duplicate->group, $duplicate->key]);

            // เก็บรายการแรก (ล่าสุด) และลบที่เหลือ
            $keepId = $rows[0]->id;
            $this->info("เก็บรายการ ID {$keepId} และลบรายการซ้ำอื่นๆ");

            for ($i = 1; $i < count($rows); $i++) {
                DB::table('translations')->where('id', $rows[$i]->id)->delete();
                $this->line("ลบรายการ ID {$rows[$i]->id}");
            }
        }
    }

    private function checkDuplicates()
    {
        // ตรวจสอบว่ายังมีข้อมูลซ้ำอยู่หรือไม่
        $duplicates = DB::select("
            SELECT company_id, locale, `group`, `key`, COUNT(*) as count
            FROM translations
            GROUP BY company_id, locale, `group`, `key`
            HAVING COUNT(*) > 1
        ");

        if (count($duplicates) > 0) {
            $this->warn("ยังพบข้อมูลซ้ำอยู่ " . count($duplicates) . " รายการ");
            foreach ($duplicates as $duplicate) {
                $this->line("- Company ID {$duplicate->company_id}, Locale: {$duplicate->locale}, Group: {$duplicate->group}, Key: {$duplicate->key} (จำนวน: {$duplicate->count})");
            }
        } else {
            $this->info("ไม่พบข้อมูลซ้ำแล้ว");
        }
    }
}
