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
        // ตรวจสอบและอัปเดต company_user pivot table
        if (Schema::hasTable('company_user')) {
            Schema::table('company_user', function (Blueprint $table) {
                if (!Schema::hasColumn('company_user', 'status')) {
                    $table->string('status', 20)->default('active')->after('is_default');
                    $table->index('status');
                }
                if (!Schema::hasColumn('company_user', 'role')) {
                    $table->string('role', 50)->nullable()->after('status');
                }
                if (!Schema::hasColumn('company_user', 'invitation_token')) {
                    $table->string('invitation_token', 100)->nullable()->after('role');
                }
                if (!Schema::hasColumn('company_user', 'invited_at')) {
                    $table->timestamp('invited_at')->nullable()->after('invitation_token');
                }
                if (!Schema::hasColumn('company_user', 'accepted_at')) {
                    $table->timestamp('accepted_at')->nullable()->after('invited_at');
                }
            });
        } else {
            // สร้างตาราง company_user ถ้ายังไม่มี
            Schema::create('company_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->boolean('is_default')->default(false);
                $table->string('status', 20)->default('active'); // active, suspended, pending
                $table->string('role', 50)->nullable(); // บทบาทในบริษัท (CEO, CFO, etc)
                $table->string('invitation_token', 100)->nullable();
                $table->timestamp('invited_at')->nullable();
                $table->timestamp('accepted_at')->nullable();
                $table->timestamps();
                
                // Indexes
                $table->index('company_id');
                $table->index('user_id');
                $table->index('is_default');
                $table->index('status');
                
                // Unique constraints
                $table->unique(['company_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        if (Schema::hasTable('company_user')) {
            if (Schema::hasColumn('company_user', 'id')) {
                // แสดงว่าเราได้สร้างตารางนี้ใหม่ จึงลบทั้งตารางเมื่อ rollback
                Schema::dropIfExists('company_user');
            } else {
                // แต่ถ้าเราเพียงแค่ปรับปรุงตารางที่มีอยู่แล้ว ให้ลบเฉพาะคอลัมน์ที่เพิ่มไป
                Schema::table('company_user', function (Blueprint $table) {
                    $columns = ['status', 'role', 'invitation_token', 'invited_at', 'accepted_at'];
                    
                    foreach ($columns as $column) {
                        if (Schema::hasColumn('company_user', $column)) {
                            if ($column === 'status') {
                                $table->dropIndex(['status']);
                            }
                            $table->dropColumn($column);
                        }
                    }
                });
            }
        }
    }
};
