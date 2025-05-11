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
        Schema::table('delivery_order_items', function (Blueprint $table) {
            // เพิ่มคอลัมน์ status สำหรับเก็บสถานะของรายการสินค้า
            $table->string('status')->default('pending')->after('unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_order_items', function (Blueprint $table) {
            // ลบคอลัมน์ status กรณี rollback
            $table->dropColumn('status');
        });
    }
};
