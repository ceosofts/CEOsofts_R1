<?php

namespace App\Domain\Organization\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domain\HumanResources\Models\Employee;

class Department extends Model
{
    use HasFactory, SoftDeletes;
    // ได้ลบ HasCompanyScope ออกไปแล้วเพื่อแก้ปัญหา error

    /**
     * คอลัมน์ที่อนุญาตให้กำหนดค่าได้
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'company_id',
        'parent_id',
        'is_active',
        'status',
        'metadata',
    ];

    /**
     * คอลัมน์ที่จะแปลงเป็น type ที่ระบุ
     */
    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'json',
    ];

    /**
     * ความสัมพันธ์กับบริษัท
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * ความสัมพันธ์กับแผนกแม่ (parent)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    /**
     * ความสัมพันธ์กับแผนกลูก (children)
     */
    public function children(): HasMany
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    /**
     * ความสัมพันธ์กับตำแหน่งทั้งหมดในแผนก
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    /**
     * ความสัมพันธ์กับพนักงานทั้งหมดในแผนก
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Scope สำหรับค้นหาแผนกที่ active
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
