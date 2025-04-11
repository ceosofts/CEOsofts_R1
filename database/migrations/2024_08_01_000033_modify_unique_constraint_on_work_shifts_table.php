<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // แก้ไข unique constraint ของ code ในตาราง work_shifts
        // เปลี่ยนจาก unique global ให้เป็น unique ในขอบเขตของ company_id
        
        try {
            Schema::table('work_shifts', function (Blueprint $table) {
                // แทนที่จะใช้ dropUnique ใช้วิธีสร้าง SQL โดยตรง
                DB::statement('ALTER TABLE work_shifts DROP INDEX work_shifts_code_unique');
            });
        } catch (\Exception $e) {
            // ถ้า index ไม่มี ข้าม error นี้ไป
            // แต่ให้ log ไว้เพื่อตรวจสอบ
            Log::info('Unable to drop index work_shifts_code_unique: ' . $e->getMessage());
        }
        
        try {
            // สร้าง unique constraint ใหม่ ที่รวม company_id เข้าไปด้วย
            Schema::table('work_shifts', function (Blueprint $table) {
                $table->unique(['company_id', 'code'], 'work_shifts_company_id_code_unique');
            });
        } catch (\Exception $e) {
            // ถ้า index มีอยู่แล้ว หรือมีข้อผิดพลาดอื่น ให้ log ข้อความนั้น
            Log::warning('Unable to create unique constraint work_shifts_company_id_code_unique: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            // ลบ unique constraint ใหม่
            Schema::table('work_shifts', function (Blueprint $table) {
                $table->dropUnique('work_shifts_company_id_code_unique');
            });
        } catch (\Exception $e) {
            Log::info('Unable to drop index work_shifts_company_id_code_unique: ' . $e->getMessage());
        }
        
        try {
            // สร้าง unique constraint เดิม กลับคืนมา
            Schema::table('work_shifts', function (Blueprint $table) {
                $table->unique('code', 'work_shifts_code_unique');
            });
        } catch (\Exception $e) {
            Log::warning('Unable to create unique constraint work_shifts_code_unique: ' . $e->getMessage());
        }
    }
};
