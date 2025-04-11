<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Organization\Models\BranchOffice;
use App\Domain\Organization\Models\Company;

class BranchOfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();
        
        foreach ($companies as $company) {
            $this->createBranchOfficesForCompany($company->id);
        }
    }

    private function createBranchOfficesForCompany($companyId)
    {
        $branches = [
            [
                'company_id' => $companyId,
                'name' => 'สำนักงานใหญ่',
                'code' => 'HQ',
                'address' => '123 ถนนรัชดาภิเษก แขวงดินแดง เขตดินแดง กรุงเทพมหานคร 10400',
                'phone' => '02-123-4567',
                'email' => 'hq@ceosofts.com',
                'is_headquarters' => true,
                'is_active' => true,
                'metadata' => json_encode([
                    'region' => 'กรุงเทพและปริมณฑล',
                    'tax_branch_id' => '00000',
                    'opening_date' => '2020-01-01'
                ]),
            ],
            [
                'company_id' => $companyId,
                'name' => 'สาขาเชียงใหม่',
                'code' => 'CNX',
                'address' => '456 ถ.ห้วยแก้ว ต.สุเทพ อ.เมือง จ.เชียงใหม่ 50200',
                'phone' => '053-123-456',
                'email' => 'cnx@ceosofts.com',
                'is_headquarters' => false,
                'is_active' => true,
                'metadata' => json_encode([
                    'region' => 'ภาคเหนือ',
                    'tax_branch_id' => '00001',
                    'opening_date' => '2021-03-01'
                ]),
            ],
            [
                'company_id' => $companyId,
                'name' => 'สาขาขอนแก่น',
                'code' => 'KKC',
                'address' => '789 ถ.มิตรภาพ ต.ในเมือง อ.เมือง จ.ขอนแก่น 40000',
                'phone' => '043-234-567',
                'email' => 'kkc@ceosofts.com',
                'is_headquarters' => false,
                'is_active' => true,
                'metadata' => json_encode([
                    'region' => 'ภาคตะวันออกเฉียงเหนือ',
                    'tax_branch_id' => '00002',
                    'opening_date' => '2022-06-01'
                ]),
            ],
        ];

        foreach ($branches as $branch) {
            BranchOffice::firstOrCreate(
                [
                    'company_id' => $companyId,
                    'code' => $branch['code']
                ],
                $branch
            );
        }
    }
}
