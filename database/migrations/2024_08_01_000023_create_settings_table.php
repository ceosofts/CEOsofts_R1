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
     * สร้างตารางการตั้งค่า (settings)
     * รวมการทำงานจากไฟล์:
     * - 2024_08_01_000055_create_settings_table_if_not_exists.php
     * - 2024_08_01_000056_add_deleted_at_to_settings_table.php
     * - 2024_08_01_000057_add_options_to_settings_table.php
     */
    public function up(): void
    {
        // ตรวจสอบก่อนว่ามีตาราง settings แล้วหรือยัง (จาก create_settings_table_if_not_exists)
        if (Schema::hasTable('settings')) {
            Log::info('ตาราง settings มีอยู่แล้ว จะทำการอัปเดตโครงสร้าง');

            // สำรองข้อมูลเดิม
            try {
                $existingData = DB::table('settings')->get();
                Log::info('สำรองข้อมูล settings จำนวน ' . count($existingData) . ' รายการ');

                // ลบตารางเดิม
                Schema::dropIfExists('settings');
            } catch (\Exception $e) {
                Log::warning('ไม่สามารถสำรองข้อมูล settings: ' . $e->getMessage());
                Schema::dropIfExists('settings');
            }
        }

        // สร้างตารางการตั้งค่า (settings)
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('group', 50);
            $table->string('key', 100);
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string');
            $table->boolean('is_public')->default(false);
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);

            // เพิ่มคอลัมน์จาก add_options_to_settings_table.php
            $table->json('options')->nullable(); // ตัวเลือกเพิ่มเติม (เช่น ค่าที่เลือกได้, min, max)
            $table->boolean('is_system')->default(false); // เป็นการตั้งค่าของระบบหรือไม่
            $table->boolean('is_hidden')->default(false); // ซ่อนจากหน้า UI หรือไม่
            $table->string('component', 50)->nullable(); // UI component ที่ใช้แสดง (text, select, radio)
            $table->string('validation', 255)->nullable(); // กฎการ validate (เช่น required|numeric|min:1)
            $table->boolean('is_required')->default(false); // จำเป็นต้องกรอกหรือไม่
            $table->string('category', 50)->nullable(); // หมวดหมู่ย่อย
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes(); // จาก add_deleted_at_to_settings_table.php

            // Indexes
            $table->index('company_id');
            $table->index(['group', 'key']);
            $table->index('is_public');
            $table->index('is_system'); // เพิ่ม index สำหรับคอลัมน์ใหม่
            $table->index('category'); // เพิ่ม index สำหรับคอลัมน์ใหม่

            // Unique constraints
            $table->unique(['company_id', 'group', 'key']);
        });

        Log::info('สร้างหรืออัปเดตตาราง settings เรียบร้อยแล้ว');

        // นำข้อมูลเดิมกลับคืนถ้ามีการสำรองไว้
        if (isset($existingData) && count($existingData) > 0) {
            try {
                foreach ($existingData as $setting) {
                    // แปลง stdClass เป็น array
                    $settingArr = (array) $setting;

                    // ลบ primary key เพื่อให้ auto-increment ทำงานได้ถูกต้อง
                    if (isset($settingArr['id'])) {
                        unset($settingArr['id']);
                    }

                    // เพิ่มค่าเริ่มต้นสำหรับคอลัมน์ใหม่ที่จำเป็น
                    if (!isset($settingArr['options'])) {
                        $settingArr['options'] = null;
                    }

                    if (!isset($settingArr['is_system'])) {
                        $settingArr['is_system'] = false;
                    }

                    if (!isset($settingArr['is_hidden'])) {
                        $settingArr['is_hidden'] = false;
                    }

                    // เพิ่มกลับเข้าฐานข้อมูล
                    DB::table('settings')->insert($settingArr);
                }

                Log::info('นำข้อมูล settings กลับคืนเรียบร้อยแล้ว');
            } catch (\Exception $e) {
                Log::error('ไม่สามารถนำข้อมูล settings กลับคืน: ' . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
