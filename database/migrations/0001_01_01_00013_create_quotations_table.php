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
     * - 2024_08_01_000027_recreate_quotations_table.php
     */
    public function up(): void
    {
        // ตรวจสอบว่ามีตารางอยู่แล้วหรือไม่
        if (Schema::hasTable('quotations')) {
            try {
                // สำรองข้อมูล
                $existingData = DB::table('quotations')->get();
                Log::info('สำรองข้อมูล quotations จำนวน ' . count($existingData) . ' รายการ');

                // ลบตารางเดิม
                Schema::dropIfExists('quotations');
            } catch (\Exception $e) {
                Log::warning('ไม่สามารถสำรองข้อมูล quotations: ' . $e->getMessage());
                Schema::dropIfExists('quotations');
            }
        }

        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('quotation_number');
            $table->date('issue_date');
            $table->date('expiry_date')->nullable();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status', 20)->default('draft');

            // คอลัมน์เพิ่มเติมจาก recreate_quotations_table
            $table->text('notes')->nullable(); // บันทึกเพิ่มเติม
            $table->decimal('discount_amount', 15, 2)->default(0); // ส่วนลดรวม
            $table->string('discount_type', 10)->default('fixed'); // ประเภทส่วนลด (fixed, percentage)
            $table->decimal('tax_rate', 5, 2)->default(0); // อัตราภาษี
            $table->decimal('tax_amount', 15, 2)->default(0); // จำนวนภาษี
            $table->decimal('subtotal', 15, 2)->default(0); // ยอดรวมก่อนภาษี
            $table->string('reference_number')->nullable(); // เลขที่อ้างอิง
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // ผู้สร้าง
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete(); // ผู้อนุมัติ
            $table->timestamp('approved_at')->nullable(); // วันที่อนุมัติ
            $table->foreignId('sales_person_id')->nullable(); // พนักงานขาย
            $table->foreignId('payment_term_id')->nullable(); // เงื่อนไขการชำระเงิน
            $table->string('shipping_method', 50)->nullable(); // วิธีการจัดส่ง
            $table->decimal('shipping_cost', 15, 2)->default(0); // ค่าจัดส่ง
            $table->string('currency', 3)->default('THB'); // สกุลเงิน
            $table->decimal('currency_rate', 10, 6)->default(1); // อัตราแลกเปลี่ยน

            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('company_id');
            $table->index('customer_id');
            $table->index('status');
            $table->index('quotation_number');
            $table->index('created_by');
            $table->index('approved_by');
            $table->index('sales_person_id');

            // ในการสร้างใหม่ ควรกำหนด unique constraint ที่ถูกต้อง
            $table->unique(['company_id', 'quotation_number']);
        });

        Log::info('สร้างตาราง quotations เรียบร้อยแล้ว');

        // นำข้อมูลเดิมกลับคืน (ถ้ามี)
        if (isset($existingData) && count($existingData) > 0) {
            try {
                foreach ($existingData as $quotation) {
                    $quotationArr = (array) $quotation;

                    // ลบ primary key เพื่อให้ auto-increment ทำงานได้ถูกต้อง
                    if (isset($quotationArr['id'])) {
                        unset($quotationArr['id']);
                    }

                    // ตรวจสอบและเพิ่มค่าเริ่มต้นสำหรับคอลัมน์ใหม่ที่จำเป็น
                    $defaults = [
                        'discount_amount' => 0,
                        'discount_type' => 'fixed',
                        'tax_rate' => 0,
                        'tax_amount' => 0,
                        'subtotal' => $quotationArr['total_amount'] ?? 0,
                        'currency' => 'THB',
                        'currency_rate' => 1
                    ];

                    foreach ($defaults as $key => $value) {
                        if (!isset($quotationArr[$key])) {
                            $quotationArr[$key] = $value;
                        }
                    }

                    DB::table('quotations')->insert($quotationArr);
                }

                Log::info('นำข้อมูล quotations กลับคืนเรียบร้อยแล้ว');
            } catch (\Exception $e) {
                Log::error('ไม่สามารถนำข้อมูล quotations กลับคืน: ' . $e->getMessage());
            }
        }

        // สร้างตาราง quotation_items
        if (!Schema::hasTable('quotation_items')) {
            Schema::create('quotation_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quotation_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
                $table->string('description');
                $table->decimal('quantity', 15, 2)->default(0);
                $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
                $table->decimal('unit_price', 15, 2)->default(0);
                $table->decimal('discount_percentage', 5, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('tax_percentage', 5, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('total', 15, 2)->default(0);
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes(); // เพิ่ม column deleted_at สำหรับ soft delete
            });
            Log::info('สร้างตาราง quotation_items เรียบร้อยแล้ว');
        } else {
            // ตรวจสอบว่ามี column deleted_at หรือไม่ และเพิ่มถ้าไม่มี
            if (!Schema::hasColumn('quotation_items', 'deleted_at')) {
                Schema::table('quotation_items', function (Blueprint $table) {
                    $table->softDeletes();
                });
                Log::info('เพิ่ม column deleted_at ในตาราง quotation_items เรียบร้อยแล้ว');
            }
            
            // ตรวจสอบว่ามี column unit_id หรือไม่ และเพิ่มถ้าไม่มี
            if (!Schema::hasColumn('quotation_items', 'unit_id')) {
                Schema::table('quotation_items', function (Blueprint $table) {
                    $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
                });
                Log::info('เพิ่ม column unit_id ในตาราง quotation_items เรียบร้อยแล้ว');
            }
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
    }
};
