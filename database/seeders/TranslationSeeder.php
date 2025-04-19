<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // ตรวจสอบว่าเป็น SQLite หรือไม่
            $connection = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
            $isSQLite = ($connection === 'sqlite');
            
            // ตรวจสอบว่าตาราง translations มีอยู่หรือไม่
            if (!Schema::hasTable('translations')) {
                Log::error('ไม่พบตาราง translations');
                echo "ไม่พบตาราง translations ไม่สามารถเพิ่มคำแปลได้\n";
                return;
            }
            
            // ตรวจสอบโครงสร้างตาราง
            $columns = [];
            if ($isSQLite) {
                $columnInfo = DB::select("PRAGMA table_info(translations)");
                foreach ($columnInfo as $column) {
                    $columns[] = $column->name;
                }
            } else {
                $columns = Schema::getColumnListing('translations');
            }
            
            // ตรวจสอบชื่อคอลัมน์ที่เกี่ยวข้อง
            if (!in_array('company_id', $columns)) {
                Log::error('ไม่พบคอลัมน์ company_id ในตาราง translations');
                echo "ไม่พบคอลัมน์ company_id ในตาราง translations\n";
                return;
            }
            
            // กำหนดชื่อคอลัมน์ group ที่ถูกต้อง
            $groupColumnName = in_array('translation_group', $columns) ? 'translation_group' : 'group';
            
            // คำแปลภาษาอังกฤษ
            $this->addTranslation('en', $groupColumnName, 'messages', 'welcome', 'Welcome to CEOsofts');
            $this->addTranslation('th', $groupColumnName, 'messages', 'welcome', 'ยินดีต้อนรับสู่ CEOsofts');
            
            // คำแปลปุ่ม
            $this->addTranslation('en', $groupColumnName, 'buttons', 'save', 'Save');
            $this->addTranslation('th', $groupColumnName, 'buttons', 'save', 'บันทึก');
            $this->addTranslation('en', $groupColumnName, 'buttons', 'cancel', 'Cancel');
            $this->addTranslation('th', $groupColumnName, 'buttons', 'cancel', 'ยกเลิก');
            
        } catch (\Exception $e) {
            Log::error('เกิดข้อผิดพลาดในการเพิ่มคำแปล: ' . $e->getMessage());
            $this->command->error('เกิดข้อผิดพลาดในการเพิ่มคำแปล: ' . $e->getMessage());
        }
    }
    
    /**
     * เพิ่มหรืออัพเดทคำแปล
     * 
     * @param string $locale รหัสภาษา
     * @param string $groupColumnName ชื่อคอลัมน์กลุ่ม (group หรือ translation_group)
     * @param string $group กลุ่มของคำแปล
     * @param string $key คีย์
     * @param string $value คำแปล
     */
    private function addTranslation(string $locale, string $groupColumnName, string $group, string $key, string $value): void
    {
        try {
            $company_id = 1; // บริษัทแรก
            
            // สร้าง query แบบไดนามิกที่ใช้ชื่อคอลัมน์ที่ถูกต้อง
            $columnList = ['company_id', 'locale', $groupColumnName, 'key', 'field', 'value', 'translatable_type', 'translatable_id', 'created_at', 'updated_at'];
            $valueList = [$company_id, "'$locale'", "'$group'", "'$key'", "'general'", "'$value'", "'general'", 0, "'" . now() . "'", "'" . now() . "'"];
            
            // ตรวจสอบการมีอยู่ของคำแปล
            $existingSql = "SELECT id FROM translations WHERE company_id = ? AND locale = ? AND $groupColumnName = ? AND key = ?";
            $existing = DB::select($existingSql, [$company_id, $locale, $group, $key]);
            
            if (!empty($existing)) {
                // อัพเดทคำแปลที่มีอยู่แล้ว
                $updateSql = "UPDATE translations SET value = ?, updated_at = ? WHERE id = ?";
                DB::update($updateSql, [$value, now(), $existing[0]->id]);
            } else {
                // เพิ่มคำแปลใหม่
                $insertSql = "INSERT INTO translations (" . implode(', ', $columnList) . ") VALUES (" . implode(', ', $valueList) . ")";
                DB::statement($insertSql);
            }
            
            echo "เพิ่มคำแปล: {$locale}.{$group}.{$key}\n";
        } catch (\Exception $e) {
            echo "เกิดข้อผิดพลาดในการเพิ่ม/อัปเดตคำแปล {$key}: " . $e->getMessage() . "\n";
        }
    }
}
