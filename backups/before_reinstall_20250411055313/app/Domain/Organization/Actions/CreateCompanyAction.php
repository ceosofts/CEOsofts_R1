<?php

namespace App\Domain\Organization\Actions;

use App\Domain\Organization\Models\Company;
use Illuminate\Support\Facades\Storage;

class CreateCompanyAction
{
    /**
     * สร้างบริษัทใหม่
     *
     * @param array $data
     * @return Company
     */
    public function execute(array $data): Company
    {
        // จัดการกับการอัพโหลดโลโก้
        if (isset($data['logo']) && $data['logo']) {
            $path = $data['logo']->store('companies/logos', 'public');
            $data['logo'] = $path;
        }
        
        // สร้างบริษัทใหม่
        $company = Company::create([
            'name' => $data['name'],
            'tax_id' => $data['tax_id'] ?? null,
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'website' => $data['website'] ?? null,
            'logo' => $data['logo'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'settings' => $data['settings'] ?? [],
        ]);
        
        // สร้างแผนกเริ่มต้น (ถ้าต้องการ)
        if (isset($data['create_default_departments']) && $data['create_default_departments']) {
            $this->createDefaultDepartments($company);
        }
        
        return $company;
    }
    
    /**
     * สร้างแผนกเริ่มต้นสำหรับบริษัทใหม่
     *
     * @param Company $company
     * @return void
     */
    protected function createDefaultDepartments(Company $company): void
    {
        $defaultDepartments = [
            ['name' => 'ผู้บริหาร', 'description' => 'แผนกผู้บริหาร'],
            ['name' => 'บัญชีและการเงิน', 'description' => 'แผนกบัญชีและการเงิน'],
            ['name' => 'การตลาด', 'description' => 'แผนกการตลาด'],
            ['name' => 'ขาย', 'description' => 'แผนกขาย'],
            ['name' => 'ทรัพยากรมนุษย์', 'description' => 'แผนกทรัพยากรมนุษย์'],
            ['name' => 'ไอที', 'description' => 'แผนกเทคโนโลยีสารสนเทศ'],
        ];
        
        foreach ($defaultDepartments as $dept) {
            $company->departments()->create($dept);
        }
    }
}
