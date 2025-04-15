<?php

namespace App\Domain\Organization\Actions\Positions;

use App\Domain\Organization\Models\Position;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GetPositionAction
{
    /**
     * ดึงข้อมูลตำแหน่ง พร้อม relationships ที่เกี่ยวข้อง
     *
     * @param int $id
     * @return Position
     */
    public function execute(int $id): Position
    {
        try {
            // ดึงข้อมูลตำแหน่งพร้อมความสัมพันธ์ที่เกี่ยวข้อง
            $position = Position::with([
                'company',
                'department',
                'employees',
            ])->findOrFail($id);

            return $position;
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException("ไม่พบตำแหน่งรหัส #{$id} หรืออาจถูกลบไปแล้ว", 404);
        }
    }
}
