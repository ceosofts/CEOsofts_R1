<?php

namespace App\Domain\Organization\Actions\Positions;

use App\Domain\Organization\Models\Position;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Throwable;

class UpdatePositionAction
{
    /**
     * อัปเดตข้อมูลตำแหน่ง
     *
     * @param int $id
     * @param array $data
     * @return Position
     * @throws ModelNotFoundException|QueryException
     */
    public function execute(int $id, array $data): Position
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                $position = Position::findOrFail($id);

                // กำหนดค่า default สำหรับ is_active
                if (!isset($data['is_active'])) {
                    $data['is_active'] = false;
                }

                // ลบฟิลด์ที่ไม่มีในฐานข้อมูล
                if (isset($data['status'])) {
                    unset($data['status']);
                }

                // อัปเดตข้อมูลตำแหน่ง
                $position->update($data);

                // ปิดการใช้งาน activity log ที่ทำให้เกิดข้อผิดพลาด
                // activity()
                //     ->performedOn($position)
                //     ->withProperties([
                //         'company_id' => $position->company_id,
                //         'name' => $position->name,
                //     ])
                //     ->log('อัปเดตตำแหน่ง');

                return $position->fresh();
            });
        } catch (QueryException $e) {
            // Check if this is a unique constraint violation
            if (strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
                throw new \Exception('รหัสตำแหน่งนี้มีอยู่แล้วในบริษัทที่เลือก กรุณาเลือกรหัสอื่น', 0, $e instanceof Throwable ? $e : null);
            }

            throw $e;
        }
    }
}
