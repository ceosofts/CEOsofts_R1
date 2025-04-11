<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;


return new class extends Migration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        // สร้างตารางการเคลื่อนไหวสินค้า (stock_movements) เฉพาะเมื่อยังไม่มีตารางนี้
        if (!Schema::hasTable('stock_movements')) {
            Schema::create('stock_movements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('reference_type', 50); // order, invoice, adjustment, etc
                $table->unsignedBigInteger('reference_id');
                $table->decimal('quantity', 15, 2); // จำนวนที่เปลี่ยนแปลง (+ เข้า, - ออก)
                $table->decimal('before_quantity', 15, 2); // จำนวนก่อนเปลี่ยนแปลง
                $table->decimal('after_quantity', 15, 2); // จำนวนหลังเปลี่ยนแปลง
                $table->foreignId('unit_id')->nullable()->constrained()->onDelete('set null');
                $table->decimal('unit_cost', 15, 2)->nullable(); // ต้นทุนต่อหน่วย
                $table->text('note')->nullable(); // บันทึกเพิ่มเติม
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
                
                // Indexes
                $table->index('company_id');
                $table->index('product_id');
                $table->index(['reference_type', 'reference_id']);
                $table->index('created_at');
                $table->index('created_by');
            });
        } else {
            // ตรวจสอบว่า indexes มีอยู่แล้วหรือไม่ และเพิ่มถ้ายังไม่มี
            Schema::table('stock_movements', function (Blueprint $table) {
                // ตรวจสอบและเพิ่ม indexes ที่จำเป็น (ตัวอย่าง - ต้องปรับแต่งตามที่เหมาะสม)
                if (!Schema::hasIndex('stock_movements', 'stock_movements_reference_type_reference_id_index')) {
                    $table->index(['reference_type', 'reference_id']);
                }
            });
        }
        
        // ปรับปรุงตาราง products เพิ่มฟิลด์ที่จำเป็น (ถ้ายังไม่มี)
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                // แก้ไขจุดนี้ โดยไม่ระบุ after คอลัมน์ที่อาจไม่มีอยู่
                if (!Schema::hasColumn('products', 'is_inventory')) {
                    $table->boolean('is_inventory')->default(true);
                }
                if (!Schema::hasColumn('products', 'is_service')) {
                    $table->boolean('is_service')->default(false);
                }
                if (!Schema::hasColumn('products', 'weight')) {
                    $table->decimal('weight', 10, 3)->nullable();
                }
                if (!Schema::hasColumn('products', 'length')) {
                    $table->decimal('length', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('products', 'width')) {
                    $table->decimal('width', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('products', 'height')) {
                    $table->decimal('height', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('products', 'is_featured')) {
                    $table->boolean('is_featured')->default(false);
                }
                if (!Schema::hasColumn('products', 'tags')) {
                    $table->json('tags')->nullable();
                }
                if (!Schema::hasColumn('products', 'attributes')) {
                    $table->json('attributes')->nullable();
                }
                if (!Schema::hasColumn('products', 'created_by')) {
                    $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                }
                if (!Schema::hasColumn('products', 'updated_by')) {
                    $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
                }
                
                // Indexes
                try {
                    if (!Schema::hasIndex('products', 'products_is_inventory_index')) {
                        $table->index('is_inventory');
                    }
                    if (!Schema::hasIndex('products', 'products_is_service_index')) {
                        $table->index('is_service');
                    }
                    if (!Schema::hasIndex('products', 'products_is_featured_index')) {
                        $table->index('is_featured');
                    }
                } catch (\Exception $e) {
                    // ถ้าเกิดข้อผิดพลาดเกี่ยวกับ index ให้บันทึกเป็น log แทนการล้มเหลวทั้ง migration
                    \Log::warning("ไม่สามารถเพิ่ม index ในตาราง products: " . $e->getMessage());
                }
            });
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        // ให้ลบตารางเฉพาะเมื่อมีตารางอยู่จริง
        if (Schema::hasTable('stock_movements')) {
            Schema::dropIfExists('stock_movements');
        }
        
        // ถอนการปรับปรุงตาราง products
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $columns = [
                    'is_inventory', 'is_service', 'weight', 'length',
                    'width', 'height', 'is_featured', 'tags', 'attributes',
                    'created_by', 'updated_by'
                ];
                
                foreach ($columns as $column) {
                    if (Schema::hasColumn('products', $column)) {
                        try {
                            if (in_array($column, ['is_inventory', 'is_service', 'is_featured'])) {
                                $indexName = "products_{$column}_index";
                                if (Schema::hasIndex('products', $indexName)) {
                                    $table->dropIndex($indexName);
                                }
                            }
                            if (in_array($column, ['created_by', 'updated_by'])) {
                                $foreignName = "products_{$column}_foreign";
                                if (Schema::hasIndex('products', $foreignName)) {
                                    $table->dropForeign($foreignName);
                                }
                            }
                            $table->dropColumn($column);
                        } catch (\Exception $e) {
                            \Log::warning("ไม่สามารถลบคอลัมน์ {$column} ในตาราง products: " . $e->getMessage());
                        }
                    }
                }
            });
        }
    }
};
