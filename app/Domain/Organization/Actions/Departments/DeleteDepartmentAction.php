<?php

namespace App\Domain\Organization\Actions\Departments;

use App\Domain\Organization\Models\Department;
use Exception;
use Illuminate\Support\Facades\DB;

class DeleteDepartmentAction
{
    /**
     * ลบแผนกที่ระบุ
     *
     * @param int $id
     * @return bool
     * @throws Exception ถ้าไม่สามารถลบแผนกได้
     */
    public function execute(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            // ดึงข้อมูลแผนก
            $department = Department::findOrFail($id);

            // ตรวจสอบว่าแผนกมีพนักงาน หรือตำแหน่งอยู่หรือไม่
            if ($department->positions()->count() > 0) {
                throw new Exception("ไม่สามารถลบแผนกที่มีตำแหน่งงานอยู่ได้");
            }

            if ($department->employees()->count() > 0) {
                throw new Exception("ไม่สามารถลบแผนกที่มีพนักงานอยู่ได้");
            }

            // ตรวจสอบว่าแผนกมีแผนกย่อยหรือไม่
            if ($department->children()->count() > 0) {
                throw new Exception("ไม่สามารถลบแผนกที่มีแผนกย่อยได้");
            }

            // ลบส่วน activity log ที่ทำให้เกิดข้อผิดพลาด
            // activity()
            //     ->performedOn($department)
            //     ->withProperties([
            //         'company_id' => $department->company_id,
            //         'name' => $department->name,
            //     ])
            //     ->log('ลบแผนก');

            // ลบแผนก (soft delete)
            return $department->delete();
        });
    }
}
