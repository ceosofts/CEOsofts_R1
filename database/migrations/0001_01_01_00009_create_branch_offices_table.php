<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        // สร้างตาราง branch_offices ถ้ายังไม่มี
        if (!Schema::hasTable('branch_offices')) {
            Schema::create('branch_offices', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->string('code', 50)->nullable();
                $table->text('address')->nullable();
                $table->string('phone', 20)->nullable();
                $table->string('email')->nullable();
                $table->boolean('is_headquarters')->default(false);
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('manager_id')->nullable(); // จากไฟล์ 2025_04_17
                $table->foreign('manager_id')->references('id')->on('employees')->onDelete('set null'); // จากไฟล์ 2025_04_17
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                // Indexes
                $table->index('company_id');
                $table->index('name');
                $table->index('code');
                $table->index('is_headquarters');
                $table->index('is_active');
                $table->index('email');
                $table->index('manager_id'); // เพิ่ม index สำหรับ manager_id
                
                // Unique constraint
                $table->unique(['company_id', 'code']);
            });
            
            Log::info('สร้างตาราง branch_offices เรียบร้อยแล้ว');
        } else {
            // ตรวจสอบและเพิ่มคอลัมน์ manager_id ถ้ายังไม่มี
            if (!Schema::hasColumn('branch_offices', 'manager_id')) {
                Schema::table('branch_offices', function (Blueprint $table) {
                    $table->unsignedBigInteger('manager_id')->nullable()->after('is_active');
                    $table->foreign('manager_id')->references('id')->on('employees')->onDelete('set null');
                    $table->index('manager_id');
                });
                
                Log::info('เพิ่มคอลัมน์ manager_id ในตาราง branch_offices เรียบร้อยแล้ว');
            }
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_offices');
    }
};
