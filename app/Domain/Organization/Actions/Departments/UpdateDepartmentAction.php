<?php

namespace App\Domain\Organization\Actions\Departments;

use App\Domain\Organization\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateDepartmentAction
{
    /**
     * อัปเดตข้อมูลแผนก
     * 
     * @param int $id
     * @param array $data
     * @return Department
     */
    public function execute(int $id, array $data): Department
    {
        return DB::transaction(function () use ($id, $data) {
            // ดึงข้อมูลแผนกที่ต้องการอัปเดต
            $department = Department::findOrFail($id);

            // ตรวจสอบก่อนว่าคอลัมน์มีอยู่ในฐานข้อมูลหรือไม่
            $columns = Schema::getColumnListing($department->getTable());

            // กรองข้อมูลให้มีเฉพาะคอลัมน์ที่มีอยู่ในฐานข้อมูลเท่านั้น
            $filteredData = array_filter($data, function ($key) use ($columns) {
                return in_array($key, $columns);
            }, ARRAY_FILTER_USE_KEY);

            // อัปเดตแผนก
            $department->update($filteredData);

            // ลบการใช้งาน activity log ที่ทำให้เกิดข้อผิดพลาด
            // activity()
            //    ->performedOn($department)
            //    ->withProperties([
            //        'company_id' => $department->company_id,
            //        'name' => $department->name,
            //    ])
            //    ->log('อัปเดตข้อมูลแผนก');

            return $department->fresh();
        });
    }
}
