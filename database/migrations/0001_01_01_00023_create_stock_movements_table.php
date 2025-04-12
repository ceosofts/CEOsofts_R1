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
     * สร้างตาราง stock_movements และรวมการเพิ่มคอลัมน์จากไฟล์ต่อไปนี้:
     *
     * - 2024_08_01_000042_add_missing_columns_to_stock_movements_table.php
     * - 2024_08_01_000043_add_processed_by_to_stock_movements_table.php
     * - 2024_08_01_000044_add_processed_at_to_stock_movements_table.php
     * - 2024_08_01_000045_add_metadata_to_stock_movements_table.php
     */
    public function up(): void
    {
        // ตรวจสอบว่าตาราง stock_movements มีอยู่หรือไม่
        $hasTable = Schema::hasTable('stock_movements');
        $stockMovementsData = [];

        // สำรองข้อมูลถ้าตารางมีอยู่แล้ว
        if ($hasTable) {
            try {
                Log::info('พบตาราง stock_movements อยู่แล้ว จะสำรองข้อมูลก่อนสร้างใหม่');
                $stockMovementsData = DB::table('stock_movements')->get()->toArray();
                Schema::dropIfExists('stock_movements');
            } catch (\Exception $e) {
                Log::warning('ไม่สามารถสำรองข้อมูล stock_movements: ' . $e->getMessage());
            }
        }

        // สร้างตาราง stock_movements
        Schema::create('stock_movements', function (Blueprint $table) {
            // คอลัมน์หลักจากไฟล์เดิม
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('type', 30); // เพิ่มคอลัมน์ type สำหรับประเภท (receive, issue, return, adjust)
            $table->string('movement_type')->nullable(); // IN, OUT (เก็บไว้เพื่อความเข้ากันได้กับระบบเก่า)
            $table->integer('quantity'); // จำนวนที่เคลื่อนไหว
            $table->decimal('before_quantity', 15, 2)->default(0); // จำนวนก่อนการเคลื่อนไหว
            $table->decimal('after_quantity', 15, 2); // จำนวนหลังการเคลื่อนไหว
            $table->string('reference_type', 50)->nullable(); // e.g. PurchaseOrder, SalesOrder
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('unit_price', 15, 4)->nullable(); // ราคาต่อหน่วย
            $table->decimal('unit_cost', 15, 4)->nullable(); // ต้นทุนต่อหน่วย
            $table->decimal('total_price', 15, 4)->nullable(); // ราคารวม
            $table->decimal('total_cost', 15, 4)->nullable(); // ต้นทุนรวม
            $table->string('location')->nullable();
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users');

            // จาก 2024_08_01_000042_add_missing_columns_to_stock_movements_table.php
            $table->string('status')->default('pending'); // pending, completed, cancelled
            $table->string('transaction_id')->nullable()->unique(); // รหัสธุรกรรม
            $table->string('source_location')->nullable(); // ตำแหน่งต้นทาง
            $table->string('destination_location')->nullable(); // ตำแหน่งปลายทาง
            $table->string('currency', 10)->default('THB'); // สกุลเงิน
            $table->date('transaction_date')->nullable(); // วันที่ทำธุรกรรม
            $table->string('reason_code', 50)->nullable(); // รหัสเหตุผล
            $table->text('reason_notes')->nullable(); // รายละเอียดเหตุผล

            // จาก 2024_08_01_000043_add_processed_by_to_stock_movements_table.php
            $table->foreignId('processed_by')->nullable()->constrained('users');

            // จาก 2024_08_01_000044_add_processed_at_to_stock_movements_table.php
            $table->timestamp('processed_at')->nullable();

            // จาก 2024_08_01_000045_add_metadata_to_stock_movements_table.php
            $table->json('metadata')->nullable();

            // คอลัมน์ระบบ
            $table->timestamps();
            $table->softDeletes();

            // สร้าง indexes
            $table->index(['product_id', 'movement_type', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
            $table->index(['company_id', 'product_id']);
            $table->index(['transaction_date']);
            $table->index(['status']);
            $table->index('type');
        });

        // นำข้อมูลเดิมกลับเข้าระบบ ถ้ามี
        if (!empty($stockMovementsData)) {
            try {
                foreach ($stockMovementsData as $movement) {
                    // แปลงข้อมูลเป็น array
                    $data = (array) $movement;

                    // ใส่ค่าเริ่มต้นสำหรับคอลัมน์ใหม่ที่อาจไม่มีในข้อมูลเดิม
                    if (!isset($data['status'])) {
                        $data['status'] = 'completed'; // ถือว่าข้อมูลเดิมเป็นข้อมูลที่เสร็จสมบูรณ์แล้ว
                    }

                    if (!isset($data['transaction_date']) && isset($data['created_at'])) {
                        $data['transaction_date'] = $data['created_at'];
                    }

                    if (!isset($data['metadata'])) {
                        $data['metadata'] = json_encode([
                            'imported' => true,
                            'imported_at' => now()->toDateTimeString(),
                            'note' => 'Imported from previous database structure'
                        ]);
                    }

                    // เพิ่มข้อมูลกลับเข้าไป
                    DB::table('stock_movements')->insert($data);
                }

                Log::info('นำเข้าข้อมูล stock_movements กลับเข้าระบบเรียบร้อยแล้ว จำนวน ' . count($stockMovementsData) . ' รายการ');
            } catch (\Exception $e) {
                Log::error('ไม่สามารถนำเข้าข้อมูล stock_movements: ' . $e->getMessage());
            }
        }

        Log::info('สร้างตาราง stock_movements เรียบร้อยแล้วพร้อมคอลัมน์ทั้งหมด');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');

        // ถอนการปรับปรุงตาราง products
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $columns = [
                    'is_inventory',
                    'is_service',
                    'weight',
                    'length',
                    'width',
                    'height',
                    'is_featured',
                    'tags',
                    'attributes',
                    'created_by',
                    'updated_by'
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
                            Log::warning("ไม่สามารถลบคอลัมน์ {$column} ในตาราง products: " . $e->getMessage());
                        }
                    }
                }
            });
        }
    }
};
