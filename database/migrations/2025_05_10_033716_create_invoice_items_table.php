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
        // ปรับเปลี่ยนจาก create เป็น table เพื่อปรับปรุงตารางที่มีอยู่แล้ว
        Schema::table('invoice_items', function (Blueprint $table) {
            // เพิ่มฟิลด์ใหม่เข้าไปในตาราง
            if (!Schema::hasColumn('invoice_items', 'subtotal')) {
                $table->decimal('subtotal', 15, 2)->default(0)->after('tax_amount');
            }
            
            if (!Schema::hasColumn('invoice_items', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->default(7)->after('unit_price');
            }
            
            if (!Schema::hasColumn('invoice_items', 'delivery_order_item_id')) {
                $table->foreignId('delivery_order_item_id')->nullable()->after('product_id');
            }
            
            if (!Schema::hasColumn('invoice_items', 'quotation_item_id')) {
                $table->foreignId('quotation_item_id')->nullable()->after('delivery_order_item_id');
            }
            
            if (!Schema::hasColumn('invoice_items', 'order_item_id')) {
                $table->foreignId('order_item_id')->nullable()->after('quotation_item_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            // ลบฟิลด์ที่เพิ่มเข้าไปในตาราง
            if (Schema::hasColumn('invoice_items', 'subtotal')) {
                $table->dropColumn('subtotal');
            }
            
            if (Schema::hasColumn('invoice_items', 'tax_rate')) {
                $table->dropColumn('tax_rate');
            }
            
            if (Schema::hasColumn('invoice_items', 'delivery_order_item_id')) {
                $table->dropForeign(['delivery_order_item_id']);
                $table->dropColumn('delivery_order_item_id');
            }
            
            if (Schema::hasColumn('invoice_items', 'quotation_item_id')) {
                $table->dropForeign(['quotation_item_id']);
                $table->dropColumn('quotation_item_id');
            }
            
            if (Schema::hasColumn('invoice_items', 'order_item_id')) {
                $table->dropForeign(['order_item_id']);
                $table->dropColumn('order_item_id');
            }
        });
    }
};
