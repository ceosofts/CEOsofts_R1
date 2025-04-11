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
        // ปรับปรุงตาราง users เพื่อเพิ่มฟิลด์ตามเอกสารออกแบบ
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'uuid')) {
                    $table->ulid('uuid')->nullable()->unique()->after('id');
                }
                if (!Schema::hasColumn('users', 'two_factor_secret')) {
                    $table->text('two_factor_secret')->nullable()->after('password');
                }
                if (!Schema::hasColumn('users', 'two_factor_recovery_codes')) {
                    $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret');
                }
                if (!Schema::hasColumn('users', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('remember_token');
                }
                if (!Schema::hasColumn('users', 'is_system_admin')) {
                    $table->boolean('is_system_admin')->default(false)->after('is_active');
                }
                if (!Schema::hasColumn('users', 'language')) {
                    $table->string('language', 5)->default('th')->after('is_system_admin');
                }
                if (!Schema::hasColumn('users', 'timezone')) {
                    $table->string('timezone', 50)->default('Asia/Bangkok')->after('language');
                }
                if (!Schema::hasColumn('users', 'last_login_at')) {
                    $table->timestamp('last_login_at')->nullable()->after('timezone');
                }
                
                // เพิ่มคอลัมน์ profile_photo_path ถ้ายังไม่มี
                if (!Schema::hasColumn('users', 'profile_photo_path')) {
                    $table->string('profile_photo_path', 2048)->nullable();
                }

                // เพิ่มคอลัมน์ settings ไว้ท้ายตาราง (ไม่ระบุว่าต้องอยู่หลังคอลัมน์ไหน)
                if (!Schema::hasColumn('users', 'settings')) {
                    $table->json('settings')->nullable();
                }
                
                // เพิ่ม soft delete ถ้ายังไม่มี
                if (!Schema::hasColumn('users', 'deleted_at')) {
                    $table->softDeletes();
                }
                
                // เพิ่ม indexes
                try {
                    if (!Schema::hasIndex('users', 'users_is_active_index') && Schema::hasColumn('users', 'is_active')) {
                        $table->index('is_active');
                    }
                    if (!Schema::hasIndex('users', 'users_is_system_admin_index') && Schema::hasColumn('users', 'is_system_admin')) {
                        $table->index('is_system_admin');
                    }
                    if (!Schema::hasIndex('users', 'users_last_login_at_index') && Schema::hasColumn('users', 'last_login_at')) {
                        $table->index('last_login_at');
                    }
                } catch (\Exception $e) {
                    \Log::warning("ไม่สามารถเพิ่ม index ในตาราง users: " . $e->getMessage());
                }
            });
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $columns = [
                    'uuid', 'two_factor_secret', 'two_factor_recovery_codes',
                    'is_active', 'is_system_admin', 'language', 'timezone',
                    'last_login_at', 'profile_photo_path', 'settings', 'deleted_at'
                ];
                
                foreach ($columns as $column) {
                    if (Schema::hasColumn('users', $column)) {
                        if ($column === 'uuid') {
                            $table->dropUnique(['uuid']);
                        }
                        if (in_array($column, ['is_active', 'is_system_admin', 'last_login_at'])) {
                            try {
                                $indexName = "users_{$column}_index";
                                if (Schema::hasIndex('users', $indexName)) {
                                    $table->dropIndex($indexName);
                                }
                            } catch (\Exception $e) {
                                \Log::warning("ไม่สามารถลบ index {$column}: " . $e->getMessage());
                            }
                        }
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
