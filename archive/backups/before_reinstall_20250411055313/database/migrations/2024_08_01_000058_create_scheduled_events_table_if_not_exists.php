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
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_events');
    }
};
