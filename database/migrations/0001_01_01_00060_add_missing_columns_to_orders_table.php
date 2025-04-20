<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ตรวจสอบและเพิ่มคอลัมน์ที่ขาดหายไปในตาราง orders
        try {
            Schema::table('orders', function (Blueprint $table) {
                // เพิ่ม tax_rate ถ้ายังไม่มี
                if (!Schema::hasColumn('orders', 'tax_rate')) {
                    $table->decimal('tax_rate', 5, 2)->default(0)->after('subtotal');
                    Log::info('เพิ่มคอลัมน์ tax_rate ลงในตาราง orders เรียบร้อยแล้ว');
                }

                // เพิ่ม discount_type ถ้ายังไม่มี
                if (!Schema::hasColumn('orders', 'discount_type')) {
                    $table->string('discount_type')->default('fixed')->after('subtotal');
                    Log::info('เพิ่มคอลัมน์ discount_type ลงในตาราง orders เรียบร้อยแล้ว');
                }

                // เพิ่ม shipping_cost ถ้ายังไม่มี
                if (!Schema::hasColumn('orders', 'shipping_cost')) {
                    $table->decimal('shipping_cost', 15, 2)->default(0)->after('tax_amount');
                    Log::info('เพิ่มคอลัมน์ shipping_cost ลงในตาราง orders เรียบร้อยแล้ว');
                }

                // เพิ่ม shipping_method ถ้ายังไม่มี
                if (!Schema::hasColumn('orders', 'shipping_method')) {
                    $table->string('shipping_method')->nullable()->after('shipping_address');
                    Log::info('เพิ่มคอลัมน์ shipping_method ลงในตาราง orders เรียบร้อยแล้ว');
                }
                
                // เพิ่มคอลัมน์อื่นๆ ที่มีใน Model แต่อาจจะขาดหายไปในตาราง
                if (!Schema::hasColumn('orders', 'tracking_number')) {
                    $table->string('tracking_number')->nullable();
                }

                if (!Schema::hasColumn('orders', 'shipping_notes')) {
                    $table->text('shipping_notes')->nullable();
                }
            });
        } catch (\Exception $e) {
            Log::error('ไม่สามารถเพิ่มคอลัมน์ที่ขาดหายไปในตาราง orders: ' . $e->getMessage());
            echo "เกิดข้อผิดพลาดในการเพิ่มคอลัมน์ที่ขาดหายไป: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('orders', function (Blueprint $table) {
                // ลบคอลัมน์ที่เพิ่มเข้าไปในขั้นตอน up()
                if (Schema::hasColumn('orders', 'tax_rate')) {
                    $table->dropColumn('tax_rate');
                }
                if (Schema::hasColumn('orders', 'discount_type')) {
                    $table->dropColumn('discount_type');
                }
                if (Schema::hasColumn('orders', 'shipping_cost')) {
                    $table->dropColumn('shipping_cost');
                }
                if (Schema::hasColumn('orders', 'shipping_method')) {
                    $table->dropColumn('shipping_method');
                }
                if (Schema::hasColumn('orders', 'tracking_number')) {
                    $table->dropColumn('tracking_number');
                }
                if (Schema::hasColumn('orders', 'shipping_notes')) {
                    $table->dropColumn('shipping_notes');
                }
            });
        } catch (\Exception $e) {
            Log::error('ไม่สามารถลบคอลัมน์ที่เพิ่มไปในตาราง orders: ' . $e->getMessage());
            echo "เกิดข้อผิดพลาดในการลบคอลัมน์: " . $e->getMessage() . "\n";
        }
    }
};
