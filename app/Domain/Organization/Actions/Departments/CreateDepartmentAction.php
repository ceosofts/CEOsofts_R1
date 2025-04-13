<?php

namespace App\Domain\Organization\Actions\Departments;

use App\Domain\Organization\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateDepartmentAction
{
    /**
     * สร้างแผนกใหม่
     */
    public function execute(array $data): Department
    {
        // เริ่มต้น database transaction
        return DB::transaction(function () use ($data) {
            // เตรียมข้อมูลสำหรับสร้างแผนก
            $departmentData = [
                'name' => $data['name'],
                'code' => $data['code'] ?? null,
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'company_id' => $data['company_id'] ?? Auth::user()->current_company_id,
                'parent_id' => $data['parent_id'] ?? null,
                'head_position_id' => $data['head_position_id'] ?? null,
                'settings' => $data['settings'] ?? null,
                'metadata' => $data['metadata'] ?? null,
            ];

            // สร้างแผนกใหม่
            $department = Department::create($departmentData);

            // บันทึกกิจกรรม (activity log)
            activity()
                ->performedOn($department)
                ->withProperties([
                    'company_id' => $department->company_id,
                    'name' => $department->name,
                ])
                ->log('สร้างแผนกใหม่');

            return $department;
        });
    }
}
