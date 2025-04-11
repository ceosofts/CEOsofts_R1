<?php

namespace App\Domain\Organization\Actions;

use App\Domain\Organization\Models\Company;
use Illuminate\Support\Facades\Storage;

class UpdateCompanyAction
{
    /**
     * อัพเดทข้อมูลบริษัท
     *
     * @param Company $company
     * @param array $data
     * @return Company
     */
    public function execute(Company $company, array $data): Company
    {
        // จัดการกับการอัพโหลดโลโก้
        if (isset($data['logo']) && $data['logo']) {
            // ลบโลโก้เดิม (ถ้ามี)
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }
            
            $path = $data['logo']->store('companies/logos', 'public');
            $data['logo'] = $path;
        } else {
            // ถ้าไม่ได้อัพโหลดโลโก้ใหม่ ให้ใช้โลโก้เดิม
            unset($data['logo']);
        }
        
        // อัพเดทข้อมูล
        $company->update($data);
        
        return $company;
    }
}
