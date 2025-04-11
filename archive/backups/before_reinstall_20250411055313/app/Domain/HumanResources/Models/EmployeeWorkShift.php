<?php

namespace App\Domain\HumanResources\Models;

use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeWorkShift extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'employee_id',
        'work_shift_id',
        'effective_date', // เพิ่มฟิลด์ใหม่
        'work_date',
        'status',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'effective_date' => 'date', // เพิ่ม cast
        'work_date' => 'date',
        'metadata' => 'json',
    ];

    /**
     * Get the employee that owns this shift assignment.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the work shift for this assignment.
     */
    public function workShift()
    {
        return $this->belongsTo(WorkShift::class);
    }

    /**
     * Scope a query to only include shifts with a specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include shifts for a specific date.
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('work_date', $date);
    }

    /**
     * Scope a query to only include shifts within a date range.
     */
    public function scopeDateBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('work_date', [$startDate, $endDate]);
    }

    /**
     * Get company ID through employee relation.
     * This is used by the HasCompanyScope trait.
     */
    public function getCompanyIdAttribute()
    {
        return $this->employee ? $this->employee->company_id : null;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // ถ้าไม่มีการกำหนด effective_date ให้ใช้ work_date
            if (empty($model->effective_date)) {
                $model->effective_date = $model->work_date;
            }
        });
    }
}
