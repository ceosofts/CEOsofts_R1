<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     * สร้างตารางเกี่ยวกับ Permissions
     * รวมการทำงานจากไฟล์:
     * - 2024_08_01_000025_add_group_to_permissions_table.php
     * - 0001_01_01_00022_create_roles_tables.php
     */
    public function up(): void
    {
        // สร้างตาราง permissions
        if (!Schema::hasTable('permissions')) {
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
            Log::info('สร้างตาราง permissions เรียบร้อยแล้ว');
        }

        // สร้างตาราง roles
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('guard_name')->default('web');
                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('description')->nullable();
                $table->boolean('is_system')->default(false);
                $table->boolean('is_system_role')->default(false); // จากไฟล์ 00022
                $table->json('metadata')->nullable(); // จากไฟล์ 00022
                $table->string('color')->nullable(); // จากไฟล์ 00022
                $table->integer('level')->default(0); // จากไฟล์ 00022
                $table->string('type')->default('custom'); // จากไฟล์ 00022
                $table->boolean('is_default')->default(false); // จากไฟล์ 00022
                $table->boolean('is_protected')->default(false); // จากไฟล์ 00022
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null'); // จากไฟล์ 00022
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null'); // จากไฟล์ 00022
                $table->timestamps();

                $table->unique(['name', 'guard_name']);
                $table->index('company_id');
                $table->index('is_system');
                $table->index('is_default');
                $table->index('type');
                $table->index('level');
            });
            Log::info('สร้างตาราง roles เรียบร้อยแล้ว');
        } else {
            // ตรวจสอบและเพิ่มคอลัมน์ที่อาจหายไป
            $columns = [
                'company_id', 'is_system_role', 'metadata', 'color', 'level', 
                'type', 'is_default', 'is_protected', 'created_by', 'updated_by'
            ];
            
            foreach ($columns as $column) {
                if (!Schema::hasColumn('roles', $column)) {
                    Schema::table('roles', function (Blueprint $table) use ($column) {
                        switch ($column) {
                            case 'company_id':
                                $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
                                $table->index('company_id');
                                break;
                            case 'is_system_role':
                                $table->boolean('is_system_role')->default(false);
                                break;
                            case 'metadata':
                                $table->json('metadata')->nullable();
                                break;
                            case 'color':
                                $table->string('color')->nullable();
                                break;
                            case 'level':
                                $table->integer('level')->default(0);
                                $table->index('level');
                                break;
                            case 'type':
                                $table->string('type')->default('custom');
                                $table->index('type');
                                break;
                            case 'is_default':
                                $table->boolean('is_default')->default(false);
                                $table->index('is_default');
                                break;
                            case 'is_protected':
                                $table->boolean('is_protected')->default(false);
                                break;
                            case 'created_by':
                                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                                break;
                            case 'updated_by':
                                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
                                break;
                        }
                    });
                    Log::info("เพิ่มคอลัมน์ {$column} ในตาราง roles เรียบร้อยแล้ว");
                }
            }
        }

        // สร้างตาราง role_has_permissions
        if (!Schema::hasTable('role_has_permissions')) {
            Schema::create('role_has_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('permission_id');
                $table->unsignedBigInteger('role_id');
                $table->primary(['permission_id', 'role_id']);
            });
            Log::info('สร้างตาราง role_has_permissions เรียบร้อยแล้ว');
        }

        // สร้างตาราง model_has_roles
        if (!Schema::hasTable('model_has_roles')) {
            Schema::create('model_has_roles', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id');
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
                $table->primary(['role_id', 'model_id', 'model_type']);
            });
            Log::info('สร้างตาราง model_has_roles เรียบร้อยแล้ว');
        }

        // สร้างตาราง model_has_permissions
        if (!Schema::hasTable('model_has_permissions')) {
            Schema::create('model_has_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('permission_id');
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
                $table->primary(['permission_id', 'model_id', 'model_type']);
            });
            Log::info('สร้างตาราง model_has_permissions เรียบร้อยแล้ว');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permissions');
    }
};
