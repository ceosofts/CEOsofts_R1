<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // เพิ่มคอลัมน์พื้นฐาน
            if (!Schema::hasColumn('products', 'stock_quantity')) {
                $table->decimal('stock_quantity', 10, 2)->default(0)->after('cost');
            }
            if (!Schema::hasColumn('products', 'current_stock')) {
                $table->decimal('current_stock', 10, 2)->default(0)->after('stock_quantity');
            }
            if (!Schema::hasColumn('products', 'min_stock')) {
                $table->decimal('min_stock', 10, 2)->default(0)->after('current_stock');
            }
            if (!Schema::hasColumn('products', 'max_stock')) {
                $table->decimal('max_stock', 10, 2)->default(0)->after('min_stock');
            }
            if (!Schema::hasColumn('products', 'weight')) {
                $table->decimal('weight', 8, 2)->nullable()->after('max_stock');
            }
            if (!Schema::hasColumn('products', 'dimension')) {
                $table->json('dimension')->nullable()->after('weight');
            }
            if (!Schema::hasColumn('products', 'location')) {
                $table->string('location')->nullable()->after('dimension');
            }

            // เพิ่ม Foreign Keys
            if (!Schema::hasColumn('products', 'unit_id')) {
                $table->foreignId('unit_id')->nullable()->after('category_id')
                    ->constrained('units')->onDelete('set null');
            }
            if (!Schema::hasColumn('products', 'tax_id')) {
                $table->foreignId('tax_id')->nullable()->after('unit_id')
                    ->constrained('taxes')->onDelete('set null');
            }

            // เพิ่ม Flags
            if (!Schema::hasColumn('products', 'is_sellable')) {
                $table->boolean('is_sellable')->default(true)->after('is_active');
            }
            if (!Schema::hasColumn('products', 'is_purchasable')) {
                $table->boolean('is_purchasable')->default(true)->after('is_sellable');
            }
            
            // เพิ่ม indexes
            $table->index(['company_id', 'sku']);
            $table->index(['company_id', 'barcode']);
            $table->index(['current_stock', 'min_stock']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // ลบ indexes
            $table->dropIndex(['company_id', 'sku']);
            $table->dropIndex(['company_id', 'barcode']);
            $table->dropIndex(['current_stock', 'min_stock']);

            // ลบ foreign keys
            $table->dropForeign(['unit_id']);
            $table->dropForeign(['tax_id']);

            // ลบคอลัมน์
            $table->dropColumn([
                'stock_quantity',
                'current_stock',
                'min_stock',
                'max_stock',
                'weight',
                'dimension',
                'location',
                'unit_id',
                'tax_id',
                'is_sellable',
                'is_purchasable',
            ]);
        });
    }
};
