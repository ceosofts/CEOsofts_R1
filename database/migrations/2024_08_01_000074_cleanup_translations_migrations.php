<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ลบรายการ migration เดิมที่เกี่ยวกับ translations จากตาราง migrations
        if (Schema::hasTable('migrations')) {
            // รายการที่ต้องการลบ
            $migrationsToRemove = [
                '2024_08_01_000066_create_translations_table',
            ];
            
            foreach ($migrationsToRemove as $migration) {
                DB::table('migrations')->where('migration', $migration)->delete();
                echo "ลบ migration: {$migration} สำเร็จ\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ไม่ต้องทำอะไรเนื่องจากเป็นการลบข้อมูลจาก migration table
    }
};
