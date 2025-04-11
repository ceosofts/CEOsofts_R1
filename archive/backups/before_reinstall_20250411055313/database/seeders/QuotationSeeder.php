<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Sales\Models\Quotation;
use App\Domain\Sales\Models\Customer;
use App\Domain\Organization\Models\Company;
use Carbon\Carbon;

class QuotationSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        
        foreach ($companies as $company) {
            $this->createQuotationsForCompany($company);
        }
    }

    private function createQuotationsForCompany($company)
    {
        $customers = Customer::where('company_id', $company->id)->get();
        
        if ($customers->isEmpty()) {
            return;
        }

        foreach ($customers as $index => $customer) {
            // สร้าง microtime เพื่อให้มั่นใจว่าเลขที่ไม่ซ้ำกัน
            $uniqueTimestamp = microtime(true) * 10000;
            $randomSuffix1 = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
            $randomSuffix2 = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
            
            $quotations = [
                [
                    'company_id' => $company->id,
                    'customer_id' => $customer->id,
                    'quotation_number' => 'QT' . date('Ym') . $randomSuffix1 . $index,
                    'quotation_date' => now(),
                    'valid_until' => now()->addDays(30),
                    'status' => 'draft',
                    'currency' => 'THB',
                    'discount_type' => 'fixed',
                    'discount_amount' => 1000,
                    'tax_inclusive' => false,
                    'tax_rate' => 7,
                    'subtotal' => 50000,
                    'total_discount' => 1000,
                    'tax_amount' => 3430,
                    'total' => 52430,
                    'notes' => 'ราคานี้มีผล 30 วัน',
                    'terms' => 'ชำระเงินภายใน 30 วัน',
                    'created_by' => 1,
                    'metadata' => json_encode([
                        'payment_terms' => '30 days',
                        'shipping_method' => 'standard',
                        'currency' => 'THB'
                    ])
                ],
                [
                    'company_id' => $company->id,
                    'customer_id' => $customer->id,
                    'quotation_number' => 'QT' . date('Ym') . $randomSuffix2 . $index,
                    'quotation_date' => now()->subDays(15),
                    'valid_until' => now()->addDays(15),
                    'status' => 'approved',
                    'currency' => 'THB',
                    'discount_type' => 'percentage',
                    'discount_amount' => 10,
                    'tax_inclusive' => true,
                    'tax_rate' => 7,
                    'subtotal' => 75000,
                    'total_discount' => 7500,
                    'tax_amount' => 4410,
                    'total' => 71910,
                    'notes' => 'ราคาพิเศษสำหรับลูกค้าประจำ',
                    'terms' => 'ชำระเงินภายใน 45 วัน',
                    'created_by' => 1,
                    'approved_by' => 1,
                    'approved_at' => now()->subDays(10),
                    'metadata' => json_encode([
                        'payment_terms' => '45 days',
                        'shipping_method' => 'express',
                        'currency' => 'THB'
                    ])
                ]
            ];

            foreach ($quotations as $quotation) {
                try {
                    Quotation::create($quotation);
                } catch (\Exception $e) {
                    // หากเกิดข้อผิดพลาด UniqueConstraintViolation ให้สร้างเลขที่ใหม่และลองอีกครั้ง
                    if ($e instanceof \Illuminate\Database\UniqueConstraintViolationException) {
                        // สร้างเลขที่ใหม่ที่มั่นใจว่าไม่ซ้ำ
                        $uniqueID = uniqid('', true);
                        $quotation['quotation_number'] = 'QT' . date('Ym') . substr(md5($uniqueID . $index), 0, 5);
                        
                        // ลองบันทึกอีกครั้ง
                        try {
                            Quotation::create($quotation);
                        } catch (\Exception $innerEx) {
                            // บันทึกข้อผิดพลาดถ้ายังไม่สำเร็จ
                            \Log::error("Failed to create quotation after retry: " . $innerEx->getMessage());
                        }
                    } else {
                        // บันทึกข้อผิดพลาดประเภทอื่น
                        \Log::error("Error creating quotation: " . $e->getMessage());
                    }
                }
            }
        }
    }
}
