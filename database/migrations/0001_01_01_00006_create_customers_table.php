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
            $table->string('name');
            // เปลี่ยนจากการใช้ unique() เป็น compound unique constraint
            // เพื่อให้ email ซ้ำได้ต่างบริษัทกัน (จาก modify_customers_email_unique_constraint.php)
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('tax_id', 50)->nullable();
            $table->string('status', 20)->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('company_id');
            $table->index('name');
            $table->index('email'); // ยังคง index สำหรับการค้นหา
            $table->index('status');

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
