<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     * รวมการทำงานจากไฟล์:
     * - 0001_01_01_00044_add_parent_id_to_departments_table.php
     * - 2025_04_16_add_branch_office_id_to_departments_table.php
     */
    public function up(): void
    {
        // สร้างตาราง departments ถ้ายังไม่มี
        if (!Schema::hasTable('departments')) {
            Schema::create('departments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->foreignId('branch_office_id')->nullable()->constrained('branch_offices')->nullOnDelete(); // จากไฟล์ 2025_04_16
                $table->foreignId('parent_id')->nullable()->constrained('departments')->nullOnDelete(); // จากไฟล์ 0001_01_01_00044
                $table->string('name');
                $table->string('code')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Indexes
                $table->index('company_id');
                $table->index('branch_office_id'); // เพิ่ม index สำหรับ branch_office_id
                $table->index('parent_id'); // เพิ่ม index สำหรับ parent_id
                $table->index(['company_id', 'name']); // Combined index สำหรับการค้นหาแผนกในบริษัทเดียวกัน
            });
            
            Log::info('สร้างตาราง departments เรียบร้อยแล้ว');
        } else {
            // ถ้าตารางมีอยู่แล้ว ตรวจสอบและเพิ่มคอลัมน์ที่อาจจะยังไม่มี
            
            // ตรวจสอบและเพิ่มคอลัมน์ branch_office_id ถ้ายังไม่มี
            if (!Schema::hasColumn('departments', 'branch_office_id')) {
                Schema::table('departments', function (Blueprint $table) {
                    $table->foreignId('branch_office_id')->nullable()->after('company_id')
                        ->constrained('branch_offices')->nullOnDelete();
                    $table->index('branch_office_id');
                });
                
                Log::info('เพิ่มคอลัมน์ branch_office_id ในตาราง departments เรียบร้อยแล้ว');
            }
            
            // ตรวจสอบและเพิ่มคอลัมน์ parent_id ถ้ายังไม่มี
            if (!Schema::hasColumn('departments', 'parent_id')) {
                Schema::table('departments', function (Blueprint $table) {
                    $table->foreignId('parent_id')->nullable()->constrained('departments')->nullOnDelete();
                    $table->index('parent_id');
                });
                
                Log::info('เพิ่มคอลัมน์ parent_id ในตาราง departments เรียบร้อยแล้ว');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
