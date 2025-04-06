<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Inventory\Models\Tax;
use App\Domain\Organization\Models\Company;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();
        
        foreach ($companies as $company) {
            $this->createTaxesForCompany($company->id);
        }
    }
    
    /**
     * สร้างข้อมูลภาษีสำหรับบริษัท
     */
    private function createTaxesForCompany($companyId)
    {
        $taxes = [
            [
                'company_id' => $companyId,
                'name' => 'ภาษีมูลค่าเพิ่ม 7%',
                'code' => 'VAT7',
                'rate' => 7.00,
                'type' => 'percentage',
                'is_compound' => false,
                'apply_to' => 'all',
                'is_active' => true,
            ],
            [
                'company_id' => $companyId,
                'name' => 'ภาษีหัก ณ ที่จ่าย 3%',
                'code' => 'WHT3',
                'rate' => 3.00,
                'type' => 'percentage',
                'is_compound' => false,
                'apply_to' => 'services',
                'is_active' => true,
            ],
            [
                'company_id' => $companyId,
                'name' => 'ภาษีหัก ณ ที่จ่าย 5%',
                'code' => 'WHT5',
                'rate' => 5.00,
                'type' => 'percentage',
                'is_compound' => false,
                'apply_to' => 'rent',
                'is_active' => true,
            ],
        ];
        
        foreach ($taxes as $tax) {
            Tax::firstOrCreate(
                [
                    'company_id' => $companyId,
                    'code' => $tax['code']
                ],
                $tax
            );
        }
    }
}
