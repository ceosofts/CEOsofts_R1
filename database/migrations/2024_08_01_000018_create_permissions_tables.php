<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migration.
     * สร้างตารางเกี่ยวกับ Permissions
     * รวมการทำงานจากไฟล์ 2024_08_01_000025_add_group_to_permissions_table.php แล้ว
     */
    public function up(): void
    {
        // สร้างตาราง permissions
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name')->default('web');
            $table->string('group')->nullable(); // จากไฟล์ 000025_add_group_to_permissions_table.php
            $table->string('description')->nullable(); // เพิ่มคำอธิบาย
            $table->timestamps();

            $table->unique(['name', 'guard_name']);

            // เพิ่ม index สำหรับคอลัมน์ group เพื่อการค้นหาที่เร็วขึ้น
            $table->index('group');
        });

        // สร้างตาราง model_has_permissions
        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->onDelete('cascade');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            $table->primary(['permission_id', 'model_id', 'model_type']);
            $table->index(['model_id', 'model_type']);
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('permissions');
    }
};
