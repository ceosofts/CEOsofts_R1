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
     * สร้างตารางหน่วยวัด (units)
     * รวมการทำงานจากไฟล์:
     * - 2024_08_01_000038_add_missing_columns_to_units_table.php
     */
    public function up(): void
    {
        // สำรองข้อมูลเดิม (ถ้ามี)
        $existingUnits = [];
        if (Schema::hasTable('units')) {
            try {
                $existingUnits = DB::table('units')->get()->toArray();
                Log::info('สำรองข้อมูล units จำนวน ' . count($existingUnits) . ' รายการ');
                Schema::dropIfExists('units');
            } catch (\Exception $e) {
                Log::warning('ไม่สามารถสำรองข้อมูล units: ' . $e->getMessage());
                Schema::dropIfExists('units');
            }
        }

        // สร้างตารางหน่วยวัด (units)
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name', 50);
            $table->string('code', 10);
            $table->string('symbol', 10)->nullable();
            $table->foreignId('base_unit_id')->nullable()->references('id')->on('units')->onDelete('set null');
            $table->decimal('conversion_factor', 15, 5)->default(1.00000);
            $table->boolean('is_active')->default(true);

            // คอลัมน์เพิ่มเติมจาก add_missing_columns_to_units_table.php
            $table->text('description')->nullable(); // คำอธิบายเพิ่มเติม
            $table->string('type', 20)->default('standard'); // ประเภทหน่วย (standard, derived, etc)
            $table->string('category', 30)->nullable(); // หมวดหมู่ (length, weight, volume, etc)
            $table->boolean('is_default')->default(false); // เป็นหน่วยเริ่มต้นหรือไม่
            $table->boolean('is_system')->default(false); // เป็นหน่วยของระบบหรือไม่
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // ผู้สร้างข้อมูล
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete(); // ผู้แก้ไขล่าสุด

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('company_id');
            $table->index('name');
            $table->index('code');
            $table->index('is_active');
            $table->index('type'); // index สำหรับประเภท
            $table->index('category'); // index สำหรับหมวดหมู่
            $table->index('created_by'); // index สำหรับผู้สร้าง

            // Unique constraints
            $table->unique(['company_id', 'code']);
        });

        Log::info('สร้างตาราง units เรียบร้อยแล้ว');

        // นำข้อมูลเดิมกลับคืน (ถ้ามี)
        if (!empty($existingUnits)) {
            try {
                foreach ($existingUnits as $unit) {
                    $unitData = (array) $unit;

                    // ลบ primary key เพื่อให้ auto-increment ทำงานได้ถูกต้อง
                    if (isset($unitData['id'])) {
                        unset($unitData['id']);
                    }

                    // เพิ่มค่าเริ่มต้นสำหรับคอลัมน์ที่จำเป็น
                    if (!isset($unitData['type'])) {
                        $unitData['type'] = 'standard';
                    }

                    if (!isset($unitData['is_default'])) {
                        $unitData['is_default'] = false;
                    }

                    if (!isset($unitData['is_system'])) {
                        $unitData['is_system'] = false;
                    }

                    DB::table('units')->insert($unitData);
                }

                Log::info('นำข้อมูล units กลับคืนเรียบร้อยแล้ว');
            } catch (\Exception $e) {
                Log::error('ไม่สามารถนำข้อมูล units กลับคืน: ' . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
