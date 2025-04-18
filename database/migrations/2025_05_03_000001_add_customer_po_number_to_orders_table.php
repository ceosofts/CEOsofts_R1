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
        Schema::table('orders', function (Blueprint $table) {
            // เพิ่มคอลัมน์ customer_po_number หลังจาก order_number ถ้ายังไม่มี
            if (!Schema::hasColumn('orders', 'customer_po_number')) {
                $table->string('customer_po_number')->nullable()->after('order_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'customer_po_number')) {
                $table->dropColumn('customer_po_number');
            }
        });
    }
};
