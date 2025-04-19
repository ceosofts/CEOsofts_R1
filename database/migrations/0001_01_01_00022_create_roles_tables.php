<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ข้ามการสร้างตาราง roles เนื่องจากได้ย้ายไปยังไฟล์ 0001_01_01_00021_create_permissions_tables.php แล้ว
        Log::info('การสร้างตาราง roles ได้ถูกย้ายไปยังไฟล์ 0001_01_01_00021_create_permissions_tables.php');

        // ตรวจสอบว่ามีตาราง roles หรือไม่ และตรวจสอบโครงสร้าง
        if (Schema::hasTable('roles')) {
            // ตรวจสอบว่ามีคอลัมน์ที่จำเป็นครบหรือไม่
            $requiredColumns = [
                'company_id', 'is_system_role', 'metadata', 'color', 'level', 
                'type', 'is_default', 'is_protected', 'created_by', 'updated_by'
            ];
            
            $missingColumns = [];
            foreach ($requiredColumns as $column) {
                if (!Schema::hasColumn('roles', $column)) {
                    $missingColumns[] = $column;
                }
            }
            
            if (!empty($missingColumns)) {
                Log::warning('ตาราง roles ยังขาดคอลัมน์บางส่วน: ' . implode(', ', $missingColumns));
                Log::warning('กรุณาเรียกใช้คำสั่ง php artisan migrate:fresh หรือเพิ่มคอลัมน์เหล่านี้ด้วยตนเอง');
            } else {
                Log::info('ตาราง roles มีโครงสร้างถูกต้องครบถ้วนแล้ว');
            }
        } else {
            Log::error('ไม่พบตาราง roles ซึ่งควรถูกสร้างในไฟล์ 0001_01_01_00021_create_permissions_tables.php');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ไม่ต้องทำอะไรเนื่องจากการลบตารางจะถูกจัดการโดยไฟล์ 0001_01_01_00021_create_permissions_tables.php
    }
};
