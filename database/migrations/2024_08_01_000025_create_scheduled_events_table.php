<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migration.
     * สร้างตารางเหตุการณ์แบบตั้งเวลา (scheduled_events)
     * รวมการทำงานจากไฟล์:
     * - 2024_08_01_000058_create_scheduled_events_table_if_not_exists.php
     * - 2024_08_01_000059_fix_scheduled_events_table.php
     * - 2024_08_01_000060_add_missing_columns_to_scheduled_events.php
     * - 2024_08_01_000061_add_title_to_scheduled_events_table.php
     */
    public function up(): void
    {
        // ตรวจสอบว่ามีตารางอยู่แล้วหรือไม่ (จาก create_scheduled_events_table_if_not_exists)
        if (Schema::hasTable('scheduled_events')) {
            Log::info('ตาราง scheduled_events มีอยู่แล้ว จะทำการอัปเดตโครงสร้าง');

            // สำรองข้อมูลเดิม (จาก fix_scheduled_events_table)
            try {
                $existingData = DB::table('scheduled_events')->get();
                Log::info('สำรองข้อมูล scheduled_events จำนวน ' . count($existingData) . ' รายการ');

                // ลบตารางเดิมและสร้างใหม่
                Schema::dropIfExists('scheduled_events');
            } catch (\Exception $e) {
                Log::warning('ไม่สามารถสำรองข้อมูล scheduled_events: ' . $e->getMessage());
                // ยังคงลบตารางเพื่อสร้างใหม่
                Schema::dropIfExists('scheduled_events');
            }
        }

        // สร้างตารางเหตุการณ์แบบตั้งเวลา (scheduled_events) ใหม่หรือปรับปรุง
        Schema::create('scheduled_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title'); // มีอยู่แล้ว - จากไฟล์ add_title_to_scheduled_events_table.php
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

            // คอลัมน์เพิ่มเติมจาก add_missing_columns_to_scheduled_events
            $table->boolean('recalculate_next_run')->default(false); // ต้องคำนวณ next_run ใหม่หรือไม่
            $table->integer('max_attempts')->default(1); // จำนวนครั้งสูงสุดที่จะลองทำงานซ้ำหากล้มเหลว
            $table->integer('attempts')->default(0); // จำนวนครั้งที่ได้พยายามแล้ว
            $table->timestamp('failed_at')->nullable(); // เวลาที่ทำงานล้มเหลวล่าสุด
            $table->text('failure_reason')->nullable(); // สาเหตุที่ทำงานล้มเหลว
            $table->boolean('is_recurring')->default(true); // เป็น event ที่ทำซ้ำหรือไม่
            $table->json('run_history')->nullable(); // ประวัติการทำงาน
            $table->string('status', 20)->default('pending'); // สถานะ (pending, running, completed, failed)

            // คอลัมน์เพิ่มเติมจาก fix_scheduled_events_table
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete(); // ผู้อัปเดตล่าสุด
            $table->integer('duration_minutes')->nullable(); // ระยะเวลาที่ใช้ (นาที)
            $table->timestamp('completed_at')->nullable(); // เวลาที่ทำงานเสร็จล่าสุด
            $table->boolean('notify_on_completion')->default(false); // แจ้งเตือนเมื่อทำงานเสร็จ
            $table->boolean('notify_on_failure')->default(true); // แจ้งเตือนเมื่อทำงานล้มเหลว

            $table->timestamps();

            // Indexes จากไฟล์เดิมและที่เพิ่มเติม
            $table->index('company_id');
            $table->index('event_type');
            $table->index('frequency');
            $table->index('is_active');
            $table->index('next_run');
            $table->index('created_by');
            $table->index('status'); // จาก add_missing_columns
            $table->index('is_recurring'); // จาก add_missing_columns
        });

        Log::info('สร้างหรืออัปเดตตาราง scheduled_events เรียบร้อยแล้ว');

        // พยายามนำข้อมูลเดิมกลับคืนถ้ามีการสำรองไว้
        if (isset($existingData) && count($existingData) > 0) {
            try {
                foreach ($existingData as $event) {
                    // แปลง stdClass เป็น array
                    $eventArr = (array) $event;

                    // ลบ primary key เพื่อให้ auto-increment ทำงานได้ถูกต้อง
                    if (isset($eventArr['id'])) {
                        unset($eventArr['id']);
                    }

                    // เพิ่มค่าเริ่มต้นสำหรับคอลัมน์ใหม่ที่จำเป็น
                    if (!isset($eventArr['status'])) {
                        $eventArr['status'] = 'pending';
                    }
                    if (!isset($eventArr['is_recurring'])) {
                        $eventArr['is_recurring'] = true;
                    }

                    // เพิ่มกลับเข้าฐานข้อมูล
                    DB::table('scheduled_events')->insert($eventArr);
                }

                Log::info('นำข้อมูล scheduled_events กลับคืนเรียบร้อยแล้ว');
            } catch (\Exception $e) {
                Log::error('ไม่สามารถนำข้อมูล scheduled_events กลับคืน: ' . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_events');
    }
};
