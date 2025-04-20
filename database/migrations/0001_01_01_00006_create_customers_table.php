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
     * - 2024_08_01_000037_modify_customers_email_unique_constraint.php
     */
    public function up(): void
    {
        // สำรองข้อมูล customers ถ้ามีตารางอยู่แล้ว
        $existingCustomers = [];
        if (Schema::hasTable('customers')) {
            try {
                $existingCustomers = DB::table('customers')->get()->toArray();
                Log::info('สำรองข้อมูล customers จำนวน ' . count($existingCustomers) . ' รายการ');
                Schema::dropIfExists('customers');
            } catch (\Exception $e) {
                Log::warning('ไม่สามารถสำรองข้อมูล customers: ' . $e->getMessage());
                Schema::dropIfExists('customers');
            }
        }

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique()->nullable();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('tax_id', 50)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('website')->nullable();
            $table->string('contact_person')->nullable();
            $table->json('metadata')->nullable();
            $table->text('note')->nullable();
            $table->string('type')->default('company'); // company, individual
            $table->decimal('credit_limit', 15, 2)->nullable();
            
            // เพิ่มฟิลด์ใหม่
            $table->string('contact_person_position')->nullable()->comment('ตำแหน่งของผู้ติดต่อ');
            $table->string('contact_person_email')->nullable()->comment('อีเมลของผู้ติดต่อ');
            $table->string('contact_person_phone')->nullable()->comment('เบอร์โทรศัพท์ของผู้ติดต่อ');
            $table->string('contact_person_line_id')->nullable()->comment('LINE ID ของผู้ติดต่อ');
            $table->enum('payment_term_type', ['cash', 'credit', 'cheque', 'transfer'])->default('credit')->comment('ประเภทการชำระเงิน');
            $table->decimal('discount_rate', 5, 2)->nullable()->comment('อัตราส่วนลดพิเศษ (%)');
            $table->string('reference_id')->nullable()->comment('รหัสอ้างอิงภายนอก');
            $table->json('social_media')->nullable()->comment('ข้อมูลโซเชียลมีเดีย');
            $table->string('customer_group')->nullable()->comment('กลุ่มลูกค้า');
            $table->tinyInteger('customer_rating')->nullable()->comment('การจัดอันดับลูกค้า (1-5)');
            $table->string('bank_account_name')->nullable()->comment('ชื่อบัญชีธนาคาร');
            $table->string('bank_account_number')->nullable()->comment('เลขที่บัญชีธนาคาร');
            $table->string('bank_name')->nullable()->comment('ชื่อธนาคาร');
            $table->string('bank_branch')->nullable()->comment('สาขาธนาคาร');
            $table->boolean('is_supplier')->default(false)->comment('เป็นซัพพลายเออร์ด้วยหรือไม่');
            $table->date('last_contacted_date')->nullable()->comment('วันที่ติดต่อล่าสุด');
            
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('company_id');
            $table->index('name');
            $table->index('email'); // ยังคง index สำหรับการค้นหา
            $table->index('status');
            $table->index(['company_id', 'code']);

            // เพิ่ม unique constraint แบบ compound key สำหรับ email ในแต่ละ company
            // (จาก modify_customers_email_unique_constraint.php)
            $table->unique(['company_id', 'email'], 'customers_company_email_unique');
        });

        Log::info('สร้างตาราง customers เรียบร้อยแล้ว พร้อมปรับ unique constraint ของ email');

        // นำข้อมูลเดิมกลับคืน (ถ้ามี)
        if (!empty($existingCustomers)) {
            try {
                foreach ($existingCustomers as $customer) {
                    $customerData = (array) $customer;

                    // ลบ primary key เพื่อให้ auto-increment ทำงานได้ถูกต้อง
                    if (isset($customerData['id'])) {
                        unset($customerData['id']);
                    }

                    DB::table('customers')->insert($customerData);
                }

                Log::info('นำข้อมูล customers กลับคืนเรียบร้อยแล้ว');
            } catch (\Exception $e) {
                Log::error('ไม่สามารถนำข้อมูล customers กลับคืน: ' . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
