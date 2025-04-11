<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        // ปรับปรุงตาราง leave_types เพื่อเพิ่มฟิลด์ตามเอกสารออกแบบ
        if (Schema::hasTable('leave_types')) {
            Schema::table('leave_types', function (Blueprint $table) {
                if (!Schema::hasColumn('leave_types', 'code')) {
                    $table->string('code', 20)->nullable()->after('name');
                    $table->index('code');
                }
                if (!Schema::hasColumn('leave_types', 'color')) {
                    $table->string('color', 7)->default('#cccccc')->after('description');
                }
                if (!Schema::hasColumn('leave_types', 'max_consecutive_days')) {
                    $table->integer('max_consecutive_days')->default(0)->after('annual_allowance');
                }
                if (!Schema::hasColumn('leave_types', 'min_advance_notice')) {
                    $table->integer('min_advance_notice')->default(0)->after('max_consecutive_days');
                }
                if (!Schema::hasColumn('leave_types', 'requires_document')) {
                    $table->boolean('requires_document')->default(false)->after('requires_approval');
                }
                if (!Schema::hasColumn('leave_types', 'count_as_work_day')) {
                    $table->boolean('count_as_work_day')->default(false)->after('is_paid');
                }
            });
            
            // ตรวจสอบและเพิ่ม unique constraint ถ้าจำเป็น
            // แทนที่จะใช้ Schema Builder หรือ DB::statement ให้ตรวจสอบด้วยคำสั่ง SQL โดยตรง
            try {
                // ดึงข้อมูล index จากฐานข้อมูลโดยตรง
                $indexes = DB::select("SHOW INDEXES FROM `leave_types` WHERE `Key_name` = 'leave_types_company_id_code_unique'");
                
                // ถ้าไม่พบ index นี้ ให้สร้างใหม่
                if (empty($indexes) && 
                    Schema::hasColumn('leave_types', 'company_id') && 
                    Schema::hasColumn('leave_types', 'code')) {
                    
                    // แน่ใจว่าไม่มี duplicate values ก่อนสร้าง unique constraint
                    $duplicates = DB::table('leave_types')
                        ->select(DB::raw('company_id, code, COUNT(*) as count'))
                        ->whereNotNull('code')
                        ->groupBy('company_id', 'code')
                        ->having('count', '>', 1)
                        ->get();
                        
                    if ($duplicates->count() > 0) {
                        // มี duplicate อยู่ ให้ log warning และข้ามการสร้าง constraint
                        foreach ($duplicates as $dupe) {
                            Log::warning("พบข้อมูลซ้ำใน leave_types: company_id = {$dupe->company_id}, code = {$dupe->code}, count = {$dupe->count}");
                        }
                    } else {
                        // ไม่มี duplicate สามารถสร้าง constraint ได้
                        DB::statement('ALTER TABLE `leave_types` ADD UNIQUE `leave_types_company_id_code_unique`(`company_id`, `code`)');
                    }
                }
            } catch (\Exception $e) {
                // เกิดข้อผิดพลาด ให้บันทึกลงใน log แต่ให้ migration ทำงานต่อไปได้
                Log::warning("ไม่สามารถจัดการ unique constraint ได้: " . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        if (Schema::hasTable('leave_types')) {
            // ลบ unique constraint ถ้ามี
            try {
                $indexes = DB::select("SHOW INDEXES FROM `leave_types` WHERE `Key_name` = 'leave_types_company_id_code_unique'");
                if (!empty($indexes)) {
                    DB::statement('ALTER TABLE `leave_types` DROP INDEX `leave_types_company_id_code_unique`');
                }
            } catch (\Exception $e) {
                Log::warning("ไม่สามารถลบ unique constraint ได้: " . $e->getMessage());
            }
            
            // ลบคอลัมน์
            Schema::table('leave_types', function (Blueprint $table) {
                $columns = [
                    'code', 'color', 'max_consecutive_days',
                    'min_advance_notice', 'requires_document', 'count_as_work_day'
                ];
                
                foreach ($columns as $column) {
                    if (Schema::hasColumn('leave_types', $column)) {
                        if ($column === 'code') {
                            $table->dropIndex(['code']);
                        }
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
