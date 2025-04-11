<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\HumanResources\Models\Employee;
use App\Domain\Organization\Models\Company;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $this->createEmployeesForCompany($company->id);
        }
    }

    private function createEmployeesForCompany($companyId)
    {
        $employees = [
            [
                'company_id' => $companyId,
                'email' => 'somchai@ceosofts.com',
                'department_id' => 1,
                'position_id' => 1,
                'branch_office_id' => 1,
                'employee_code' => 'EMP001',
                'first_name' => 'สมชาย',
                'last_name' => 'ใจดี',
                'phone' => '081-234-5678',
                'address' => '123/456 ถ.รัชดาภิเษก กรุงเทพฯ',
                'id_card_number' => '1234567890123',
                'hire_date' => '2020-01-01',
                'status' => 'active',
                'metadata' => json_encode([
                    'education' => 'ปริญญาโท บริหารธุรกิจ',
                    'emergency_contact' => 'นางสมศรี ใจดี (ภรรยา) 082-345-6789'
                ])
            ],
            [
                'company_id' => $companyId,
                'email' => 'somsri@ceosofts.com',
                'department_id' => 2,
                'position_id' => 3,
                'branch_office_id' => 1,
                'employee_code' => 'EMP002',
                'first_name' => 'สมศรี',
                'last_name' => 'มีสุข',
                'phone' => '089-876-5432',
                'address' => '789/123 ถ.สีลม กรุงเทพฯ',
                'id_card_number' => '9876543210123',
                'hire_date' => '2021-03-15',
                'status' => 'active',
                'metadata' => json_encode([
                    'education' => 'ปริญญาตรี บัญชี',
                    'emergency_contact' => 'นายสมชาย มีสุข (สามี) 081-987-6543'
                ])
            ],
            [
                'company_id' => $companyId,
                'email' => 'somying@ceosofts.com',
                'department_id' => 3,
                'position_id' => 5,
                'branch_office_id' => 2,
                'employee_code' => 'EMP003',
                'first_name' => 'สมหญิง',
                'last_name' => 'รักงาน',
                'phone' => '083-456-7890',
                'address' => '456/789 ถ.พระราม9 กรุงเทพฯ',
                'id_card_number' => '4567890123123',
                'hire_date' => '2023-01-01',
                'status' => 'active',
                'metadata' => json_encode([
                    'education' => 'ปริญญาตรี การตลาด',
                    'emergency_contact' => 'นางสมจิตร รักงาน (มารดา) 084-567-8901'
                ])
            ]
        ];

        foreach ($employees as $employee) {
            $employee['uuid'] = Str::uuid(); // สร้าง UUID ใหม่ทุกครั้ง
            
            // เพิ่มค่า birth_date ถ้ามีคอลัมน์นี้
            if (Schema::hasColumn('employees', 'birth_date')) {
                $employee['birth_date'] = '1980-01-01'; // ดีฟอลต์
            }
            
            Employee::firstOrCreate(
                [
                    'company_id' => $companyId,
                    'email' => $employee['email']
                ],
                $employee
            );
        }
    }
}
