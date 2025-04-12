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
            if (!Schema::hasColumn('scheduled_events', 'frequency')) {
                $table->string('frequency')->default('daily')->after('schedule');
            }
        });
        
        // อัปเดต frequency จาก schedule
        DB::statement("UPDATE scheduled_events SET frequency = CASE 
            WHEN schedule LIKE '%minute%' THEN 'minute'
            WHEN schedule LIKE '%hourly%' THEN 'hourly'
            WHEN schedule LIKE '%daily%' THEN 'daily'
            WHEN schedule LIKE '%weekly%' THEN 'weekly'
            WHEN schedule LIKE '%monthly%' THEN 'monthly'
            WHEN schedule LIKE '%yearly%' THEN 'yearly'
            ELSE 'daily'
        END WHERE frequency IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scheduled_events', function (Blueprint $table) {
            if (Schema::hasColumn('scheduled_events', 'frequency')) {
                $table->dropColumn('frequency');
            }
        });
    }
};
