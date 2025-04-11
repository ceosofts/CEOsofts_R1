<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        // ตรวจสอบว่าตารางมีอยู่หรือไม่ก่อนสร้าง
        if (!Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) {
                // โครงสร้างตาราง
                $table->id();
                $table->string('name');
                $table->string('code', 50)->nullable()->unique();
                $table->text('address')->nullable();
                $table->string('phone', 20)->nullable();
                $table->string('email')->nullable();
                $table->string('tax_id', 50)->nullable();
                $table->string('website')->nullable();
                $table->string('logo')->nullable();
                $table->boolean('is_active')->default(true);
                $table->string('status', 20)->default('active');
                $table->json('settings')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                // Indexes
                $table->index('name');
                $table->index('code');
                $table->index('email');
                $table->index('phone');
                $table->index('status');
                $table->index('is_active');
            });
        } else {
            // อัปเดตโครงสร้างตารางที่มีอยู่แล้ว (ถ้าต้องการ)
            Schema::table('companies', function (Blueprint $table) {
                // เพิ่มฟิลด์ใหม่หรือแก้ไขฟิลด์ที่มีอยู่
                // ตัวอย่าง:
                // if (!Schema::hasColumn('companies', 'new_column')) {
                //     $table->string('new_column')->nullable();
                // }
            });
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        // ไม่ลบตารางในกรณี rollback เนื่องจากอาจมีการสร้างจาก migration อื่น
        // Schema::dropIfExists('companies');
    }
};
