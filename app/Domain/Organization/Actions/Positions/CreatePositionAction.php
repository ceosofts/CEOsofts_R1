<?php

namespace App\Domain\Organization\Actions\Positions;

use App\Domain\Organization\Models\Position;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Throwable;

class CreatePositionAction
{
    /**
     * สร้างตำแหน่งใหม่
     *
     * @param array $data
     * @return Position
     */
    public function execute(array $data): Position
    {
        try {
            return DB::transaction(function () use ($data) {
                // กำหนดค่า default สำหรับ is_active
                if (!isset($data['is_active'])) {
                    $data['is_active'] = false;
                }

                // ลบฟิลด์ที่ไม่มีในฐานข้อมูล
                if (isset($data['status'])) {
                    unset($data['status']);
                }

                // Check if there's a deleted position with the same code and company_id
                $existingDeletedPosition = Position::onlyTrashed()
                    ->where('code', $data['code'])
                    ->where('company_id', $data['company_id'])
                    ->first();

                // If found, restore it with the new data instead of creating a new one
                if ($existingDeletedPosition && !empty($data['code'])) {
                    $existingDeletedPosition->restore();
                    $existingDeletedPosition->update($data);
                    return $existingDeletedPosition;
                }

                // สร้างตำแหน่งใหม่
                $position = Position::create($data);

                // ปิดการใช้งาน activity log ที่ทำให้เกิดข้อผิดพลาด
                // activity()
                //     ->performedOn($position)
                //     ->withProperties([
                //         'company_id' => $position->company_id,
                //         'name' => $position->name,
                //     ])
                //     ->log('สร้างตำแหน่ง');

                return $position;
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
