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
        // แยกการจัดการตาม database driver
        $driver = DB::connection()->getDriverName();

        if (Schema::hasTable('orders') && Schema::hasTable('quotations')) {
            try {
                Schema::table('orders', function (Blueprint $table) use ($driver) {
                    // สำหรับ SQLite ไม่สามารถใช้ information_schema ได้
                    if ($driver === 'sqlite') {
                        // สำหรับ SQLite ให้พยายาม drop และสร้าง constraint ใหม่ด้วยวิธีพื้นฐาน
                        try {
                            // นี่อาจจะไม่ทำงานใน SQLite เพราะข้อจำกัด แต่เราจะพยายาม
                            if (Schema::hasColumn('orders', 'quotation_id')) {
                                // สำหรับ SQLite เราไม่สามารถแค่ drop constraint และสร้างใหม่ได้
                                // ต้องสร้างตารางใหม่หรือใช้วิธีอื่น
                                $table->foreign('quotation_id')
                                    ->references('id')->on('quotations')
                                    ->onDelete('set null')
                                    ->onUpdate('cascade');

                                Log::info("พยายามปรับปรุง foreign key constraint สำหรับ orders.quotation_id ใน SQLite");
                            }
                        } catch (\Exception $e) {
                            Log::warning("ไม่สามารถปรับปรุง constraint ของ orders.quotation_id ใน SQLite: " . $e->getMessage());
                        }
                    }
                    // สำหรับ MySQL
                    else if ($driver === 'mysql') {
                        // ดึงข้อมูล constraint ที่มีอยู่
                        $constraintName = DB::selectOne("
                            SELECT CONSTRAINT_NAME 
                            FROM information_schema.KEY_COLUMN_USAGE 
                            WHERE TABLE_SCHEMA = DATABASE() 
                            AND TABLE_NAME = 'orders' 
                            AND COLUMN_NAME = 'quotation_id' 
                            AND REFERENCED_TABLE_NAME = 'quotations'
                        ");

                        // ถ้ามี constraint อยู่แล้ว ให้ลบก่อน
                        if ($constraintName && isset($constraintName->CONSTRAINT_NAME)) {
                            $table->dropForeign($constraintName->CONSTRAINT_NAME);
                        }

                        // สร้าง constraint ใหม่
                        $table->foreign('quotation_id')
                            ->references('id')->on('quotations')
                            ->onDelete('set null')
                            ->onUpdate('cascade');

                        Log::info("ปรับปรุง foreign key constraint สำหรับ orders.quotation_id เรียบร้อยแล้ว");
                    }
                });
            } catch (\Exception $e) {
                Log::warning("ไม่สามารถปรับปรุง foreign key ได้: " . $e->getMessage());
            }
        } else {
            Log::warning("ไม่พบตาราง orders หรือ quotations จึงไม่สามารถปรับปรุง foreign key ได้");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // การ reverse ก็ควรตรวจสอบ driver เช่นกัน
        $driver = DB::connection()->getDriverName();

        // สำหรับ SQLite ไม่ต้องทำอะไร เพราะไม่สามารถจัดการ constraints ได้โดยตรง
        if ($driver === 'sqlite') {
            Log::info('SQLite ไม่สนับสนุนการแก้ไข foreign key constraints หลังจากสร้างตาราง');
            return;
        }

        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'quotation_id')) {
            try {
                Schema::table('orders', function (Blueprint $table) {
                    $table->dropForeign(['quotation_id']);

                    // สร้าง constraint กลับเป็นแบบเดิม
                    $table->foreign('quotation_id')
                        ->references('id')->on('quotations')
                        ->onDelete('restrict') // เปลี่ยนกลับเป็น restrict
                        ->onUpdate('restrict');
                });
            } catch (\Exception $e) {
                Log::warning("ไม่สามารถย้อนกลับ foreign key ได้: " . $e->getMessage());
            }
        }
    }
};
