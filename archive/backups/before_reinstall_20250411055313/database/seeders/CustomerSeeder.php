<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Sales\Models\Customer;
use App\Domain\Organization\Models\Company;

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
                'email' => 'contact@abc.co.th',
                'phone' => '02-123-4567',
                'address' => '123/45 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110',
                'tax_id' => '0105555123456',
                'status' => 'active',
                'metadata' => json_encode([
                    'industry' => 'Manufacturing',
                    'credit_term' => 30,
                    'sales_region' => 'Bangkok'
                ])
            ],
            [
                'company_id' => $companyId,
                'name' => 'ร้าน XYZ การค้า',
                'email' => 'xyz@trading.com',
                'phone' => '02-987-6543',
                'address' => '789 ถนนรัชดาภิเษก แขวงดินแดง เขตดินแดง กรุงเทพฯ 10400',
                'tax_id' => '0105555789012',
                'status' => 'active',
                'metadata' => json_encode([
                    'industry' => 'Retail',
                    'credit_term' => 15,
                    'sales_region' => 'Bangkok'
                ])
            ],
            [
                'company_id' => $companyId,
                'name' => 'ห้างหุ้นส่วนจำกัด ชัยพัฒนา',
                'email' => 'info@chaipat.co.th',
                'phone' => '02-345-6789',
                'address' => '456 ถนนพระราม 9 แขวงห้วยขวาง เขตห้วยขวาง กรุงเทพฯ 10310',
                'tax_id' => '0113555456789',
                'status' => 'active',
                'metadata' => json_encode([
                    'industry' => 'Wholesale',
                    'credit_term' => 45,
                    'sales_region' => 'Central'
                ])
            ]
        ];

        foreach ($customers as $customer) {
            Customer::firstOrCreate(
                [
                    'company_id' => $companyId,
                    'email' => $customer['email']
                ],
                array_merge($customer, ['company_id' => $companyId])
            );
        }
    }
}
