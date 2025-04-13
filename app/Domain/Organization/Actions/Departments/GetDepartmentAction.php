<?php

namespace App\Domain\Organization\Actions\Departments;

use App\Domain\Organization\Models\Department;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GetDepartmentAction
{
    /**
     * ดึงข้อมูลแผนก พร้อม relationships ที่เกี่ยวข้อง
     *
     * @param int $id
     * @return Department
     */
    public function execute(int $id): Department
    {
        try {
            // ดึงข้อมูลแผนกพร้อมความสัมพันธ์ที่เกี่ยวข้อง
            $department = Department::with([
                'company',
                'parent',
                'children',
                'positions',
            ])->findOrFail($id);

            return $department;
        } catch (ModelNotFoundException $e) {
            // เพิ่มข้อความที่มีความเฉพาะเจาะจงมากขึ้น
            throw new ModelNotFoundException("ไม่พบแผนกรหัส #{$id} หรืออาจถูกลบไปแล้ว", 404);
        }
    }
}
