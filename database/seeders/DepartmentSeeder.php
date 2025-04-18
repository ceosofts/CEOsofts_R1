<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Organization\Models\Department;
use App\Domain\Organization\Models\Company;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();
        
        foreach ($companies as $company) {
            $this->createDepartmentsForCompany($company->id);
        }
    }

    private function createDepartmentsForCompany($companyId)
    {
        $departments = [
            [
                'company_id' => $companyId,
                'name' => 'บริหารจัดการ',
                'code' => 'MGMT',
                'description' => 'ฝ่ายบริหารจัดการองค์กร',
                'is_active' => true,
                'parent_id' => null,
                'metadata' => json_encode([
                    'level' => 1,
                    'cost_center' => 'CC001',
                    'manager_position' => 'CEO'
                ]),
            ],
            [
                'company_id' => $companyId,
                'name' => 'บัญชีและการเงิน',
                'code' => 'FIN',
                'description' => 'ฝ่ายบัญชีและการเงิน',
                'is_active' => true,
                'parent_id' => null,
                'metadata' => json_encode([
                    'level' => 2,
                    'cost_center' => 'CC002',
                    'manager_position' => 'CFO'
                ]),
            ],
            [
                'company_id' => $companyId,
                'name' => 'เทคโนโลยีสารสนเทศ',
                'code' => 'IT',
                'description' => 'ฝ่ายเทคโนโลยีสารสนเทศ',
                'is_active' => true,
                'parent_id' => null,
                'metadata' => json_encode([
                    'level' => 2,
                    'cost_center' => 'CC003',
                    'manager_position' => 'CTO'
                ]),
            ],
            [
                'company_id' => $companyId,
                'name' => 'ทรัพยากรบุคคล',
                'code' => 'HR',
                'description' => 'ฝ่ายทรัพยากรบุคคล',
                'is_active' => true,
                'parent_id' => null,
                'metadata' => json_encode([
                    'level' => 2,
                    'cost_center' => 'CC004',
                    'manager_position' => 'HR Manager'
                ]),
            ],
            [
                'company_id' => $companyId,
                'name' => 'การตลาดและขาย',
                'code' => 'SALES',
                'description' => 'ฝ่ายการตลาดและการขาย',
                'is_active' => true,
                'parent_id' => null,
                'metadata' => json_encode([
                    'level' => 2,
                    'cost_center' => 'CC005',
                    'manager_position' => 'Sales Director'
                ]),
            ],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(
                [
                    'company_id' => $companyId,
                    'code' => $department['code']
                ],
                $department
            );
        }
    }
}
