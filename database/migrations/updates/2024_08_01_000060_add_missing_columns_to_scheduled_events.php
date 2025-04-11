<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ตรวจสอบว่าตาราง scheduled_events มีอยู่จริง
        if (!Schema::hasTable('scheduled_events')) {
            Schema::create('scheduled_events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->string('type'); // email, report, invoice, etc.
                $table->string('description')->nullable();
                $table->string('schedule'); // cron expression or keyword like "daily", "weekly", etc.
                $table->string('timezone')->default('Asia/Bangkok');
                $table->boolean('is_enabled')->default(true);
                $table->timestamp('last_run')->nullable();
                $table->timestamp('next_run')->nullable();
                $table->json('event_data')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
                
                // Indexes
                $table->index(['company_id', 'type']);
                $table->index(['is_enabled', 'next_run']);
            });
        } else {
            // เพิ่มคอลัมน์ที่อาจขาดหายไป
            Schema::table('scheduled_events', function (Blueprint $table) {
                // คอลัมน์ type
                if (!Schema::hasColumn('scheduled_events', 'type')) {
                    $table->string('type')->after('name')->default('default');
                }
                
                // คอลัมน์ schedule
                if (!Schema::hasColumn('scheduled_events', 'schedule')) {
                    $table->string('schedule')->after('description')->default('daily');
                }
                
                // คอลัมน์ is_enabled
                if (!Schema::hasColumn('scheduled_events', 'is_enabled')) {
                    $table->boolean('is_enabled')->default(true)->after('timezone');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ไม่ต้องทำอะไรใน down เพราะเราไม่ต้องการลบตาราง
        // หรือคอลัมน์ที่อาจเพิ่มไปแล้ว
    }
};
