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
        Schema::table('scheduled_events', function (Blueprint $table) {
            if (!Schema::hasColumn('scheduled_events', 'event_type')) {
                $table->string('event_type')->default('general')->after('type');
            }
            
            // อัปเดตค่า event_type จากคอลัมน์ type ถ้ามีค่าเป็น NULL
            DB::statement('UPDATE scheduled_events SET event_type = type WHERE event_type IS NULL');
            
            // ปรับให้ event_type เป็น NOT NULL เพื่อป้องกันปัญหา
            DB::statement('ALTER TABLE scheduled_events MODIFY event_type VARCHAR(255) NOT NULL DEFAULT "general"');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scheduled_events', function (Blueprint $table) {
            if (Schema::hasColumn('scheduled_events', 'event_type')) {
                $table->dropColumn('event_type');
            }
        });
    }
};
