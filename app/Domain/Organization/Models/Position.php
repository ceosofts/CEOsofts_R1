<?php

namespace App\Domain\Organization\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domain\HumanResources\Models\Employee;

class Position extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * คอลัมน์ที่อนุญาตให้กำหนดค่าได้
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'company_id',
        'department_id',
        'is_active',
        'status',
        'level',
        'min_salary',
        'max_salary',
        'metadata',
    ];

    /**
     * คอลัมน์ที่จะแปลงเป็น type ที่ระบุ
     */
    protected $casts = [
        'is_active' => 'boolean',
        'level' => 'integer',
        'min_salary' => 'float',
        'max_salary' => 'float',
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
     * ความสัมพันธ์กับแผนก
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * ความสัมพันธ์กับพนักงานทั้งหมดในตำแหน่งนี้
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Scope สำหรับค้นหาตำแหน่งที่ active
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // When a position is being restored, check for unique constraints
        static::restoring(function ($position) {
            // Check if another non-deleted position exists with the same code and company_id
            $exists = static::where('company_id', $position->company_id)
                ->where('code', $position->code)
                ->whereNull('deleted_at')
                ->where('id', '!=', $position->id)
                ->exists();

            return !$exists; // If one exists, prevent restoring
        });
    }
}
