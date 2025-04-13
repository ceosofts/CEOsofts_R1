<?php

namespace App\Domain\Organization\Actions\Departments;

use App\Domain\Organization\Models\Department;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FetchDepartmentsAction
{
    /**
     * ดึงข้อมูลแผนกทั้งหมด (พร้อมเงื่อนไขการค้นหา)
     */
    public function execute(array $filters = []): LengthAwarePaginator
    {
        $query = Department::query();

        // Filter by ID
        if (!empty($filters['id'])) {
            $query->where('id', $filters['id']);
        }

        // Filter by name
        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        // Filter by company_id
        if (!empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        // Filter by parent_id
        if (isset($filters['parent_id'])) {
            if ($filters['parent_id'] === 'null') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $filters['parent_id']);
            }
        }

        // Filter by status/is_active
        if (isset($filters['status']) || isset($filters['is_active'])) {
            $status = $filters['status'] ?? $filters['is_active'];
            $query->where('is_active', $status);
        }

        // Apply search on both name and code
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%');
            });
        }

        // Sort functionality
        $sortField = $filters['sort'] ?? 'id'; // Default sort by ID
        $sortDirection = $filters['direction'] ?? 'asc'; // Default sort direction is ascending

        $allowedSortFields = ['id', 'name', 'code', 'created_at'];

        // Validate sort field
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'id';
        }

        // Validate sort direction
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        $query->orderBy($sortField, $sortDirection);

        // Always include companies relation to avoid N+1 queries
        $query->with('company');

        // Default per page
        $perPage = $filters['per_page'] ?? 10;

        return $query->paginate($perPage);
    }
}
