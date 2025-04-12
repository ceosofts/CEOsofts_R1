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
     * - 2024_08_01_000026_update_users_table.php
     */
    public function up(): void
    {
        // สำรองข้อมูล users ถ้ามีตารางอยู่แล้ว
        $existingUsers = [];
        if (Schema::hasTable('users')) {
            try {
                $existingUsers = DB::table('users')->get()->toArray();
                Log::info('สำรองข้อมูล users จำนวน ' . count($existingUsers) . ' รายการ');
                Schema::dropIfExists('users');
            } catch (\Exception $e) {
                Log::warning('ไม่สามารถสำรองข้อมูล users: ' . $e->getMessage());
            }
        }

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            // คอลัมน์เพิ่มเติมจาก update_users_table.php
            $table->string('uuid', 36)->unique()->nullable();
            $table->string('username')->nullable()->unique();
            $table->string('phone', 20)->nullable();
            $table->string('avatar')->nullable();
            $table->string('timezone', 50)->default('Asia/Bangkok');
            $table->string('locale', 10)->default('th');
            $table->json('preferences')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_system')->default(false);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->timestamp('password_changed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->foreignId('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('name');
            $table->index('email');
            $table->index('username');
            $table->index('phone');
            $table->index('is_active');
            $table->index('is_admin');
            $table->index('last_login_at');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Log::info('สร้างตาราง users, password_reset_tokens, sessions เรียบร้อยแล้ว');

        // นำข้อมูลเดิมกลับคืน (ถ้ามี)
        if (!empty($existingUsers)) {
            try {
                foreach ($existingUsers as $user) {
                    $userData = (array) $user;

                    // ตรวจสอบว่าจำเป็นต้องเพิ่ม UUID หรือไม่
                    if (!isset($userData['uuid']) || empty($userData['uuid'])) {
                        $userData['uuid'] = \Illuminate\Support\Str::uuid()->toString();
                    }

                    DB::table('users')->insert($userData);
                }

                Log::info('นำข้อมูล users กลับคืนเรียบร้อยแล้ว');
            } catch (\Exception $e) {
                Log::error('ไม่สามารถนำข้อมูล users กลับคืน: ' . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
