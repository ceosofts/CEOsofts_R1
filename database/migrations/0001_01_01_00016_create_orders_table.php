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
        // สร้างตาราง orders ถ้ายังไม่มี
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
                $table->foreignId('branch_office_id')->nullable()->constrained('branch_offices')->onDelete('set null');
                $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
                $table->foreignId('quotation_id')->nullable()->constrained('quotations')->onDelete('set null');
                $table->string('order_number')->unique();
                $table->date('order_date');
                $table->enum('status', ['draft', 'submitted', 'approved', 'processing', 'delivered', 'completed', 'cancelled'])->default('draft');
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->text('remarks')->nullable();
                $table->date('expected_delivery_date')->nullable();
                
                // ข้อมูลลูกค้าและการจัดส่ง
                $table->string('customer_po_number')->nullable();
                $table->text('shipping_address')->nullable();
                $table->string('shipping_city')->nullable();
                $table->string('shipping_state')->nullable();
                $table->string('shipping_postal_code')->nullable();
                $table->string('shipping_country')->default('Thailand');
                
                // ข้อมูลเพิ่มเติมเกี่ยวกับออเดอร์
                $table->text('notes')->nullable();
                $table->string('payment_terms')->nullable();
                
                // ข้อมูลผู้ใช้ที่เกี่ยวข้อง
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('last_modified_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('approved_at')->nullable();
                
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // สร้างตาราง order_items ถ้ายังไม่มี
        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('set null');
                $table->foreignId('quotation_item_id')->nullable()->constrained('quotation_items')->nullOnDelete();
                $table->decimal('quantity', 15, 2);
                $table->decimal('price', 15, 2);
                $table->decimal('discount', 15, 2)->default(0);
                $table->decimal('tax_rate', 5, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->string('description')->nullable();
                $table->integer('item_order')->default(0);
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index('order_id');
                $table->index('product_id');
                $table->index('quotation_item_id');
            });
            Log::info('สร้างตาราง order_items เรียบร้อยแล้ว');
        } else {
            // ตรวจสอบและเพิ่มคอลัมน์ที่อาจหายไป
            if (!Schema::hasColumn('order_items', 'unit_id')) {
                Schema::table('order_items', function (Blueprint $table) {
                    $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('set null');
                });
            }
            
            if (!Schema::hasColumn('order_items', 'quotation_item_id')) {
                Schema::table('order_items', function (Blueprint $table) {
                    $table->foreignId('quotation_item_id')->nullable()->constrained('quotation_items')->nullOnDelete();
                });
            }
            
            if (!Schema::hasColumn('order_items', 'tax_rate')) {
                Schema::table('order_items', function (Blueprint $table) {
                    $table->decimal('tax_rate', 5, 2)->default(0);
                });
            }
            
            if (!Schema::hasColumn('order_items', 'tax_amount')) {
                Schema::table('order_items', function (Blueprint $table) {
                    $table->decimal('tax_amount', 15, 2)->default(0);
                });
            }
            
            if (!Schema::hasColumn('order_items', 'metadata')) {
                Schema::table('order_items', function (Blueprint $table) {
                    $table->json('metadata')->nullable();
                });
            }
            
            if (!Schema::hasColumn('order_items', 'deleted_at')) {
                Schema::table('order_items', function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
            
            Log::info('อัพเดทโครงสร้างตาราง order_items เรียบร้อยแล้ว');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
