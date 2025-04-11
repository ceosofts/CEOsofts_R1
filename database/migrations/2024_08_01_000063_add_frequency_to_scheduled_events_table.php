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
     */
    public function up(): void
    {
        Schema::table('scheduled_events', function (Blueprint $table) {
            if (!Schema::hasColumn('scheduled_events', 'frequency')) {
                $table->string('frequency')->nullable()->after('event_type');
            }
        });

        // ตรวจสอบว่าคอลัมน์ schedule มีอยู่หรือไม่
        if (Schema::hasColumn('scheduled_events', 'schedule')) {
            try {
                // อัปเดตค่า frequency โดยใช้ข้อมูลจาก schedule
                DB::statement('
                    UPDATE scheduled_events 
                    SET frequency = CASE 
                        WHEN schedule LIKE "%minute%" THEN "minute"
                        WHEN schedule LIKE "%hourly%" THEN "hourly"
                        WHEN schedule LIKE "%daily%" THEN "daily"
                        WHEN schedule LIKE "%weekly%" THEN "weekly"
                        WHEN schedule LIKE "%monthly%" THEN "monthly"
                        WHEN schedule LIKE "%yearly%" THEN "yearly"
                        ELSE "daily"
                    END 
                    WHERE frequency IS NULL
                ');
            } catch (\Exception $e) {
                Log::warning('ไม่สามารถอัปเดตค่า frequency: ' . $e->getMessage());
            }
        } else {
            // ถ้าคอลัมน์ schedule ไม่มีอยู่ ให้ตั้งค่า default สำหรับ frequency
            try {
                DB::statement('UPDATE scheduled_events SET frequency = "daily" WHERE frequency IS NULL');
            } catch (\Exception $e) {
                Log::warning('ไม่สามารถตั้งค่า default frequency: ' . $e->getMessage());
            }
        }
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
