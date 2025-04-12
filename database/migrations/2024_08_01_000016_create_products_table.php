<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migration.
     * รวมการทำงานจากไฟล์:
     * - 2024_08_01_000040_add_missing_columns_to_products_table.php
     * - 2024_08_01_000041_add_uuid_to_products_table.php
     */
    public function up(): void
    {
        // สร้างตารางหมวดหมู่สินค้า
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code', 30)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('product_categories')->onDelete('set null');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('company_id');
            $table->index('name');
            $table->index('code');
            $table->index('parent_id');
            $table->index('is_active');
        });

        // สร้างตารางสินค้า
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 36)->unique(); // จาก add_uuid_to_products_table.php
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->onDelete('set null');
            $table->string('name');
            $table->string('code', 30)->nullable();
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('cost', 15, 2)->default(0);
            $table->string('unit', 30)->nullable();
            $table->string('sku', 50)->nullable();
            $table->string('barcode', 50)->nullable();
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_inventory_tracked')->default(true);

            // คอลัมน์เพิ่มเติมจาก add_missing_columns_to_products_table.php
            $table->decimal('list_price', 15, 2)->nullable(); // ราคาแนะนำ
            $table->decimal('wholesale_price', 15, 2)->nullable(); // ราคาขายส่ง
            $table->decimal('special_price', 15, 2)->nullable(); // ราคาพิเศษ
            $table->date('special_price_start_date')->nullable(); // วันเริ่มต้นราคาพิเศษ
            $table->date('special_price_end_date')->nullable(); // วันสิ้นสุดราคาพิเศษ
            $table->boolean('is_featured')->default(false); // เป็นสินค้าแนะนำหรือไม่
            $table->boolean('is_bestseller')->default(false); // เป็นสินค้าขายดีหรือไม่
            $table->boolean('is_new')->default(false); // เป็นสินค้าใหม่หรือไม่
            $table->string('tax_class', 50)->nullable(); // ประเภทภาษี
            $table->decimal('weight', 10, 2)->nullable(); // น้ำหนัก
            $table->decimal('length', 10, 2)->nullable(); // ความยาว
            $table->decimal('width', 10, 2)->nullable(); // ความกว้าง
            $table->decimal('height', 10, 2)->nullable(); // ความสูง
            $table->string('weight_unit', 10)->default('kg'); // หน่วยน้ำหนัก
            $table->string('dimension_unit', 10)->default('cm'); // หน่วยความยาว
            $table->integer('min_stock')->default(0); // สต็อกขั้นต่ำ
            $table->integer('max_stock')->nullable(); // สต็อกสูงสุด
            $table->boolean('allow_backorder')->default(false); // อนุญาตให้สั่งซื้อเกินสต็อกหรือไม่
            $table->string('inventory_status', 20)->default('in_stock'); // สถานะสินค้าคงคลัง
            $table->foreignId('brand_id')->nullable(); // แบรนด์สินค้า
            $table->foreignId('vendor_id')->nullable(); // ผู้ขาย/ซัพพลายเออร์
            $table->json('attributes')->nullable(); // คุณสมบัติเพิ่มเติม
            $table->json('tags')->nullable(); // แท็กสินค้า
            $table->string('warranty', 100)->nullable(); // รายละเอียดการรับประกัน
            $table->string('condition', 20)->default('new'); // สภาพสินค้า (new, used, refurbished)
            $table->date('available_from')->nullable(); // วันที่เริ่มขาย
            $table->date('available_to')->nullable(); // วันที่สิ้นสุดการขาย

            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('company_id');
            $table->index('category_id');
            $table->index('name');
            $table->index('code');
            $table->index('sku');
            $table->index('barcode');
            $table->index('is_active');
            $table->index('uuid'); // index สำหรับคอลัมน์ uuid
            $table->index('is_featured'); // indexes เพิ่มเติม
            $table->index('is_bestseller');
            $table->index('is_new');
            $table->index('inventory_status');

            // Unique constraints
            $table->unique(['company_id', 'code']);
            $table->unique(['company_id', 'sku']);
        });

        // สร้างตารางรายการในใบเสนอราคา
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->string('description');
            $table->decimal('quantity', 15, 2);
            $table->decimal('price', 15, 2);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->integer('sort_order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('quotation_id');
            $table->index('product_id');
        });

        // สร้างตารางรายการในคำสั่งซื้อ
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('quotation_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->string('description');
            $table->decimal('quantity', 15, 2);
            $table->decimal('price', 15, 2);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->integer('sort_order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('order_id');
            $table->index('product_id');
            $table->index('quotation_item_id');
        });

        // สร้างตารางรายการในใบแจ้งหนี้
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->string('description');
            $table->decimal('quantity', 15, 2);
            $table->decimal('price', 15, 2);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2);
            $table->integer('sort_order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('invoice_id');
            $table->index('product_id');
            $table->index('order_item_id');
        });

        Log::info('สร้างตาราง products และตารางที่เกี่ยวข้องเรียบร้อยแล้ว รวมถึงคอลัมน์เพิ่มเติมจาก migrations อื่น');
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
    }
};
