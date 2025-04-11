<?php

namespace App\Domains\Organization\Actions;

use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateDepartmentAction
{
    /**
     * สร้างแผนกใหม่ในระบบ
     *
     * @param array $data
     * @return Department
     */
    public function execute(array $data): Department
    {
        try {
            DB::beginTransaction();
            
            $department = new Department();
            $department->company_id = $data['company_id']; // จะถูกกำหนดโดย HasCompanyScope ถ้าไม่ระบุ
            $department->name = $data['name'];
            $department->code = $data['code'] ?? null;
            $department->description = $data['description'] ?? null;
            $department->is_active = $data['is_active'] ?? true;
            $department->parent_id = $data['parent_id'] ?? null;
            $department->save();
            
            // สร้างตำแหน่งงานเริ่มต้นหรือข้อมูลเพิ่มเติม (ถ้าจำเป็น)
            
            DB::commit();
            
            return $department;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('ไม่สามารถสร้างแผนกได้: ' . $e->getMessage(), [
                'exception' => $e,
                'data' => $data
            ]);
            
            throw $e;
        }
    }
}
