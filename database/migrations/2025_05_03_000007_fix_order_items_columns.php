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
        // แก้ไขตาราง order_items
        Schema::table('order_items', function (Blueprint $table) {
            // กรณีที่มี unit_price แต่ไม่มี price
            if (Schema::hasColumn('order_items', 'unit_price') && !Schema::hasColumn('order_items', 'price')) {
                $table->decimal('price', 15, 2)->nullable();
            }
            
            // กรณีที่มี price แต่ไม่มี unit_price (เผื่อกรณีในอนาคต)
            if (Schema::hasColumn('order_items', 'price') && !Schema::hasColumn('order_items', 'unit_price')) {
                $table->decimal('unit_price', 15, 2)->nullable();
            }
            
            // อัพเดทให้ price เป็น nullable เพื่อความยืดหยุ่น
            if (Schema::hasColumn('order_items', 'price')) {
                $table->decimal('price', 15, 2)->nullable()->change();
            }
            
            // ตรวจสอบฟิลด์อื่นๆ ที่อาจเป็น NOT NULL
            if (Schema::hasColumn('order_items', 'sku') && 
                !Schema::getConnection()->getDoctrineColumn('order_items', 'sku')->getNotnull()) {
                $table->string('sku')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // เวอร์ชั่น down ไม่จำเป็นต้องทำอะไร เพราะเป็นการ fix columns
    }
};
