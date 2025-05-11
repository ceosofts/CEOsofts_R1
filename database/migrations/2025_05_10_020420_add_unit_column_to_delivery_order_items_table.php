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
            // เพิ่มคอลัมน์ unit สำหรับเก็บข้อมูลหน่วยของสินค้า
            $table->string('unit')->nullable()->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_order_items', function (Blueprint $table) {
            // ลบคอลัมน์ unit กรณี rollback
            $table->dropColumn('unit');
        });
    }
};
