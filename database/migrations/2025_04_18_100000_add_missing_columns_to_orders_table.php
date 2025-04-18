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
            // ตรวจสอบว่าคอลัมน์มีอยู่แล้วหรือไม่ก่อนเพิ่ม
            if (!Schema::hasColumn('orders', 'subtotal')) {
                $table->decimal('subtotal', 15, 2)->default(0)->after('total_amount');
            }
            
            if (!Schema::hasColumn('orders', 'discount_type')) {
                $table->string('discount_type')->nullable()->after('subtotal');
            }
            
            if (!Schema::hasColumn('orders', 'discount_amount')) {
                $table->decimal('discount_amount', 15, 2)->default(0)->after('discount_type');
            }
            
            if (!Schema::hasColumn('orders', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->default(0)->after('discount_amount');
            }
            
            if (!Schema::hasColumn('orders', 'tax_amount')) {
                $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_rate');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'subtotal',
                'discount_type',
                'discount_amount',
                'tax_rate',
                'tax_amount'
            ]);
        });
    }
};
