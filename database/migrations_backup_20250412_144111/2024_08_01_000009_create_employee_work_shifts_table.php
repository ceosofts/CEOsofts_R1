<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migration.
     * รวมการทำงานจากไฟล์:
     * - 2024_08_01_000034_add_missing_columns_to_employee_work_shifts_table.php
     * - 2024_08_01_000035_add_deleted_at_to_employee_work_shifts_table.php
     * - 2024_08_01_000036_add_effective_date_to_employee_work_shifts_table.php
     */
    public function up(): void
    {
        Schema::create('employee_work_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('work_shift_id')->constrained()->onDelete('cascade');
            $table->date('effective_date'); // จากไฟล์ add_effective_date_to_employee_work_shifts_table.php
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(true);

            // คอลัมน์เพิ่มเติมจาก add_missing_columns_to_employee_work_shifts_table.php
            $table->text('notes')->nullable(); // บันทึกเพิ่มเติม
            $table->string('status', 20)->default('active'); // สถานะ (active, pending, expired)
            $table->boolean('is_override')->default(false); // เป็นการ override ตารางปกติหรือไม่
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // ผู้สร้างรายการ
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete(); // ผู้แก้ไขล่าสุด
            $table->date('approved_date')->nullable(); // วันที่อนุมัติ
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete(); // ผู้อนุมัติ
            $table->json('metadata')->nullable(); // ข้อมูลเพิ่มเติม

            $table->timestamps();
            $table->softDeletes(); // จาก add_deleted_at_to_employee_work_shifts_table.php

            // Indexes
            $table->index('employee_id');
            $table->index('work_shift_id');
            $table->index('effective_date');
            $table->index('is_current');
            $table->index('status'); // เพิ่ม index สำหรับคอลัมน์ใหม่
            $table->index('created_by'); // เพิ่ม index สำหรับคอลัมน์ใหม่
            $table->index('approved_by'); // เพิ่ม index สำหรับคอลัมน์ใหม่
            $table->index('deleted_at'); // เพิ่ม index สำหรับ soft delete

            // Unique constraint with a shorter name
            $table->unique(['employee_id', 'work_shift_id', 'effective_date'], 'ewshifts_empid_shiftid_date_unique');
        });

        // เพิ่มโค้ดจาก add_effective_date_to_employee_work_shifts_table.php
        // โค้ดนี้จะทำงานถ้ามีการแก้ไขตารางที่มีอยู่แล้ว และมีคอลัมน์ work_date เดิม
        try {
            if (Schema::hasTable('employee_work_shifts') && Schema::hasColumn('employee_work_shifts', 'work_date') && Schema::hasColumn('employee_work_shifts', 'effective_date')) {
                // ย้ายข้อมูลจาก work_date ไปยัง effective_date ถ้า effective_date เป็น null
                DB::statement("UPDATE employee_work_shifts SET effective_date = work_date WHERE effective_date IS NULL");
                Log::info('ย้ายข้อมูลจาก work_date ไปยัง effective_date เรียบร้อยแล้ว');
            }
        } catch (\Exception $e) {
            Log::warning('ไม่สามารถย้ายข้อมูลจาก work_date ไปยัง effective_date: ' . $e->getMessage());
        }

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->integer('late_minutes')->default(0);
            $table->integer('overtime_minutes')->default(0);
            $table->string('status', 20)->default('present');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('company_id');
            $table->index('employee_id');
            $table->index('date');
            $table->index('status');

            // Unique constraint
            $table->unique(['employee_id', 'date']);
        });

        Log::info('สร้างตาราง employee_work_shifts และ attendances เรียบร้อยแล้ว รวมถึงการเพิ่ม effective_date');
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('employee_work_shifts');
    }
};
