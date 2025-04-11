<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\HumanResources\Models\LeaveType;
use App\Domain\Organization\Models\Company;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();
        
        foreach ($companies as $company) {
            $this->createLeaveTypesForCompany($company->id);
        }
    }

    private function createLeaveTypesForCompany($companyId)
    {
        $leaveTypes = [
            [
                'company_id' => $companyId,
                'name' => 'ลาป่วย',
                'code' => 'SICK',
                'description' => 'ลาป่วย ลาพักรักษาตัว',
                'days_allowed' => 30,
                'days_advance_notice' => 0,
                'requires_approval' => true,
                'requires_attachment' => true,
                'is_paid' => true,
                'is_active' => true,
                'color' => '#FF4444',
                'icon' => 'medical-kit',
                'metadata' => json_encode([
                    'max_consecutive_days' => 3,
                    'needs_medical_cert' => true,
                    'legal_reference' => 'พ.ร.บ. คุ้มครองแรงงาน มาตรา 32'
                ])
            ],
            [
                'company_id' => $companyId,
                'name' => 'ลาพักร้อน',
                'code' => 'ANNUAL',
                'description' => 'ลาพักร้อนประจำปี',
                'days_allowed' => 6,
                'days_advance_notice' => 3,
                'requires_approval' => true,
                'requires_attachment' => false,
                'is_paid' => true,
                'is_active' => true,
                'color' => '#4CAF50',
                'icon' => 'beach',
                'metadata' => json_encode([
                    'can_carry_forward' => true,
                    'carry_forward_days' => 3,
                    'legal_reference' => 'พ.ร.บ. คุ้มครองแรงงาน มาตรา 30'
                ])
            ],
            [
                'company_id' => $companyId,
                'name' => 'ลากิจ',
                'code' => 'PERSONAL',
                'description' => 'ลากิจส่วนตัว',
                'days_allowed' => 3,
                'days_advance_notice' => 1,
                'requires_approval' => true,
                'requires_attachment' => false,
                'is_paid' => true,
                'is_active' => true,
                'color' => '#FF9800',
                'icon' => 'calendar',
                'metadata' => json_encode([
                    'max_consecutive_days' => 2,
                    'needs_reason' => true
                ])
            ],
            [
                'company_id' => $companyId,
                'name' => 'ลาคลอด',
                'code' => 'MATERNITY',
                'description' => 'ลาคลอดบุตร',
                'days_allowed' => 98,
                'days_advance_notice' => 30,
                'requires_approval' => true,
                'requires_attachment' => true,
                'is_paid' => true,
                'is_active' => true,
                'color' => '#E91E63',
                'icon' => 'baby',
                'metadata' => json_encode([
                    'paid_days' => 45,
                    'social_security_days' => 53,
                    'legal_reference' => 'พ.ร.บ. คุ้มครองแรงงาน มาตรา 41'
                ])
            ]
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::firstOrCreate(
                [
                    'company_id' => $companyId,
                    'code' => $leaveType['code']
                ],
                $leaveType
            );
        }
    }
}
