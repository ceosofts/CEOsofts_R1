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
     * รวมการทำงานจากไฟล์:
     * - 2024_08_01_000032_add_missing_columns_to_work_shifts_table.php
     * - 2024_08_01_000033_modify_unique_constraint_on_work_shifts_table.php
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if (!Schema::hasTable('work_shifts')) {
            Schema::create('work_shifts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->string('code', 50)->nullable()->unique(); // เพิ่มจาก add_missing_columns
                $table->text('description')->nullable(); // เพิ่มจาก add_missing_columns
                $table->time('start_time');
                $table->time('end_time');
                $table->time('break_start')->nullable();
                $table->time('break_end')->nullable();
                $table->boolean('is_night_shift')->default(false);
                $table->boolean('is_active')->default(true);
                $table->json('metadata')->nullable();

                // คอลัมน์เพิ่มเติมจาก add_missing_columns
                $table->boolean('is_default')->default(false); // กำหนดเป็น default shift
                $table->string('color', 20)->nullable(); // สีที่ใช้แสดงใน calendar
                $table->time('late_threshold')->nullable(); // เวลาที่ถือว่ามาสาย (เช่น +15 นาที)
                $table->time('early_leave_threshold')->nullable(); // เวลาที่ถือว่ากลับก่อน (เช่น -15 นาที)
                $table->json('working_days')->nullable(); // วันทำงานในสัปดาห์ [1,2,3,4,5] (จันทร์-ศุกร์)
                $table->integer('grace_period_minutes')->default(0); // ช่วงผ่อนผันเป็นนาที
                $table->string('status', 20)->default('active'); // สถานะ (active, inactive)
                $table->boolean('is_flexible')->default(false); // กำหนดเป็น flexible time
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

                $table->timestamps();
                $table->softDeletes();

                // Indexes
                $table->index('company_id');
                $table->index('name');
                $table->index('is_active');
                $table->index('is_night_shift');
                $table->index('code'); // เพิ่มจาก add_missing_columns
                $table->index('is_default'); // เพิ่มจาก add_missing_columns
                $table->index('status'); // เพิ่มจาก add_missing_columns

                // Unique constraint ที่ปรับปรุงแล้วจาก modify_unique_constraint
                // เปลี่ยนจาก unique('code') เป็น unique(['company_id', 'code'])
                $table->unique(['company_id', 'code'], 'work_shifts_company_id_code_unique');
                $table->unique(['company_id', 'name']);
            });
        }
        // กรณีมีตาราง แต่ต้องการปรับ unique constraint (จาก modify_unique_constraint)
        else {
            try {
                // สำหรับ SQLite อาจจะต้องใช้วิธีการพิเศษ เนื่องจากไม่รองรับการ drop unique ที่มีอยู่แล้ว
                if ($driver === 'sqlite') {
                    // หมายเหตุ: SQLite ไม่สามารถ ALTER TABLE DROP CONSTRAINT ได้โดยตรง
                    // ขั้นตอนตามปกติคือ: สร้างตารางใหม่, คัดลอกข้อมูล, แล้ว rename
                    Log::info('SQLite ไม่รองรับการ drop unique constraint โดยตรง - แนะนำให้สร้าง migration ที่สร้างตารางใหม่สำหรับ SQLite');
                }
                // สำหรับ MySQL สามารถใช้คำสั่งมาตรฐาน
                else if ($driver === 'mysql') {
                    // ลบ unique constraint เดิมถ้ามี
                    if (Schema::hasTable('work_shifts')) {
                        Schema::table('work_shifts', function (Blueprint $table) {
                            $table->dropUnique('work_shifts_code_unique');
                        });
                    }

                    // เพิ่ม unique constraint ใหม่
                    Schema::table('work_shifts', function (Blueprint $table) {
                        $table->unique(['company_id', 'code'], 'work_shifts_company_id_code_unique');
                    });
                }
            } catch (\Exception $e) {
                Log::warning("ไม่สามารถปรับ unique constraint: " . $e->getMessage());
            }

            // เพิ่มคอลัมน์ที่อาจขาดหายไป (จาก add_missing_columns)
            Schema::table('work_shifts', function (Blueprint $table) {
                if (!Schema::hasColumn('work_shifts', 'code')) {
                    $table->string('code', 50)->nullable()->after('name');
                    $table->index('code');
                }

                if (!Schema::hasColumn('work_shifts', 'description')) {
                    $table->text('description')->nullable()->after('code');
                }

                if (!Schema::hasColumn('work_shifts', 'is_default')) {
                    $table->boolean('is_default')->default(false)->after('is_active');
                    $table->index('is_default');
                }

                if (!Schema::hasColumn('work_shifts', 'color')) {
                    $table->string('color', 20)->nullable()->after('is_default');
                }

                if (!Schema::hasColumn('work_shifts', 'late_threshold')) {
                    $table->time('late_threshold')->nullable()->after('color');
                }

                if (!Schema::hasColumn('work_shifts', 'early_leave_threshold')) {
                    $table->time('early_leave_threshold')->nullable()->after('late_threshold');
                }

                if (!Schema::hasColumn('work_shifts', 'working_days')) {
                    $table->json('working_days')->nullable()->after('early_leave_threshold');
                }

                if (!Schema::hasColumn('work_shifts', 'grace_period_minutes')) {
                    $table->integer('grace_period_minutes')->default(0)->after('working_days');
                }

                if (!Schema::hasColumn('work_shifts', 'status')) {
                    $table->string('status', 20)->default('active')->after('grace_period_minutes');
                    $table->index('status');
                }

                if (!Schema::hasColumn('work_shifts', 'is_flexible')) {
                    $table->boolean('is_flexible')->default(false)->after('status');
                }

                if (!Schema::hasColumn('work_shifts', 'updated_by')) {
                    $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_shifts');
    }
};
