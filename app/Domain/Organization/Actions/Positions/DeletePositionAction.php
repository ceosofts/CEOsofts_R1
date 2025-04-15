<?php

namespace App\Domain\Organization\Actions\Positions;

use App\Domain\Organization\Models\Position;
use Exception;
use Illuminate\Support\Facades\DB;

class DeletePositionAction
{
    /**
     * ลบตำแหน่งที่ระบุ
     *
     * @param int $id
     * @return bool
     * @throws Exception ถ้าไม่สามารถลบตำแหน่งได้
     */
    public function execute(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            // ดึงข้อมูลตำแหน่ง
            $position = Position::findOrFail($id);

            // ตรวจสอบว่ามีพนักงานในตำแหน่งนี้หรือไม่
            if ($position->employees()->count() > 0) {
                throw new Exception("ไม่สามารถลบตำแหน่งที่มีพนักงานอยู่ได้");
            }

            // ปิดการใช้งาน activity log ที่ทำให้เกิดข้อผิดพลาด
            // activity()
            //     ->performedOn($position)
            //     ->withProperties([
            //         'company_id' => $position->company_id,
            //         'name' => $position->name,
            //     ])
            //     ->log('ลบตำแหน่ง');

            // ลบตำแหน่ง (soft delete)
            return $position->delete();
        });
    }
}
