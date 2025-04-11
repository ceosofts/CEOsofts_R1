<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ข้อควรระวัง: ถ้า index ไม่มีอยู่จริง จะเกิด error
        // ให้ตรวจสอบก่อนว่ามี index นี้หรือไม่
        
        try {
            Schema::table('work_shifts', function (Blueprint $table) {
                // แทนที่จะใช้ dropUnique ใช้วิธีสร้าง SQL โดยตรง
                DB::statement('ALTER TABLE work_shifts DROP INDEX work_shifts_code_unique');
            });
        } catch (\Exception $e) {
            // ถ้า index ไม่มี ข้าม error นี้ไป
            // แต่ให้ log ไว้เพื่อตรวจสอบ
            \Log::info('Index work_shifts_code_unique does not exist: ' . $e->getMessage());
        }
        
        // สร้าง compound unique index ใหม่
        Schema::table('work_shifts', function (Blueprint $table) {
            $table->unique(['company_id', 'code'], 'work_shifts_company_id_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('work_shifts', function (Blueprint $table) {
                $table->dropUnique('work_shifts_company_id_code_unique');
            });
        } catch (\Exception $e) {
            \Log::info('Index work_shifts_company_id_code_unique does not exist: ' . $e->getMessage());
        }
        
        // สร้าง unique constraint เดิมกลับมา
        Schema::table('work_shifts', function (Blueprint $table) {
            $table->unique('code', 'work_shifts_code_unique');
        });
    }
};
