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
            if (!Schema::hasColumn('scheduled_events', 'title')) {
                $table->string('title')->after('id')->nullable();
            }
        });

        // ถ้ามีคอลัมน์ title แต่ยังไม่มีค่าเริ่มต้น ให้อัพเดตค่า title โดยใช้ name
        try {
            DB::statement('UPDATE scheduled_events SET title = name WHERE title IS NULL');
        } catch (\Exception $e) {
            Log::warning('ไม่สามารถอัปเดตค่า title: ' . $e->getMessage());
        }

        // เปลี่ยน title ให้ไม่เป็น NULL เพื่อบังคับให้มีค่า
        try {
            DB::statement('ALTER TABLE scheduled_events MODIFY title VARCHAR(255) NOT NULL');
        } catch (\Exception $e) {
            Log::warning('ไม่สามารถเปลี่ยน title ให้เป็น NOT NULL: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scheduled_events', function (Blueprint $table) {
            if (Schema::hasColumn('scheduled_events', 'title')) {
                $table->dropColumn('title');
            }
        });
    }
};
