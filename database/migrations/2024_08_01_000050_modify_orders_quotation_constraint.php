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
        // ตรวจสอบชนิดของฐานข้อมูล
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // SQLite ไม่รองรับ information_schema
            Log::info('Skipping foreign key modification for SQLite.');
            return;
        }

        // ตรวจสอบการมีอยู่ของ foreign key ใน order ที่อ้างอิงไปยัง quotations
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'quotation_id')) {
            Schema::table('orders', function (Blueprint $table) {
                // ลบ foreign key constraint ออก
                $fkName = DB::selectOne("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'orders' 
                    AND COLUMN_NAME = 'quotation_id' 
                    AND REFERENCED_TABLE_NAME = 'quotations'
                ");
                
                if ($fkName && isset($fkName->CONSTRAINT_NAME)) {
                    $table->dropForeign($fkName->CONSTRAINT_NAME);
                } else {
                    // ถ้าไม่พบชื่อ constraint ลองลบด้วยชื่อมาตรฐาน
                    try {
                        $table->dropForeign(['quotation_id']);
                    } catch (\Exception $e) {
                        // อาจจะไม่มี constraint นี้
                    }
                }
            });
        }
        
        // ตรวจสอบว่ามีตาราง orders_items และ foreign key จาก quotation_item_id หรือไม่
        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'quotation_item_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                // ลบ foreign key constraint ออก
                $fkName = DB::selectOne("
                    SELECT CONSTRAINT_NAME 
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'order_items' 
                    AND COLUMN_NAME = 'quotation_item_id' 
                    AND REFERENCED_TABLE_NAME = 'quotation_items'
                ");
                
                if ($fkName && isset($fkName->CONSTRAINT_NAME)) {
                    $table->dropForeign($fkName->CONSTRAINT_NAME);
                } else {
                    // ถ้าไม่พบชื่อ constraint ลองลบด้วยชื่อมาตรฐาน
                    try {
                        $table->dropForeign(['quotation_item_id']);
                    } catch (\Exception $e) {
                        // อาจจะไม่มี constraint นี้
                    }
                }
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
            Log::info('Skipping foreign key rollback for SQLite.');
            return;
        }

        // สร้าง foreign key constraint กลับคืน (ถ้าจำเป็น)
        if (Schema::hasTable('quotations') && Schema::hasTable('orders') && 
            Schema::hasColumn('orders', 'quotation_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreign('quotation_id')
                      ->references('id')
                      ->on('quotations')
                      ->nullOnDelete();
            });
        }
        
        // สร้าง foreign key constraint กลับคืน (ถ้าจำเป็น)
        if (Schema::hasTable('quotation_items') && Schema::hasTable('order_items') && 
            Schema::hasColumn('order_items', 'quotation_item_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->foreign('quotation_item_id')
                      ->references('id')
                      ->on('quotation_items')
                      ->nullOnDelete();
            });
        }
    }
};
