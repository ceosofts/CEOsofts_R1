<?php

namespace App\Domain\Organization\Actions\Positions;

use App\Domain\Organization\Models\Position;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FetchPositionsAction
{
    /**
     * ดึงรายการตำแหน่งทั้งหมด พร้อมกรองและเรียงลำดับ
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function execute(array $filters = []): LengthAwarePaginator
    {
        $query = Position::query()->with(['company', 'department']);

        // กรองตามรหัส
        if (!empty($filters['id'])) {
            $query->where('id', $filters['id']);
        }

        // กรองตามชื่อ
        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        // กรองตามบริษัท
        if (!empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        // กรองตามแผนก
        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        // กรองตามสถานะ
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('is_active', $filters['status'] == 'active');
        }

        // ค้นหาจากข้อความ
        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('code', 'like', $searchTerm)
                    ->orWhere('description', 'like', $searchTerm);
            });
        }

        // เรียงลำดับ
        $direction = !empty($filters['direction']) ? $filters['direction'] : 'asc';
        $sortField = !empty($filters['sort']) ? $filters['sort'] : 'id';

        // ตรวจสอบว่าฟิลด์ที่ต้องการจัดเรียงมีอยู่จริง
        $allowedSortFields = ['id', 'name', 'code', 'level', 'created_at'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'id';
        }

        $query->orderBy($sortField, $direction);

        // จำนวนรายการต่อหน้า
        $perPage = !empty($filters['per_page']) ? $filters['per_page'] : 15;

        return $query->paginate($perPage)->withQueryString();
    }
}
