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
            if (!Schema::hasColumn('scheduled_events', 'start_date')) {
                $table->timestamp('start_date')->nullable()->after('frequency');
            }
        });
        
        // อัปเดตค่า start_date ให้กับข้อมูลที่มีอยู่แล้ว
        DB::statement('UPDATE scheduled_events SET start_date = created_at WHERE start_date IS NULL');
        
        // ปรับให้ start_date เป็น NOT NULL และมีค่า default
        DB::statement('ALTER TABLE scheduled_events MODIFY start_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scheduled_events', function (Blueprint $table) {
            if (Schema::hasColumn('scheduled_events', 'start_date')) {
                $table->dropColumn('start_date');
            }
        });
    }
};
