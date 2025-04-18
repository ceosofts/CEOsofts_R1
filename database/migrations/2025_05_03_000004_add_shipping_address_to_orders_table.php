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
            // เพิ่มคอลัมน์ shipping_address ถ้ายังไม่มี
            if (!Schema::hasColumn('orders', 'shipping_address')) {
                $table->text('shipping_address')->nullable();
            }
            
            // เพิ่มคอลัมน์ shipping_method ถ้ายังไม่มี (ป้องกันกรณีเกิดปัญหาเดียวกัน)
            if (!Schema::hasColumn('orders', 'shipping_method')) {
                $table->string('shipping_method')->nullable();
            }
            
            // เพิ่มคอลัมน์ shipping_cost ถ้ายังไม่มี (ป้องกันกรณีเกิดปัญหาเดียวกัน)
            if (!Schema::hasColumn('orders', 'shipping_cost')) {
                $table->decimal('shipping_cost', 15, 2)->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = ['shipping_address', 'shipping_method', 'shipping_cost'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
