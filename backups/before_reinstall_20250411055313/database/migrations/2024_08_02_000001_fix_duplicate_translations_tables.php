<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // อัปเดตข้อมูลในตาราง migrations
        $migrationsToUpdate = [
            // ไฟล์ที่ต้องการแก้ไข (ซึ่งอาจสร้างตาราง translation ซ้ำซ้อน)
            '2024_08_01_000066_create_translations_table',
            '2024_08_01_000067_update_translations_table',
            '2024_08_01_000068_add_key_column_to_translations_table',
            '2024_08_01_000069_add_translatable_fields_to_translations_table',
            '2024_08_01_000070_update_translatable_id_in_translations_table',
            '2024_08_01_000071_add_field_and_deleted_at_to_translations_table',
            '2024_08_01_000072_fix_translations_unique_constraints',
        ];

        // ทำเครื่องหมายเหล่านี้ว่า migration แล้ว เพื่อระบบจะได้ไม่พยายามรันอีก
        foreach ($migrationsToUpdate as $migration) {
            // ลบออกก่อนถ้ามีอยู่แล้ว
            DB::table('migrations')->where('migration', $migration)->delete();
            
            // แล้วเพิ่มเข้าไปใหม่ โดยกำหนด batch ให้เป็น batch ล่าสุด
            $latestBatch = DB::table('migrations')->max('batch') ?: 1;
            
            DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => $latestBatch,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ไม่จำเป็นต้องทำสิ่งใดในขั้นตอนการถอยกลับ
    }
};
