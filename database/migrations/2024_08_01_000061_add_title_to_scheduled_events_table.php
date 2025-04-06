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
            if (!Schema::hasColumn('scheduled_events', 'title')) {
                $table->string('title')->after('id')->nullable();
            }
            
            // ถ้ามีคอลัมน์ title แต่ยังไม่มีค่าเริ่มต้น ให้อัพเดตค่า title โดยใช้ name
            \DB::statement('UPDATE scheduled_events SET title = name WHERE title IS NULL');
            
            // เปลี่ยน title ให้ไม่เป็น NULL เพื่อบังคับให้มีค่า
            \DB::statement('ALTER TABLE scheduled_events MODIFY title VARCHAR(255) NOT NULL');
        });
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
