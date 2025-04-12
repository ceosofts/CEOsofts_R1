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
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // สำหรับ SQLite: ใช้วิธีสร้างตารางใหม่และย้ายข้อมูล
            Schema::create('scheduled_events_temp', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('event_type')->default('general')->nullable(false);
                $table->string('frequency')->nullable();
                $table->timestamp('start_date')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable(false);
                $table->timestamps();
            });

            // ย้ายข้อมูลจากตารางเดิมไปยังตารางใหม่
            DB::statement('
                INSERT INTO scheduled_events_temp (id, title, event_type, frequency, start_date, created_at, updated_at)
                SELECT id, title, event_type, frequency, CURRENT_TIMESTAMP, created_at, updated_at
                FROM scheduled_events
            ');

            // ลบตารางเดิมและเปลี่ยนชื่อ
            Schema::drop('scheduled_events');
            Schema::rename('scheduled_events_temp', 'scheduled_events');
        } else {
            // สำหรับ MySQL หรือฐานข้อมูลอื่นๆ
            Schema::table('scheduled_events', function (Blueprint $table) {
                $table->timestamp('start_date')->default(DB::raw('CURRENT_TIMESTAMP'))->nullable(false)->after('frequency');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // สำหรับ SQLite: ใช้วิธีสร้างตารางใหม่และย้ายข้อมูลกลับ
            Schema::create('scheduled_events_temp', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('event_type')->default('general')->nullable(false);
                $table->string('frequency')->nullable();
                $table->timestamps();
            });

            // ย้ายข้อมูลกลับไปยังตารางใหม่
            DB::statement('
                INSERT INTO scheduled_events_temp (id, title, event_type, frequency, created_at, updated_at)
                SELECT id, title, event_type, frequency, created_at, updated_at
                FROM scheduled_events
            ');

            // ลบตารางเดิมและเปลี่ยนชื่อ
            Schema::drop('scheduled_events');
            Schema::rename('scheduled_events_temp', 'scheduled_events');
        } else {
            // สำหรับ MySQL หรือฐานข้อมูลอื่นๆ
            Schema::table('scheduled_events', function (Blueprint $table) {
                $table->dropColumn('start_date');
            });
        }
    }
};
