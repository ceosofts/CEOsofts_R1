<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Quotation;
use App\Models\Customer;
use App\Models\Company;
use Carbon\Carbon;

class QuotationSeeder extends Seeder
{
    public function run(): void
    {
        // เพิ่มการตรวจสอบ Class ที่จำเป็นก่อน
        if (!class_exists('App\Models\Quotation')) {
            $this->command->error('ไม่พบคลาส App\Models\Quotation');
            return;
        }
        
        if (!class_exists('App\Models\Customer')) {
            $this->command->error('ไม่พบคลาส App\Models\Customer');
            return;
        }
        
        if (!class_exists('App\Models\Company')) {
            $this->command->error('ไม่พบคลาส App\Models\Company');
            return;
        }
        
        if (!Schema::hasTable('quotations')) {
            $this->command->error('ไม่พบตาราง quotations กรุณา migrate ก่อน');
            return;
        }
        
        $this->command->info('เริ่มสร้างข้อมูลใบเสนอราคา...');
        
        // ตรวจสอบว่ามีข้อมูลบริษัทหรือไม่
        $companyCount = Company::count();
        $this->command->info("จำนวนบริษัท: {$companyCount}");
        
        if ($companyCount === 0) {
            $this->command->error('ไม่พบข้อมูลบริษัท ไม่สามารถสร้างใบเสนอราคาได้');
            return;
        }
        
        // ตรวจสอบว่ามีข้อมูลลูกค้าหรือไม่
        $customerCount = Customer::count();
        $this->command->info("จำนวนลูกค้า: {$customerCount}");
        
        if ($customerCount === 0) {
            $this->command->error('ไม่พบข้อมูลลูกค้า ไม่สามารถสร้างใบเสนอราคาได้');
            return;
        }
        
        try {
            // ตรวจสอบข้อมูลแบบละเอียด
            $companies = Company::all();
            foreach ($companies as $company) {
                $this->createQuotationsForCompany($company);
            }
            
            $count = Quotation::count();
            $this->command->info("สร้างข้อมูลใบเสนอราคาเสร็จสิ้น: {$count} รายการ");
        } catch (\Exception $e) {
            $this->command->error("เกิดข้อผิดพลาด: " . $e->getMessage());
            Log::error("QuotationSeeder Error: " . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }

    private function createQuotationsForCompany($company)
    {
        $customers = Customer::where('company_id', $company->id)->get();

        if ($customers->isEmpty()) {
            $this->command->warn("ไม่พบข้อมูลลูกค้าสำหรับบริษัท ID:{$company->id} ({$company->name})");
            return;
        }

        $this->command->info("กำลังสร้างใบเสนอราคาสำหรับบริษัท: {$company->name}");

        foreach ($customers as $index => $customer) {
            // สร้าง microtime เพื่อให้มั่นใจว่าเลขที่ไม่ซ้ำกัน
            $timestamp = now()->format('YmdHis');
            $randomSuffix1 = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
            $randomSuffix2 = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);

            // สร้างใบเสนอราคา #1 (draft)
            try {
                // แยกเป็นขั้นตอนเพื่อหาจุดที่ error
                $quotationData = [
                    'company_id' => $company->id,
                    'customer_id' => $customer->id,
                    'quotation_number' => 'QT' . date('Ym') . $randomSuffix1 . $index,
                    'issue_date' => now(),
                    'expiry_date' => now()->addDays(30),
                    'status' => 'draft',
                    'currency' => 'THB',
                    'discount_type' => 'fixed',
                    'discount_amount' => 1000,
                    'tax_rate' => 7,
                    'tax_amount' => 3430,
                    'subtotal' => 50000,
                    'total_amount' => 52430,
                    'notes' => 'ราคานี้มีผล 30 วัน',
                    'reference_number' => 'REF-' . $timestamp . '-' . $randomSuffix1,
                    'created_by' => 1
                ];
                
                // ใช้ DB::table แทนเพื่อแก้ปัญหา namespace
                DB::table('quotations')->insert([
                    'company_id' => $quotationData['company_id'],
                    'customer_id' => $quotationData['customer_id'],
                    'quotation_number' => $quotationData['quotation_number'],
                    'issue_date' => $quotationData['issue_date'],
                    'expiry_date' => $quotationData['expiry_date'],
                    'status' => $quotationData['status'],
                    'currency' => $quotationData['currency'],
                    'discount_type' => $quotationData['discount_type'],
                    'discount_amount' => $quotationData['discount_amount'],
                    'tax_rate' => $quotationData['tax_rate'],
                    'tax_amount' => $quotationData['tax_amount'],
                    'subtotal' => $quotationData['subtotal'],
                    'total_amount' => $quotationData['total_amount'],
                    'notes' => $quotationData['notes'],
                    'reference_number' => $quotationData['reference_number'],
                    'created_by' => $quotationData['created_by'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $this->command->info("  สร้างใบเสนอราคา: {$quotationData['quotation_number']} สำเร็จ");
            } catch (\Exception $e) {
                $this->command->error("  ไม่สามารถสร้างใบเสนอราคาได้: " . $e->getMessage());
                Log::error("Error creating quotation: " . $e->getMessage());
            }

            // สร้างใบเสนอราคา #2 (approved)
            try {
                // แยกเป็นขั้นตอนเพื่อหาจุดที่ error
                $quotationData = [
                    'company_id' => $company->id,
                    'customer_id' => $customer->id,
                    'quotation_number' => 'QT' . date('Ym') . $randomSuffix2 . $index,
                    'issue_date' => now()->subDays(15),
                    'expiry_date' => now()->addDays(15),
                    'status' => 'approved',
                    'currency' => 'THB',
                    'discount_type' => 'percentage',
                    'discount_amount' => 7500,
                    'tax_rate' => 7,
                    'tax_amount' => 4410,
                    'subtotal' => 75000,
                    'total_amount' => 71910,
                    'notes' => 'ราคาพิเศษสำหรับลูกค้าประจำ',
                    'reference_number' => 'REF-' . $timestamp . '-' . $randomSuffix2,
                    'created_by' => 1,
                    'approved_by' => 1,
                    'approved_at' => now()->subDays(10)
                ];
                
                // ใช้ DB::table แทนเพื่อแก้ปัญหา namespace
                DB::table('quotations')->insert([
                    'company_id' => $quotationData['company_id'],
                    'customer_id' => $quotationData['customer_id'],
                    'quotation_number' => $quotationData['quotation_number'],
                    'issue_date' => $quotationData['issue_date'],
                    'expiry_date' => $quotationData['expiry_date'],
                    'status' => $quotationData['status'],
                    'currency' => $quotationData['currency'],
                    'discount_type' => $quotationData['discount_type'],
                    'discount_amount' => $quotationData['discount_amount'],
                    'tax_rate' => $quotationData['tax_rate'],
                    'tax_amount' => $quotationData['tax_amount'],
                    'subtotal' => $quotationData['subtotal'],
                    'total_amount' => $quotationData['total_amount'],
                    'notes' => $quotationData['notes'],
                    'reference_number' => $quotationData['reference_number'],
                    'created_by' => $quotationData['created_by'],
                    'approved_by' => $quotationData['approved_by'],
                    'approved_at' => $quotationData['approved_at'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $this->command->info("  สร้างใบเสนอราคา: {$quotationData['quotation_number']} สำเร็จ");
            } catch (\Exception $e) {
                $this->command->error("  ไม่สามารถสร้างใบเสนอราคาได้: " . $e->getMessage());
                Log::error("Error creating quotation: " . $e->getMessage());
            }
        }
    }
}
