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
     * ในไฟล์นี้ได้รวมการปรับปรุงจากไฟล์ต่อไปนี้:
     * - 2024_08_01_000029_add_missing_columns_to_employees_table.php
     * - 2024_08_01_000030_modify_uuid_column_in_employees_table.php
     * - 2024_08_01_000049_add_missing_columns_to_employees_table.php
     */
    public function up(): void
    {
        // สำรองข้อมูลเดิม (ถ้ามี)
        $existingEmployees = [];
        if (Schema::hasTable('employees')) {
            try {
                $existingEmployees = DB::table('employees')->get()->toArray();
                Log::info('สำรองข้อมูล employees จำนวน ' . count($existingEmployees) . ' รายการ');
                Schema::dropIfExists('employees');
            } catch (\Exception $e) {
                Log::warning('ไม่สามารถสำรองข้อมูล employees: ' . $e->getMessage());
                Schema::dropIfExists('employees');
            }
        }

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            // ปรับแต่งจาก modify_uuid_column_in_employees_table.php 
            // เปลี่ยนจาก ulid('uuid') เป็น string('uuid')
            $table->string('uuid', 36)->unique();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('restrict');
            $table->foreignId('position_id')->constrained()->onDelete('restrict');
            $table->foreignId('branch_office_id')->nullable()->constrained()->onDelete('set null');
            $table->string('employee_code', 50);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->date('birthdate')->nullable();
            $table->date('hire_date');
            $table->date('termination_date')->nullable();
            $table->string('id_card_number', 50)->nullable();
            $table->string('tax_id', 50)->nullable();
            $table->string('bank_account', 50)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->string('profile_image')->nullable();
            $table->string('status', 20)->default('active');

            // คอลัมน์เพิ่มเติมจาก add_missing_columns_to_employees_table.php
            $table->string('title', 20)->nullable(); // คำนำหน้า (นาย, นาง, นางสาว)
            $table->string('nickname', 50)->nullable(); // ชื่อเล่น
            $table->string('gender', 10)->nullable(); // เพศ (male, female, other)
            $table->string('marital_status', 20)->nullable(); // สถานะการสมรส
            $table->string('nationality', 50)->nullable(); // สัญชาติ
            $table->string('religion', 50)->nullable(); // ศาสนา
            $table->string('blood_type', 5)->nullable(); // กรุ๊ปเลือด
            $table->decimal('height', 5, 2)->nullable(); // ส่วนสูง (ซม.)
            $table->decimal('weight', 5, 2)->nullable(); // น้ำหนัก (กก.)
            $table->text('medical_conditions')->nullable(); // โรคประจำตัว/ข้อมูลสุขภาพ
            $table->string('education_level', 50)->nullable(); // ระดับการศึกษา
            $table->string('education_institute')->nullable(); // สถาบันการศึกษา
            $table->string('education_major')->nullable(); // สาขาวิชา
            $table->integer('years_experience')->nullable(); // ประสบการณ์ทำงาน (ปี)
            $table->foreignId('manager_id')->nullable()->constrained('employees')->onDelete('set null'); // หัวหน้า/ผู้จัดการ
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // ผู้ใช้งานในระบบ

            // คอลัมน์เพิ่มเติมจาก add_missing_columns_to_employees_table.php (ไฟล์ที่ 49)
            $table->string('employee_type', 30)->nullable(); // ประเภทพนักงาน (full-time, part-time, contract, intern, etc.)
            $table->date('probation_end_date')->nullable(); // วันสิ้นสุดทดลองงาน
            $table->string('work_permit_number', 50)->nullable(); // เลขที่ใบอนุญาตทำงาน (สำหรับคนต่างด้าว)
            $table->date('work_permit_expiry')->nullable(); // วันหมดอายุใบอนุญาตทำงาน
            $table->string('passport_number', 50)->nullable(); // เลขที่หนังสือเดินทาง
            $table->date('passport_expiry')->nullable(); // วันหมดอายุหนังสือเดินทาง
            $table->string('visa_type', 30)->nullable(); // ประเภทวีซ่า
            $table->date('visa_expiry')->nullable(); // วันหมดอายุวีซ่า
            $table->string('social_security_number', 50)->nullable(); // เลขที่ประกันสังคม
            $table->string('tax_filing_status', 30)->nullable(); // สถานะการยื่นภาษี
            $table->json('skills')->nullable(); // ทักษะความสามารถ
            $table->json('certificates')->nullable(); // ใบรับรอง/ใบประกาศนียบัตร
            $table->json('previous_employment')->nullable(); // ประวัติการทำงานก่อนหน้า
            $table->boolean('has_company_email')->default(false); // มีอีเมลบริษัทหรือไม่
            $table->string('company_email')->nullable(); // อีเมลบริษัท

            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable();

            // Indexes
            $table->index('company_id');
            $table->index('department_id');
            $table->index('position_id');
            $table->index('employee_code');
            $table->index('email');
            $table->index('phone');
            $table->index('status');
            $table->index('hire_date');
            $table->index(['first_name', 'last_name']);

            // Indexes เพิ่มเติม
            $table->index('manager_id');
            $table->index('user_id');
            $table->index('gender');
            $table->index('nationality');
            $table->index('employee_type'); // index สำหรับประเภทพนักงาน
            $table->index('probation_end_date'); // index สำหรับวันสิ้นสุดทดลองงาน
            $table->index('work_permit_expiry'); // index สำหรับวันหมดอายุใบอนุญาตทำงาน
            $table->index('visa_expiry'); // index สำหรับวันหมดอายุวีซ่า

            // Unique constraints
            $table->unique(['company_id', 'employee_code']);
            $table->unique(['company_id', 'email']);
        });

        Log::info('สร้างตาราง employees เรียบร้อยแล้ว');

        // นำข้อมูลกลับคืน (ถ้ามี)
        if (!empty($existingEmployees)) {
            try {
                foreach ($existingEmployees as $employee) {
                    $employeeData = (array) $employee;

                    // ลบ primary key เพื่อให้ auto-increment ทำงานได้ถูกต้อง
                    if (isset($employeeData['id'])) {
                        unset($employeeData['id']);
                    }

                    // ตรวจสอบและเพิ่ม UUID ถ้าจำเป็น
                    if (!isset($employeeData['uuid']) || empty($employeeData['uuid'])) {
                        $employeeData['uuid'] = (string) \Illuminate\Support\Str::uuid();
                    }

                    // เพิ่มค่าเริ่มต้นสำหรับคอลัมน์ที่เพิ่มเติม
                    if (!isset($employeeData['employee_type'])) {
                        $employeeData['employee_type'] = 'full-time';
                    }

                    if (!isset($employeeData['has_company_email'])) {
                        $employeeData['has_company_email'] = false;
                    }

                    DB::table('employees')->insert($employeeData);
                }

                Log::info('นำข้อมูล employees กลับคืนเรียบร้อยแล้ว');
            } catch (\Exception $e) {
                Log::error('ไม่สามารถนำข้อมูล employees กลับคืน: ' . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
