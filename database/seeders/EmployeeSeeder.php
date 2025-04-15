<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Company;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

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
                
                // ข้อมูลส่วนตัว
                'title' => 'นาย',
                'nickname' => 'ชาย',
                'gender' => 'ชาย',
                'birthdate' => '1985-05-15',
                'nationality' => 'ไทย',
                'religion' => 'พุทธ',
                'blood_type' => 'O',
                'height' => 175,
                'weight' => 68,
                'marital_status' => 'สมรส',
                'medical_conditions' => 'ไม่มี',
                
                // ข้อมูลการศึกษาและประสบการณ์
                'education_level' => 'ปริญญาโท',
                'education_institute' => 'มหาวิทยาลัยจุฬาลงกรณ์',
                'education_major' => 'บริหารธุรกิจ',
                'years_experience' => 10,
                'skills' => 'การบริหารจัดการ, ภาษาอังกฤษ, การวิเคราะห์ข้อมูล',
                'certificates' => 'MBA, PMP',
                'previous_employment' => 'บริษัท AAA จำกัด (2015-2019), บริษัท BBB จำกัด (2010-2015)',
                
                // ข้อมูลติดต่อฉุกเฉิน
                'emergency_contact_name' => 'นางสมศรี ใจดี',
                'emergency_contact_phone' => '082-345-6789',
                
                // ข้อมูลธนาคารและภาษี
                'bank_name' => 'ธนาคารกรุงเทพ',
                'bank_account' => '123-456-7890',
                'tax_id' => '1234567890123',
                'tax_filing_status' => 'มีคู่สมรส',
                'social_security_number' => '1234567890',
                
                // ข้อมูลการทำงาน
                'employee_type' => 'พนักงานประจำ',
                'probation_end_date' => '2020-04-01',
                'manager_id' => null,
                
                // ข้อมูลเอกสาร
                'passport_number' => 'AA12345678',
                'passport_expiry' => '2028-05-15',
                'work_permit_number' => null,
                'work_permit_expiry' => null,
                'visa_type' => null,
                'visa_expiry' => null,
                
                // ข้อมูล metadata
                'metadata' => json_encode([
                    'education' => 'ปริญญาโท บริหารธุรกิจ มหาวิทยาลัยจุฬาลงกรณ์',
                    'emergency_contact' => 'นางสมศรี ใจดี (ภรรยา) 082-345-6789',
                    'additional_skills' => 'ความเป็นผู้นำ, การวางแผนกลยุทธ์, การแก้ไขปัญหา',
                    'languages' => 'ไทย (เจ้าของภาษา), อังกฤษ (ดีมาก), จีน (พื้นฐาน)',
                    'hobbies' => 'อ่านหนังสือ, เล่นกอล์ฟ, ท่องเที่ยว'
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
                
                // ข้อมูลส่วนตัว
                'title' => 'นาง',
                'nickname' => 'ศรี',
                'gender' => 'หญิง',
                'birthdate' => '1990-08-20',
                'nationality' => 'ไทย',
                'religion' => 'คริสต์',
                'blood_type' => 'A',
                'height' => 162,
                'weight' => 54,
                'marital_status' => 'สมรส',
                'medical_conditions' => 'ภูมิแพ้',
                
                // ข้อมูลการศึกษาและประสบการณ์
                'education_level' => 'ปริญญาตรี',
                'education_institute' => 'มหาวิทยาลัยธรรมศาสตร์',
                'education_major' => 'การบัญชี',
                'years_experience' => 5,
                'skills' => 'การบัญชี, Excel, การวางแผนภาษี',
                'certificates' => 'CPA, CIA',
                'previous_employment' => 'บริษัท CCC จำกัด (2018-2021), บริษัท DDD จำกัด (2016-2018)',
                
                // ข้อมูลติดต่อฉุกเฉิน
                'emergency_contact_name' => 'นายสมชาย มีสุข',
                'emergency_contact_phone' => '081-987-6543',
                
                // ข้อมูลธนาคารและภาษี
                'bank_name' => 'ธนาคารกสิกรไทย',
                'bank_account' => '987-654-3210',
                'tax_id' => '9876543210123',
                'tax_filing_status' => 'มีคู่สมรส',
                'social_security_number' => '9876543210',
                
                // ข้อมูลการทำงาน
                'employee_type' => 'พนักงานประจำ',
                'probation_end_date' => '2021-06-15',
                'manager_id' => 1,
                
                // ข้อมูลเอกสาร
                'passport_number' => 'BB98765432',
                'passport_expiry' => '2030-08-20',
                'work_permit_number' => null,
                'work_permit_expiry' => null,
                'visa_type' => null,
                'visa_expiry' => null,
                
                // ข้อมูล metadata
                'metadata' => json_encode([
                    'education' => 'ปริญญาตรี การบัญชี มหาวิทยาลัยธรรมศาสตร์',
                    'emergency_contact' => 'นายสมชาย มีสุข (สามี) 081-987-6543',
                    'additional_skills' => 'การจัดการระบบบัญชี, การวางแผนการเงิน, การวิเคราะห์งบการเงิน',
                    'languages' => 'ไทย (เจ้าของภาษา), อังกฤษ (ดี)',
                    'hobbies' => 'ปลูกต้นไม้, ทำอาหาร, อ่านหนังสือ'
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
                
                // ข้อมูลส่วนตัว
                'title' => 'นางสาว',
                'nickname' => 'หญิง',
                'gender' => 'หญิง',
                'birthdate' => '1995-12-10',
                'nationality' => 'ไทย',
                'religion' => 'พุทธ',
                'blood_type' => 'B',
                'height' => 165,
                'weight' => 50,
                'marital_status' => 'โสด',
                'medical_conditions' => 'ไม่มี',
                
                // ข้อมูลการศึกษาและประสบการณ์
                'education_level' => 'ปริญญาตรี',
                'education_institute' => 'มหาวิทยาลัยกรุงเทพ',
                'education_major' => 'การตลาด',
                'years_experience' => 3,
                'skills' => 'Digital Marketing, Social Media, Content Creation',
                'certificates' => 'Google Digital Marketing Certificate',
                'previous_employment' => 'บริษัท EEE จำกัด (2020-2022)',
                
                // ข้อมูลติดต่อฉุกเฉิน
                'emergency_contact_name' => 'นางสมจิตร รักงาน',
                'emergency_contact_phone' => '084-567-8901',
                
                // ข้อมูลธนาคารและภาษี
                'bank_name' => 'ธนาคารไทยพาณิชย์',
                'bank_account' => '456-789-0123',
                'tax_id' => '4567890123123',
                'tax_filing_status' => 'โสด',
                'social_security_number' => '4567890123',
                
                // ข้อมูลการทำงาน
                'employee_type' => 'พนักงานประจำ',
                'probation_end_date' => '2023-04-01',
                'manager_id' => 2,
                
                // ข้อมูลเอกสาร
                'passport_number' => 'CC45678901',
                'passport_expiry' => '2032-12-10',
                'work_permit_number' => null,
                'work_permit_expiry' => null,
                'visa_type' => null,
                'visa_expiry' => null,
                
                // ข้อมูล metadata
                'metadata' => json_encode([
                    'education' => 'ปริญญาตรี การตลาด มหาวิทยาลัยกรุงเทพ',
                    'emergency_contact' => 'นางสมจิตร รักงาน (มารดา) 084-567-8901',
                    'additional_skills' => 'การวิเคราะห์ตลาด, การวางแผนการตลาด, การทำงานร่วมกับทีม',
                    'languages' => 'ไทย (เจ้าของภาษา), อังกฤษ (ดีมาก), ญี่ปุ่น (พื้นฐาน)',
                    'hobbies' => 'ถ่ายรูป, เล่นดนตรี, ท่องเที่ยว'
                ])
            ]
        ];

        foreach ($employees as $employeeData) {
            $employeeData['uuid'] = Str::uuid(); // สร้าง UUID ใหม่ทุกครั้ง
            
            // แปลง format วันที่ให้เป็น Y-m-d สำหรับ Carbon
            foreach (['birthdate', 'hire_date', 'termination_date', 'probation_end_date', 'passport_expiry', 'work_permit_expiry', 'visa_expiry'] as $dateField) {
                if (!empty($employeeData[$dateField])) {
                    $employeeData[$dateField] = Carbon::parse($employeeData[$dateField])->format('Y-m-d');
                }
            }
            
            // เพิ่มตรวจสอบฟิลด์ที่ต้องมีในตาราง
            $tableCols = Schema::getColumnListing('employees');
            $filteredData = array_intersect_key($employeeData, array_flip($tableCols));
            
            Employee::firstOrCreate(
                [
                    'company_id' => $companyId,
                    'email' => $employeeData['email']
                ],
                $filteredData
            );
        }
    }
}
