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
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'price')) {
                $table->decimal('price', 15, 2)->default(0)->after('unit');
            }
            
            if (!Schema::hasColumn('order_items', 'unit_price')) {
                $table->decimal('unit_price', 15, 2)->default(0)->after('unit');
            }
            
            if (!Schema::hasColumn('order_items', 'subtotal')) {
                $table->decimal('subtotal', 15, 2)->default(0)->after('unit_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'price')) {
                $table->dropColumn('price');
            }
            
            if (Schema::hasColumn('order_items', 'unit_price')) {
                $table->dropColumn('unit_price');
            }
            
            if (Schema::hasColumn('order_items', 'subtotal')) {
                $table->dropColumn('subtotal');
            }
        });
    }
};
