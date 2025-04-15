<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Company;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $this->createCustomersForCompany($company->id);
        }
    }

    private function createCustomersForCompany($companyId)
    {
        $customers = [
            [
                'company_id' => $companyId,
                'name' => 'บริษัท ABC จำกัด',
                'code' => 'CUST-' . $companyId . '-001',
                'email' => 'contact@abc.co.th',
                'phone' => '02-123-4567',
                'address' => '123/45 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110',
                'tax_id' => '0105555123456',
                'status' => 'active',
                'type' => 'company',
                'contact_person' => 'คุณประเสริฐ มั่งมี',
                'contact_person_position' => 'ผู้จัดการฝ่ายจัดซื้อ',
                'contact_person_email' => 'prasert@abc.co.th',
                'contact_person_phone' => '081-234-5678',
                'contact_person_line_id' => 'prasert_abc',
                'payment_term_type' => 'credit',
                'discount_rate' => 5.00,
                'reference_id' => 'CUS-ABC-2023',
                'social_media' => json_encode([
                    'facebook' => 'abcthailand',
                    'line_official' => '@abc',
                ]),
                'customer_group' => 'A',
                'customer_rating' => 5,
                'metadata' => json_encode([
                    'industry' => 'Manufacturing',
                    'credit_term' => 30,
                    'sales_region' => 'Bangkok'
                ])
            ],
            [
                'company_id' => $companyId,
                'name' => 'ร้าน XYZ การค้า',
                'code' => 'CUST-' . $companyId . '-002',
                'email' => 'xyz@trading.com',
                'phone' => '02-987-6543',
                'address' => '789 ถนนรัชดาภิเษก แขวงดินแดง เขตดินแดง กรุงเทพฯ 10400',
                'tax_id' => '0105555789012',
                'status' => 'active',
                'type' => 'company',
                'contact_person' => 'คุณสมศรี วงศ์พาณิชย์',
                'contact_person_position' => 'เจ้าของกิจการ',
                'contact_person_email' => 'somsri@xyztrading.com',
                'contact_person_phone' => '089-876-5432',
                'payment_term_type' => 'cash',
                'discount_rate' => 2.00,
                'reference_id' => 'CUS-XYZ-2023',
                'customer_group' => 'B',
                'customer_rating' => 3,
                'bank_account_name' => 'ร้าน XYZ การค้า',
                'bank_account_number' => '123-4-56789-0',
                'bank_name' => 'กสิกรไทย',
                'bank_branch' => 'รัชดาภิเษก',
                'metadata' => json_encode([
                    'industry' => 'Retail',
                    'credit_term' => 15,
                    'sales_region' => 'Bangkok'
                ])
            ],
            [
                'company_id' => $companyId,
                'name' => 'ห้างหุ้นส่วนจำกัด ชัยพัฒนา',
                'code' => 'CUST-' . $companyId . '-003',
                'email' => 'info@chaipat.co.th',
                'phone' => '02-345-6789',
                'address' => '456 ถนนพระราม 9 แขวงห้วยขวาง เขตห้วยขวาง กรุงเทพฯ 10310',
                'tax_id' => '0113555456789',
                'status' => 'active',
                'type' => 'company',
                'contact_person' => 'คุณวิชัย ธนกิจ',
                'contact_person_position' => 'กรรมการผู้จัดการ',
                'contact_person_email' => 'vichai@chaipat.co.th',
                'contact_person_phone' => '086-123-4567',
                'contact_person_line_id' => 'vichai_chaipat',
                'payment_term_type' => 'credit',
                'reference_id' => 'CUS-CHP-2023',
                'social_media' => json_encode([
                    'facebook' => 'chaipatpartnership',
                    'website' => 'www.chaipat.co.th'
                ]),
                'customer_group' => 'A',
                'customer_rating' => 4,
                'is_supplier' => true,
                'last_contacted_date' => now()->subDays(15),
                'metadata' => json_encode([
                    'industry' => 'Wholesale',
                    'credit_term' => 45,
                    'sales_region' => 'Central'
                ])
            ],
            [
                'company_id' => $companyId,
                'name' => 'คุณสมชาย ใจดี',
                'code' => 'CUST-' . $companyId . '-004',
                'email' => 'somchai@example.com',
                'phone' => '089-123-4567',
                'address' => '123 หมู่ 4 ต.บางรัก อ.เมือง จ.สงขลา 90000',
                'status' => 'active',
                'type' => 'individual',
                'payment_term_type' => 'cash',
                'customer_group' => 'C',
                'customer_rating' => 3,
                'last_contacted_date' => now()->subDays(30),
                'metadata' => json_encode([
                    'sales_region' => 'Southern'
                ])
            ]
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(
                [
                    'company_id' => $companyId,
                    'email' => $customer['email']
                ],
                $customer
            );
        }
    }
}
