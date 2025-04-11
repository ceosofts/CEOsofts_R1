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
        if (Schema::hasTable('translations') && Schema::hasColumn('translations', 'translatable_id')) {
            // แก้ไขคอลัมน์ translatable_id ให้มีค่าเริ่มต้นเป็น 0
            DB::statement('ALTER TABLE translations MODIFY translatable_id BIGINT UNSIGNED DEFAULT 0');
            
            // อัพเดตข้อมูลเดิมในตาราง
            DB::statement('UPDATE translations SET translatable_id = 0 WHERE translatable_id IS NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('translations') && Schema::hasColumn('translations', 'translatable_id')) {
            // เปลี่ยนกลับไปเป็น NULL ได้
            DB::statement('ALTER TABLE translations MODIFY translatable_id BIGINT UNSIGNED NULL');
        }
    }
};
