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
        // สร้างตารางการตั้งค่า (settings)
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('group', 50);
            $table->string('key', 100);
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string');
            $table->boolean('is_public')->default(false);
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            // Indexes
            $table->index('company_id');
            $table->index(['group', 'key']);
            $table->index('is_public');
            
            // Unique constraints
            $table->unique(['company_id', 'group', 'key']);
        });

        // สร้างตารางเหตุการณ์แบบตั้งเวลา (scheduled_events)
        Schema::create('scheduled_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('event_type', 50);
            $table->string('frequency', 20); // once, daily, weekly, monthly, yearly
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('time')->nullable();
            $table->string('day_of_week', 10)->nullable(); // วันในสัปดาห์ (1-7)
            $table->integer('day_of_month')->nullable(); // วันที่ในเดือน (1-31)
            $table->integer('month')->nullable(); // เดือน (1-12)
            $table->string('timezone', 50)->default('Asia/Bangkok');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_run')->nullable();
            $table->timestamp('next_run')->nullable();
            $table->string('action');
            $table->json('parameters')->nullable();
            $table->text('output')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('notifications_to')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('company_id');
            $table->index('event_type');
            $table->index('frequency');
            $table->index('is_active');
            $table->index('next_run');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_events');
        Schema::dropIfExists('settings');
    }
};
