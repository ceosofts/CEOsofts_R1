<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\LeaveType; // เปลี่ยนเป็น App\Models\LeaveType
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        foreach ($companies as $company) {
            $this->createLeaveTypesForCompany($company);
        }
    }

    protected function createLeaveTypesForCompany(Company $company): void
    {
        // ข้อมูลพื้นฐานสำหรับประเภทการลา
        $leaveTypes = [
            [
                'code' => 'SICK',
                'name' => 'ลาป่วย',
                'description' => 'ลาป่วย ลาพักรักษาตัว',
                'annual_allowance' => 30,
                'min_notice_days' => 0, // เปลี่ยนจาก days_advance_notice เป็น min_notice_days
                'requires_approval' => true,
                'requires_documents' => true, // เปลี่ยนจาก requires_attachment เป็น requires_documents
                'is_paid' => true,
                'is_active' => true,
                'color' => '#FF4444',
                'icon' => 'medical-kit',
                'metadata' => [
                    'max_consecutive_days' => 3,
                    'needs_medical_cert' => true,
                    'legal_reference' => 'พ.ร.บ. คุ้มครองแรงงาน มาตรา 32'
                ]
            ],
            [
                'code' => 'ANNUAL',
                'name' => 'ลาพักร้อน',
                'description' => 'ลาพักร้อนประจำปี',
                'annual_allowance' => 6,
                'min_notice_days' => 3, // เปลี่ยนจาก days_advance_notice เป็น min_notice_days
                'requires_approval' => true,
                'requires_documents' => false, // เปลี่ยนจาก requires_attachment เป็น requires_documents
                'is_paid' => true,
                'is_active' => true,
                'color' => '#4444FF',
                'icon' => 'beach',
                'metadata' => [
                    'can_accumulate' => true,
                    'max_accumulate_days' => 12,
                    'legal_reference' => 'พ.ร.บ. คุ้มครองแรงงาน มาตรา 30'
                ]
            ],
            // ... ข้อมูลอื่นๆ ...
        ];

        // เพิ่มข้อมูลประเภทการลา
        foreach ($leaveTypes as $leaveTypeData) {
            // แปลง metadata เป็น JSON
            if (isset($leaveTypeData['metadata'])) {
                $leaveTypeData['metadata'] = json_encode($leaveTypeData['metadata']);
            }

            // เพิ่มข้อมูล company_id
            $leaveTypeData['company_id'] = $company->id;

            // ใช้ firstOrCreate เพื่อหลีกเลี่ยงการซ้ำซ้อนของข้อมูล
            LeaveType::firstOrCreate(
                [
                    'company_id' => $company->id,
                    'code' => $leaveTypeData['code']
                ],
                $leaveTypeData
            );
        }
    }
}
