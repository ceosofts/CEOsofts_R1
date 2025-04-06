<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Organization\Models\Position;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Department;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();
        
        foreach ($companies as $company) {
            $this->createPositionsForCompany($company);
        }
    }

    private function createPositionsForCompany($company)
    {
        // ดึงแผนกของบริษัท
        $departments = Department::where('company_id', $company->id)->get();
        $deptMap = $departments->pluck('id', 'code')->toArray();

        $positions = [
            // ผู้บริหาร
            [
                'company_id' => $company->id,
                'department_id' => $deptMap['MGMT'] ?? null,
                'name' => 'ประธานเจ้าหน้าที่บริหาร',
                'code' => 'CEO',
                'level' => 1,
                'is_active' => true,
                'min_salary' => 150000,
                'max_salary' => 300000,
                'metadata' => json_encode([
                    'en_name' => 'Chief Executive Officer',
                    'reports_to' => 'Board of Directors',
                    'grade' => 'E1'
                ])
            ],
            [
                'company_id' => $company->id,
                'department_id' => $deptMap['FIN'] ?? null,
                'name' => 'ผู้อำนวยการฝ่ายการเงิน',
                'code' => 'CFO',
                'level' => 2,
                'is_active' => true,
                'min_salary' => 100000,
                'max_salary' => 200000,
                'metadata' => json_encode([
                    'en_name' => 'Chief Financial Officer',
                    'reports_to' => 'CEO',
                    'grade' => 'E2'
                ])
            ],
            
            // ฝ่ายบัญชีและการเงิน
            [
                'company_id' => $company->id,
                'department_id' => $deptMap['FIN'] ?? null,
                'name' => 'ผู้จัดการฝ่ายบัญชี',
                'code' => 'ACC-MGR',
                'level' => 3,
                'is_active' => true,
                'min_salary' => 50000,
                'max_salary' => 80000,
                'metadata' => json_encode([
                    'en_name' => 'Accounting Manager',
                    'reports_to' => 'CFO',
                    'grade' => 'M1'
                ])
            ],
            
            // ฝ่ายไอที
            [
                'company_id' => $company->id,
                'department_id' => $deptMap['IT'] ?? null,
                'name' => 'ผู้จัดการฝ่ายไอที',
                'code' => 'IT-MGR',
                'level' => 3,
                'is_active' => true,
                'min_salary' => 60000,
                'max_salary' => 90000,
                'metadata' => json_encode([
                    'en_name' => 'IT Manager',
                    'reports_to' => 'CTO',
                    'grade' => 'M1'
                ])
            ],
            
            // ฝ่ายทรัพยากรบุคคล
            [
                'company_id' => $company->id,
                'department_id' => $deptMap['HR'] ?? null,
                'name' => 'ผู้จัดการฝ่ายทรัพยากรบุคคล',
                'code' => 'HR-MGR',
                'level' => 3,
                'is_active' => true,
                'min_salary' => 50000,
                'max_salary' => 80000,
                'metadata' => json_encode([
                    'en_name' => 'HR Manager',
                    'reports_to' => 'CEO',
                    'grade' => 'M1'
                ])
            ],
        ];

        foreach ($positions as $position) {
            Position::firstOrCreate(
                [
                    'company_id' => $position['company_id'],
                    'code' => $position['code']
                ],
                $position
            );
        }
    }
}
