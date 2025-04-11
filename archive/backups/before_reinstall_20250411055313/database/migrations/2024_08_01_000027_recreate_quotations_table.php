<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // รัน drop foreign key บน orders ก่อนเสมอ
        $this->dropOrdersForeignKeys();
        
        // ลบตารางเดิมก่อน (ถ้ามี)
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
        
        // สร้างตารางใหม่
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('quotation_number')->unique();
            $table->date('quotation_date');
            $table->date('valid_until')->nullable();
            $table->string('reference')->nullable();
            $table->string('status')->default('draft'); // draft, approved, rejected, expired, cancelled
            $table->string('currency', 3)->default('THB');
            $table->decimal('exchange_rate', 15, 6)->default(1.0);
            $table->string('discount_type')->default('fixed'); // fixed, percentage, none
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->boolean('tax_inclusive')->default(false);
            $table->decimal('tax_rate', 5, 2)->default(7.0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total_discount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // แทนที่จะใช้ unique() ซึ่งจะสร้าง index แบบ unique เฉพาะคอลัมน์นั้น
            // ใช้ index() จะช่วยให้การค้นหาเร็วขึ้นโดยไม่ต้องมีข้อจำกัด unique
            $table->index(['company_id', 'quotation_number']);
            $table->index(['company_id', 'customer_id']);
            $table->index(['company_id', 'status']);
            $table->index('quotation_date');
            $table->index('valid_until');
        });

        // สร้างตาราง quotation_items
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description');
            $table->decimal('quantity', 15, 2)->default(1);
            $table->string('unit')->nullable();
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->string('discount_type')->default('none'); // none, fixed, percentage
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['quotation_id', 'product_id']);
        });
        
        // สร้าง foreign key constraints กลับไปใน orders (ถ้าจำเป็น)
        $this->recreateOrdersForeignKeys();
    }

    public function down(): void
    {
        $this->dropOrdersForeignKeys();
        
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
    }
    
    /**
     * ลบ foreign keys จากตาราง orders และ order_items
     */
    private function dropOrdersForeignKeys()
    {
        // ลบ foreign key จาก orders.quotation_id -> quotations.id
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'quotation_id')) {
            try {
                DB::statement('ALTER TABLE orders DROP FOREIGN KEY IF EXISTS orders_quotation_id_foreign');
            } catch (\Exception $e) {
                // ข้ามข้อผิดพลาด - อาจเป็นเพราะชื่อ constraint ไม่ตรง
            }
            
            // ลองวิธีอื่น
            try {
                $constraints = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'orders'
                    AND COLUMN_NAME = 'quotation_id'
                    AND REFERENCED_TABLE_NAME = 'quotations'
                ");
                
                foreach ($constraints as $constraint) {
                    if (isset($constraint->CONSTRAINT_NAME)) {
                        DB::statement("ALTER TABLE orders DROP FOREIGN KEY `{$constraint->CONSTRAINT_NAME}`");
                    }
                }
            } catch (\Exception $e) {
                // ข้ามข้อผิดพลาด
            }
        }
        
        // ลบ foreign key จาก order_items.quotation_item_id -> quotation_items.id
        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'quotation_item_id')) {
            try {
                DB::statement('ALTER TABLE order_items DROP FOREIGN KEY IF EXISTS order_items_quotation_item_id_foreign');
            } catch (\Exception $e) {
                // ข้ามข้อผิดพลาด
            }
            
            // ลองวิธีอื่น
            try {
                $constraints = DB::select("
                    SELECT CONSTRAINT_NAME 
                    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = 'order_items'
                    AND COLUMN_NAME = 'quotation_item_id'
                    AND REFERENCED_TABLE_NAME = 'quotation_items'
                ");
                
                foreach ($constraints as $constraint) {
                    if (isset($constraint->CONSTRAINT_NAME)) {
                        DB::statement("ALTER TABLE order_items DROP FOREIGN KEY `{$constraint->CONSTRAINT_NAME}`");
                    }
                }
            } catch (\Exception $e) {
                // ข้ามข้อผิดพลาด
            }
        }
    }
    
    /**
     * สร้าง foreign keys ใหม่
     */
    private function recreateOrdersForeignKeys()
    {
        // สร้าง foreign key ใหม่สำหรับ orders.quotation_id -> quotations.id
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'quotation_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->foreign('quotation_id')
                    ->references('id')
                    ->on('quotations')
                    ->nullOnDelete();
            });
        }
        
        // สร้าง foreign key ใหม่สำหรับ order_items.quotation_item_id -> quotation_items.id
        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'quotation_item_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->foreign('quotation_item_id')
                    ->references('id')
                    ->on('quotation_items')
                    ->nullOnDelete();
            });
        }
    }
};
