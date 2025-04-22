<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
                $table->dateTime('order_date');
                $table->dateTime('delivery_date')->nullable();
                $table->string('status')->check("status IN ('draft', 'pending', 'approved', 'processing', 'shipped', 'completed', 'canceled')");
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
                
                // เพิ่มฟิลด์พนักงานขาย
                $table->foreignId('sales_person_id')->nullable()->constrained('employees')->onDelete('set null');
                
                // ข้อมูลผู้ใช้ที่เกี่ยวข้อง
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('last_modified_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('approved_at')->nullable();
                
                $table->timestamps();
                $table->softDeletes();
            });
        } else if (!Schema::hasColumn('orders', 'sales_person_id')) {
            // เพิ่มคอลัมน์ sales_person_id ถ้าตารางมีอยู่แล้วแต่ยังไม่มีคอลัมน์นี้
            Schema::table('orders', function (Blueprint $table) {
                $table->foreignId('sales_person_id')->nullable()->constrained('employees')->onDelete('set null');
            });
        }

        // จัดการกับตาราง order_items โดยตรวจสอบการใช้งาน SQLite
        try {
            // เช็คว่าเป็น SQLite หรือไม่
            $connection = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
            $isSQLite = ($connection === 'sqlite');
            
            // เช็คว่าตาราง order_items มีอยู่แล้วหรือไม่
            $tableExists = false;
            if ($isSQLite) {
                $tableExists = !empty(DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name='order_items'"));
            } else {
                $tableExists = Schema::hasTable('order_items');
            }
            
            if (!$tableExists) {
                // กรณีเป็น SQLite ใช้ raw SQL สำหรับการสร้างตาราง
                if ($isSQLite) {
                    DB::statement('CREATE TABLE order_items (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        order_id INTEGER NOT NULL,
                        product_id INTEGER NOT NULL,
                        unit_id INTEGER NULL,
                        quotation_item_id INTEGER NULL,
                        quantity DECIMAL(15, 2) NOT NULL DEFAULT 0,
                        price DECIMAL(15, 2) NOT NULL DEFAULT 0,
                        unit_price DECIMAL(15, 2) NOT NULL DEFAULT 0,
                        discount DECIMAL(15, 2) NOT NULL DEFAULT 0,
                        discount_percentage DECIMAL(15, 2) NOT NULL DEFAULT 0,
                        discount_amount DECIMAL(15, 2) NOT NULL DEFAULT 0,
                        tax_rate DECIMAL(5, 2) NOT NULL DEFAULT 0,
                        tax_percentage DECIMAL(15, 2) NOT NULL DEFAULT 0,
                        tax_amount DECIMAL(15, 2) NOT NULL DEFAULT 0,
                        subtotal DECIMAL(15, 2) NOT NULL DEFAULT 0,
                        total DECIMAL(15, 2) NOT NULL DEFAULT 0,
                        description TEXT NULL,
                        item_order INTEGER NOT NULL DEFAULT 0,
                        metadata JSON NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        deleted_at TIMESTAMP NULL,
                        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                        FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE SET NULL,
                        FOREIGN KEY (quotation_item_id) REFERENCES quotation_items(id) ON DELETE SET NULL
                    )');
                    
                    // สร้าง indexes
                    DB::statement('CREATE INDEX idx_order_items_order_id ON order_items(order_id)');
                    DB::statement('CREATE INDEX idx_order_items_product_id ON order_items(product_id)');
                    DB::statement('CREATE INDEX idx_order_items_quotation_item_id ON order_items(quotation_item_id)');
                    
                    Log::info('สร้างตาราง order_items ด้วย raw SQL สำหรับ SQLite เรียบร้อยแล้ว');
                } else {
                    // กรณีไม่ใช่ SQLite ใช้ Schema Builder ปกติ
                    Schema::create('order_items', function (Blueprint $table) {
                        $table->id();
                        $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
                        $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                        $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('set null');
                        $table->foreignId('quotation_item_id')->nullable()->constrained('quotation_items')->nullOnDelete();
                        $table->decimal('quantity', 15, 2);
                        $table->decimal('price', 15, 2);
                        $table->decimal('unit_price', 15, 2)->default(0);
                        $table->decimal('discount', 15, 2)->default(0);
                        $table->decimal('discount_percentage', 15, 2)->default(0);
                        $table->decimal('discount_amount', 15, 2)->default(0);
                        $table->decimal('tax_rate', 5, 2)->default(0);
                        $table->decimal('tax_percentage', 15, 2)->default(0);
                        $table->decimal('tax_amount', 15, 2)->default(0);
                        $table->decimal('subtotal', 15, 2)->default(0);
                        $table->decimal('total', 15, 2)->default(0);
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
                }
            } else if ($isSQLite) {
                // กรณีเป็น SQLite และตารางมีอยู่แล้ว ตรวจสอบโครงสร้างและปรับปรุง
                
                // ตรวจสอบโครงสร้างตารางปัจจุบัน
                $columns = [];
                $columnInfo = DB::select("PRAGMA table_info(order_items)");
                
                foreach ($columnInfo as $column) {
                    $columns[] = $column->name;
                }
                
                // สร้างตารางชั่วคราว
                DB::statement('CREATE TABLE order_items_temp (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    order_id INTEGER NOT NULL,
                    product_id INTEGER NOT NULL,
                    unit_id INTEGER NULL,
                    quotation_item_id INTEGER NULL,
                    quantity DECIMAL(15, 2) NOT NULL DEFAULT 0,
                    price DECIMAL(15, 2) NOT NULL DEFAULT 0,
                    unit_price DECIMAL(15, 2) NOT NULL DEFAULT 0,
                    discount DECIMAL(15, 2) NOT NULL DEFAULT 0,
                    discount_percentage DECIMAL(15, 2) NOT NULL DEFAULT 0,
                    discount_amount DECIMAL(15, 2) NOT NULL DEFAULT 0,
                    tax_rate DECIMAL(5, 2) NOT NULL DEFAULT 0,
                    tax_percentage DECIMAL(15, 2) NOT NULL DEFAULT 0,
                    tax_amount DECIMAL(15, 2) NOT NULL DEFAULT 0,
                    subtotal DECIMAL(15, 2) NOT NULL DEFAULT 0,
                    total DECIMAL(15, 2) NOT NULL DEFAULT 0,
                    description TEXT NULL,
                    item_order INTEGER NOT NULL DEFAULT 0,
                    metadata JSON NULL,
                    created_at TIMESTAMP NULL,
                    updated_at TIMESTAMP NULL,
                    deleted_at TIMESTAMP NULL,
                    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                    FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE SET NULL,
                    FOREIGN KEY (quotation_item_id) REFERENCES quotation_items(id) ON DELETE SET NULL
                )');
                
                // คัดลอกข้อมูล (ถ้ามี)
                if (count($columns) > 1) {
                    // สร้าง column list ที่มีอยู่ในทั้งสองตาราง
                    $commonColumns = array_intersect($columns, [
                        'id', 'order_id', 'product_id', 'unit_id', 'quotation_item_id', 'quantity', 
                        'price', 'unit_price', 'discount', 'discount_percentage', 'discount_amount', 
                        'tax_rate', 'tax_percentage', 'tax_amount', 'subtotal', 'total', 
                        'description', 'item_order', 'metadata', 'created_at', 'updated_at', 'deleted_at'
                    ]);
                    
                    $columnList = implode(', ', $commonColumns);
                    
                    if (!empty($columnList)) {
                        DB::statement("INSERT INTO order_items_temp ($columnList) SELECT $columnList FROM order_items");
                    }
                }
                
                // ลบตารางเก่า
                DB::statement('DROP TABLE order_items');
                
                // เปลี่ยนชื่อตารางชั่วคราวเป็นชื่อจริง
                DB::statement('ALTER TABLE order_items_temp RENAME TO order_items');
                
                // สร้าง indexes
                DB::statement('CREATE INDEX idx_order_items_order_id ON order_items(order_id)');
                DB::statement('CREATE INDEX idx_order_items_product_id ON order_items(product_id)');
                DB::statement('CREATE INDEX idx_order_items_quotation_item_id ON order_items(quotation_item_id)');
                
                Log::info('อัพเดทโครงสร้างตาราง order_items สำหรับ SQLite เรียบร้อยแล้ว');
            } else {
                // กรณีไม่ใช่ SQLite และตารางมีอยู่แล้ว เพิ่มคอลัมน์ที่อาจหายไป
                Schema::table('order_items', function (Blueprint $table) {
                    if (!Schema::hasColumn('order_items', 'unit_price')) {
                        $table->decimal('unit_price', 15, 2)->default(0)->after('price');
                    }
                    
                    if (!Schema::hasColumn('order_items', 'discount_percentage')) {
                        $table->decimal('discount_percentage', 15, 2)->default(0)->after('discount');
                    }
                    
                    if (!Schema::hasColumn('order_items', 'discount_amount')) {
                        $table->decimal('discount_amount', 15, 2)->default(0)->after('discount_percentage');
                    }
                    
                    if (!Schema::hasColumn('order_items', 'tax_percentage')) {
                        $table->decimal('tax_percentage', 15, 2)->default(0)->after('tax_rate');
                    }
                    
                    if (!Schema::hasColumn('order_items', 'subtotal')) {
                        $table->decimal('subtotal', 15, 2)->default(0)->after('tax_amount');
                    }
                    
                    if (!Schema::hasColumn('order_items', 'total')) {
                        $table->decimal('total', 15, 2)->default(0)->after('subtotal');
                    }
                });
                
                Log::info('อัพเดทโครงสร้างตาราง order_items เรียบร้อยแล้ว');
            }
        } catch (\Exception $e) {
            Log::error('เกิดข้อผิดพลาดในการปรับปรุงตาราง order_items: ' . $e->getMessage());
            echo "เกิดข้อผิดพลาดในการปรับปรุงตาราง order_items: " . $e->getMessage() . "\n";
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
