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
     * รวมการทำงานจากไฟล์:
     * - 2024_08_01_000025_update_leave_types_table.php
     * - 2024_08_01_000031_add_missing_columns_to_leave_types_table.php
     */
    public function up(): void
    {
        // ตรวจสอบว่ามีข้อมูลเดิมหรือไม่
        $existingLeaveTypes = [];
        if (Schema::hasTable('leave_types')) {
            try {
                // สำรองข้อมูล
                $existingLeaveTypes = DB::table('leave_types')->get()->toArray();
                Log::info('สำรองข้อมูล leave_types จำนวน ' . count($existingLeaveTypes) . ' รายการ');

                // ลบตารางเดิม
                Schema::dropIfExists('leave_types');
            } catch (\Exception $e) {
                Log::warning('ไม่สามารถสำรองข้อมูล leave_types: ' . $e->getMessage());
                Schema::dropIfExists('leave_types');
            }
        }

        // สร้างตาราง leave_types
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code', 50)->nullable();
            $table->text('description')->nullable();
            $table->decimal('annual_allowance', 8, 2)->default(0);
            $table->boolean('is_paid')->default(true);
            $table->boolean('requires_approval')->default(true);
            $table->boolean('is_active')->default(true);

            // คอลัมน์เพิ่มเติมจาก update_leave_types_table.php
            $table->integer('max_consecutive_days')->default(0); // จำนวนวันติดต่อกันสูงสุดที่อนุญาต
            $table->boolean('allow_half_day')->default(true); // อนุญาตให้ลาครึ่งวันหรือไม่
            $table->integer('min_notice_days')->default(0); // จำนวนวันขั้นต่ำที่ต้องแจ้งล่วงหน้า
            $table->json('approval_levels')->nullable(); // ระดับการอนุมัติ (หลายระดับ)
            $table->boolean('requires_documents')->default(false); // ต้องแนบเอกสารหรือไม่
            $table->string('color', 20)->nullable(); // สีสำหรับแสดงในปฏิทิน
            $table->string('icon', 50)->nullable(); // ไอคอนสำหรับแสดงในหน้า UI
            $table->integer('carry_forward_days')->default(0); // จำนวนวันที่สามารถยกยอดไปปีถัดไป
            $table->boolean('is_compensated_on_termination')->default(false); // ได้รับการชดเชยเมื่อสิ้นสุดการจ้างหรือไม่
            $table->boolean('can_take_advance')->default(false); // สามารถลาล่วงหน้าได้หรือไม่

            // คอลัมน์เพิ่มเติมจาก add_missing_columns_to_leave_types_table.php
            $table->decimal('max_annual_carryover', 8, 2)->default(0); // จำนวนวันสูงสุดที่ยกยอดได้ต่อปี
            $table->integer('carryover_expiration_months')->default(3); // วันยกยอดหมดอายุใน x เดือน
            $table->string('accrual_type', 20)->default('annual'); // รูปแบบการสะสม (annual, monthly, bi-monthly, quarterly)
            $table->decimal('accrual_rate', 8, 4)->default(0); // อัตราการสะสมต่อรอบ
            $table->integer('accrual_milestone_months')->default(0); // จำนวนเดือนกว่าจะได้สิทธิ์
            $table->text('policy_text')->nullable(); // ข้อความนโยบายการลา
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // ผู้สร้างประเภทการลา
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete(); // ผู้แก้ไขล่าสุด

            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('company_id');
            $table->index('name');
            $table->index('code');
            $table->index('is_active');
            $table->index('created_by'); // index สำหรับคอลัมน์ผู้สร้าง

            // Unique constraint
            $table->unique(['company_id', 'code']);
        });

        // สร้างตาราง leaves (ไม่เปลี่ยนแปลง)
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('leave_type_id')->constrained()->onDelete('restrict');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('days', 5, 2);
            $table->text('reason')->nullable();
            $table->string('status', 20)->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('company_id');
            $table->index('employee_id');
            $table->index('leave_type_id');
            $table->index('start_date');
            $table->index('end_date');
            $table->index('status');
            $table->index('approved_by');
        });

        Log::info('สร้างตาราง leave_types และ leaves เรียบร้อยแล้ว รวมถึงคอลัมน์เพิ่มเติมทั้งหมด');

        // นำข้อมูลกลับคืน (ถ้ามี)
        if (!empty($existingLeaveTypes)) {
            try {
                foreach ($existingLeaveTypes as $leaveType) {
                    $leaveTypeArr = (array) $leaveType;

                    // ลบ primary key เพื่อให้ auto-increment ทำงานได้ถูกต้อง
                    if (isset($leaveTypeArr['id'])) {
                        unset($leaveTypeArr['id']);
                    }

                    // เพิ่มค่าเริ่มต้นสำหรับคอลัมน์ใหม่
                    if (!isset($leaveTypeArr['max_annual_carryover'])) {
                        $leaveTypeArr['max_annual_carryover'] = $leaveTypeArr['carry_forward_days'] ?? 0;
                    }

                    if (!isset($leaveTypeArr['carryover_expiration_months'])) {
                        $leaveTypeArr['carryover_expiration_months'] = 3;
                    }

                    if (!isset($leaveTypeArr['accrual_type'])) {
                        $leaveTypeArr['accrual_type'] = 'annual';
                    }

                    DB::table('leave_types')->insert($leaveTypeArr);
                }

                Log::info('นำข้อมูล leave_types กลับคืนเรียบร้อยแล้ว');
            } catch (\Exception $e) {
                Log::error('ไม่สามารถนำข้อมูล leave_types กลับคืน: ' . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
        Schema::dropIfExists('leave_types');
    }
};
