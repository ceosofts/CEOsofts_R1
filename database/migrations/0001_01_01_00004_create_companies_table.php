<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     * สร้างตาราง companies และรวมการปรับปรุงทั้งหมดใน migrations ต่อไปนี้:
     *
     * - 2024_08_05_000003_add_code_and_status_to_companies_table.php
     * - 2024_08_05_000004_add_metadata_to_companies_table.php
     * - 2025_04_12_010000_add_missing_columns_to_companies.php
     * - 2025_04_12_020000_fix_company_columns_and_add_missing.php
     * - 2025_04_12_030000_add_missing_columns_to_companies_fixed.php
     */
    public function up(): void
    {
        // เช็คว่ามี driver ประเภทใด เพื่อความเข้ากันได้ระหว่าง SQLite และ MySQL
        $driver = DB::connection()->getDriverName();

        // ถ้าตาราง companies มีอยู่แล้ว ให้สำรองข้อมูลก่อนสร้างใหม่
        $hasTable = Schema::hasTable('companies');
        $companiesData = [];

        if ($hasTable) {
            Log::info('พบตาราง companies อยู่แล้ว จะสำรองข้อมูลก่อนปรับปรุง');
            try {
                $companiesData = DB::table('companies')->get()->map(function ($item) {
                    return (array) $item;
                })->toArray();

                Schema::dropIfExists('companies');
                Log::info('สำรองข้อมูลบริษัทได้ ' . count($companiesData) . ' รายการ');
            } catch (\Exception $e) {
                Log::warning('ไม่สามารถสำรองข้อมูล companies: ' . $e->getMessage());
            }
        }

        // สร้างตาราง companies ใหม่
        Schema::create('companies', function (Blueprint $table) {
            // โครงสร้างหลัก
            $table->id();
            $table->string('uuid', 36)->nullable()->unique(); // จาก fix_company_columns - เปลี่ยนจาก string เป็น string(36)
            $table->string('ulid', 26)->nullable()->unique();
            $table->string('name');
            $table->string('code', 20)->nullable()->unique(); // จาก fix_company_columns - เพิ่มความยาว (20)
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable(); // จาก fix_company_columns - เพิ่มความยาว (20)
            $table->string('email')->nullable();
            $table->string('tax_id', 20)->nullable(); // จาก fix_company_columns - เพิ่มความยาว (20)
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('status', 20)->default('active'); // จาก fix_company_columns - เพิ่มความยาว (20)

            // คอลัมน์เพิ่มเติมจาก fix_company_columns_and_add_missing
            $table->string('registration_number', 30)->nullable(); // เลขทะเบียนนิติบุคคล
            $table->date('registration_date')->nullable(); // วันที่จดทะเบียน
            $table->decimal('registered_capital', 15, 2)->nullable(); // ทุนจดทะเบียน
            $table->string('business_type', 50)->nullable(); // ประเภทธุรกิจ
            $table->string('company_type', 30)->nullable(); // บริษัทจำกัด, บริษัทมหาชน, ห้างหุ้นส่วน
            $table->string('branch_code', 10)->nullable(); // รหัสสาขา
            $table->string('branch_name', 100)->nullable(); // ชื่อสาขา
            $table->string('branch_type', 20)->nullable(); // สำนักงานใหญ่, สาขา
            $table->string('contact_person')->nullable(); // บุคคลติดต่อ
            $table->string('contact_position')->nullable(); // ตำแหน่งของบุคคลติดต่อ
            $table->string('contact_email')->nullable(); // อีเมลบุคคลติดต่อ
            $table->string('contact_phone', 20)->nullable(); // เบอร์โทรบุคคลติดต่อ

            $table->json('settings')->nullable();
            $table->json('metadata')->nullable();

            // ฟิลด์ระบบ
            $table->timestamps();
            $table->softDeletes();

            // เพิ่ม indexes จาก fix_company_columns_and_add_missing
            $table->index('name');
            $table->index('registration_number');
            $table->index('business_type');
            $table->index('company_type');
            $table->index('branch_type');
            $table->index('status');
        });

        // นำข้อมูลเดิมกลับมาใส่ในตารางใหม่ (ถ้ามี)
        if (!empty($companiesData)) {
            try {
                foreach ($companiesData as $company) {
                    // ปรับข้อมูลให้รองรับคอลัมน์ใหม่
                    // ถ้าไม่มีคอลัมน์ code ให้ใช้ค่าเริ่มต้นจาก name
                    if (!isset($company['code']) || empty($company['code'])) {
                        $name = $company['name'] ?? '';
                        $company['code'] = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 5));
                    }

                    // ถ้าไม่มี status ให้ใช้ค่าเริ่มต้น
                    if (!isset($company['status']) || empty($company['status'])) {
                        $isActive = isset($company['is_active']) ? $company['is_active'] : true;
                        $company['status'] = $isActive ? 'active' : 'inactive';
                    }

                    // ถ้าไม่มี metadata ให้เพิ่มเป็นค่าเริ่มต้น
                    if (!isset($company['metadata']) || empty($company['metadata'])) {
                        $company['metadata'] = json_encode(['imported' => true, 'created_by' => 'system']);
                    }

                    // ถ้าไม่มี uuid ให้สร้างใหม่
                    if (!isset($company['uuid']) || empty($company['uuid'])) {
                        $company['uuid'] = (string) \Illuminate\Support\Str::uuid();
                    }

                    // ข้อมูลที่มีชื่อคอลัมน์ไม่ตรงกับตารางใหม่จะถูกละเว้น
                    DB::table('companies')->insert($company);
                }

                Log::info('นำเข้าข้อมูลบริษัทกลับเข้าตารางใหม่จำนวน ' . count($companiesData) . ' รายการ');
            } catch (\Exception $e) {
                Log::error('ไม่สามารถนำเข้าข้อมูลบริษัท: ' . $e->getMessage());
            }
        }

        Log::info('สร้างตาราง companies สำเร็จพร้อมทั้งเพิ่มคอลัมน์ที่จำเป็นทั้งหมดแล้ว');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
