<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migration.
     * สร้างตารางเกี่ยวกับ Roles
     * ต้องรันหลังจาก create_permissions_tables.php
     * รวมการทำงานจากไฟล์:
     * - 2024_08_01_000026_add_columns_to_roles_table.php
     */
    public function up(): void
    {
        // สร้างตาราง roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name')->default('web');
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('description')->nullable();
            $table->boolean('is_system_role')->default(false);
            $table->json('metadata')->nullable(); // เพิ่มสำหรับข้อมูลเพิ่มเติม

            // คอลัมน์เพิ่มเติมจาก 2024_08_01_000026_add_columns_to_roles_table.php
            $table->string('color', 20)->nullable(); // สีแสดงผลสำหรับ Role
            $table->integer('level')->default(0); // ระดับความสำคัญ (เช่น 1=ต่ำ, 10=สูงสุด)
            $table->string('type', 20)->default('custom'); // ประเภท (system, default, custom)
            $table->boolean('is_default')->default(false); // กำหนดเป็น default role หรือไม่
            $table->boolean('is_protected')->default(false); // ป้องกันการลบหรือไม่
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // ผู้สร้าง
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete(); // ผู้แก้ไขล่าสุด

            $table->timestamps();

            $table->unique(['name', 'guard_name', 'company_id']);

            // Indexes เพิ่มเติม
            $table->index('level');
            $table->index('type');
            $table->index('is_default');
            $table->index('is_protected');
            $table->index('created_by');
        });

        // สร้างตาราง model_has_roles
        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->primary(['role_id', 'model_id', 'model_type']);
            $table->index(['model_id', 'model_type']);
        });

        // สร้างตาราง role_has_permissions
        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);
        });

        // สร้างตาราง company_user (ถ้ายังไม่มี)
        if (!Schema::hasTable('company_user')) {
            Schema::create('company_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->boolean('is_default')->default(false);
                $table->timestamps();

                $table->unique(['company_id', 'user_id']);
            });
        }

        Log::info('สร้างตาราง roles และตารางที่เกี่ยวข้องเรียบร้อยแล้ว รวมถึงคอลัมน์เพิ่มเติมจาก add_columns_to_roles_table');
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        if (Schema::hasTable('company_user')) {
            Schema::dropIfExists('company_user');
        }
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('roles');
    }
};
