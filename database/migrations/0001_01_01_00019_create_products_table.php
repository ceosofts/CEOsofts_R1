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
     * - 0001_01_01_00044_add_current_stock_to_products_table.php
     * - 0001_01_01_00045_add_unit_id_to_products_table.php
     * - 0001_01_01_00033_add_missing_columns_to_product_categories_table.php
     */
    public function up(): void
    {
        // สร้างตารางหมวดหมู่สินค้า
        if (!Schema::hasTable('product_categories')) {
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
                // คอลัมน์เพิ่มเติมที่ย้ายมาจาก add_missing_columns_to_product_categories_table.php
                $table->string('icon')->nullable();  // ไอคอนสำหรับหมวดหมู่
                $table->string('image')->nullable(); // รูปภาพสำหรับหมวดหมู่
                $table->string('slug')->nullable(); // URL-friendly name
                $table->integer('display_order')->default(0); // ลำดับการแสดงผล
                $table->boolean('is_featured')->default(false); // หมวดหมู่แนะนำหรือไม่
                $table->boolean('is_visible')->default(true); // แสดงบนหน้าเว็บหรือไม่
                
                // เพิ่มคอลัมน์ที่ขาดและใช้ในการ seed
                $table->integer('level')->default(0);  // ระดับความลึกของหมวดหมู่
                $table->string('path')->nullable();    // เส้นทางแบบ hierarchical
                
                $table->timestamps();
                $table->softDeletes();

                // Indexes
                $table->index('company_id');
                $table->index('name');
                $table->index('code');
                $table->index('parent_id');
                $table->index('is_active');
                // Index เพิ่มเติมสำหรับคอลัมน์ใหม่
                $table->index('slug');
                $table->index('display_order');
                $table->index('is_featured');
                $table->index('is_visible');
            });

            Log::info('สร้างตาราง product_categories เรียบร้อยแล้ว');
        } else {
            // ตรวจสอบและเพิ่มคอลัมน์ที่หายไปในกรณีที่ตารางมีอยู่แล้ว (จากไฟล์ add_missing_columns_to_product_categories_table.php)
            Schema::table('product_categories', function (Blueprint $table) {
                // เพิ่มคอลัมน์ที่อาจหายไป
                if (!Schema::hasColumn('product_categories', 'icon')) {
                    $table->string('icon')->nullable();
                }
                if (!Schema::hasColumn('product_categories', 'image')) {
                    $table->string('image')->nullable();
                }
                if (!Schema::hasColumn('product_categories', 'slug')) {
                    $table->string('slug')->nullable();
                }
                if (!Schema::hasColumn('product_categories', 'display_order')) {
                    $table->integer('display_order')->default(0);
                }
                if (!Schema::hasColumn('product_categories', 'is_featured')) {
                    $table->boolean('is_featured')->default(false);
                }
                if (!Schema::hasColumn('product_categories', 'is_visible')) {
                    $table->boolean('is_visible')->default(true);
                }
                
                // เพิ่มคอลัมน์ที่ขาดสำหรับการ seed
                if (!Schema::hasColumn('product_categories', 'level')) {
                    $table->integer('level')->default(0);
                }
                if (!Schema::hasColumn('product_categories', 'path')) {
                    $table->string('path')->nullable();
                }

                // เพิ่ม index สำหรับคอลัมน์ใหม่
                if (!Schema::hasIndex('product_categories', 'product_categories_slug_index')) {
                    $table->index('slug');
                }
                if (!Schema::hasIndex('product_categories', 'product_categories_display_order_index')) {
                    $table->index('display_order');
                }
                if (!Schema::hasIndex('product_categories', 'product_categories_is_featured_index')) {
                    $table->index('is_featured');
                }
                if (!Schema::hasIndex('product_categories', 'product_categories_is_visible_index')) {
                    $table->index('is_visible');
                }
            });

            Log::info('อัปเดตโครงสร้างตาราง product_categories เรียบร้อยแล้ว');
        }

        // สร้างตารางสินค้า
        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('uuid', 36)->unique(); // จาก add_uuid_to_products_table.php
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->foreignId('category_id')->nullable()->constrained('product_categories')->onDelete('set null');
                $table->foreignId('unit_id')->nullable(); // จาก 0001_01_01_00045_add_unit_id_to_products_table.php
                $table->string('name');
                $table->string('code', 30)->nullable();
                $table->text('description')->nullable();
                $table->decimal('price', 15, 2)->default(0);
                $table->decimal('cost', 15, 2)->default(0);
                $table->string('unit', 30)->nullable();
                $table->string('sku', 50)->nullable();
                $table->string('barcode', 50)->nullable();
                $table->integer('stock_quantity')->default(0); // จำนวนสต็อกเริ่มต้น
                $table->integer('current_stock')->default(0);  // จาก 0001_01_01_00044_add_current_stock_to_products_table.php
                $table->string('location', 100)->nullable(); // ตำแหน่งในคลังสินค้า
                $table->string('image')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_inventory_tracked')->default(true);
                $table->boolean('is_service')->default(false);
                $table->json('dimension')->nullable();

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
                $table->index('unit_id'); // จาก 0001_01_01_00045_add_unit_id_to_products_table.php
                $table->index('name');
                $table->index('code');
                $table->index('sku');
                $table->index('barcode');
                $table->index('location');
                $table->index('is_active');
                $table->index('uuid'); // index สำหรับคอลัมน์ uuid
                $table->index('is_featured'); // indexes เพิ่มเติม
                $table->index('is_bestseller');
                $table->index('is_new');
                $table->index('inventory_status');
                $table->index('is_service');
                $table->index('current_stock'); // จาก 0001_01_01_00044_add_current_stock_to_products_table.php

                // Unique constraints
                $table->unique(['company_id', 'code']);
                $table->unique(['company_id', 'sku']);
            });

            Log::info('สร้างตาราง products เรียบร้อยแล้ว');
        }

        // ข้ามส่วนนี้เพราะตาราง quotation_items ได้ถูกสร้างไปแล้วในไฟล์ 0001_01_01_00013_create_quotations_table.php
        // ตรวจสอบว่าตาราง quotation_items มีคอลัมน์ที่จำเป็นหรือไม่
        if (Schema::hasTable('quotation_items')) {
            // ตรวจสอบแต่ไม่สร้างตารางใหม่ เนื่องจากได้สร้างไว้แล้วในไฟล์อื่น
            $columns = [
                'price' => 'unit_price', // ถ้ามี price ให้ใช้ unit_price แทน
                'discount' => 'discount_amount', // ถ้ามี discount ให้ใช้ discount_amount แทน
                'sort_order' => 'metadata' // ถ้าจำเป็นต้องมี sort_order ให้เก็บใน metadata
            ];
            
            foreach ($columns as $oldColumn => $newColumn) {
                if (Schema::hasColumn('quotation_items', $oldColumn) && !Schema::hasColumn('quotation_items', $newColumn)) {
                    Log::info("พบคอลัมน์ $oldColumn แต่ไม่พบ $newColumn ในตาราง quotation_items");
                }
            }
        }

        // ย้ายส่วนของ order_items ไปไฟล์ 0001_01_01_00016_create_orders_table.php แล้ว
        // Log บอกว่าได้ย้ายไปแล้ว
        Log::info('ส่วนของการสร้างตาราง order_items ได้ย้ายไปไฟล์ 0001_01_01_00016_create_orders_table.php แล้ว');

        // สร้างตารางรายการในใบแจ้งหนี้เฉพาะเมื่อยังไม่มีตารางนี้
        if (!Schema::hasTable('invoice_items')) {
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
            Log::info('สร้างตาราง invoice_items เรียบร้อยแล้ว');
        } else {
            Log::info('พบตาราง invoice_items อยู่แล้ว ข้ามการสร้าง');
        }

        Log::info('การปรับปรุงโครงสร้างฐานข้อมูลเสร็จสิ้น');
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        // ระมัดระวังการลบตาราง - ในกรณีนี้ไม่ควรลบตารางที่อาจถูกสร้างจากไฟล์อื่น
        // ลบเฉพาะตารางที่สร้างในไฟล์นี้
        
        // ตรวจสอบก่อนลบว่าตารางนั้นสร้างจากไฟล์นี้หรือไม่
        // เนื่องจากยากที่จะระบุแหล่งที่มาของตาราง จึงลบเฉพาะตารางหลักที่แน่ใจ
        
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
        
        // ไม่ลบตารางต่อไปนี้เนื่องจากอาจถูกสร้างจากไฟล์อื่น
        // Schema::dropIfExists('invoice_items');
        // Schema::dropIfExists('order_items');
        // Schema::dropIfExists('quotation_items');
    }
};
