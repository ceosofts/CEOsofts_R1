<?php

namespace App\Domain\HumanResources\Models;

use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkShift extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'description',
        'start_time',
        'end_time',
        'break_start',
        'break_end',
        'working_hours',
        'is_night_shift',
        'is_active',
        'color',
        'metadata'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'break_start' => 'datetime:H:i',
        'break_end' => 'datetime:H:i',
        'working_hours' => 'float',
        'is_night_shift' => 'boolean',
        'is_active' => 'boolean',
        'metadata' => 'json'
    ];

    /**
     * Get the company that owns this work shift.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the employee shifts that use this work shift.
     */
    public function employeeWorkShifts()
    {
        return $this->hasMany(EmployeeWorkShift::class);
    }

    /**
     * Scope a query to only include active shifts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include night shifts.
     */
    public function scopeNightShift($query)
    {
        return $query->where('is_night_shift', true);
    }

    /**
     * Scope a query to only include day shifts.
     */
    public function scopeDayShift($query)
    {
        return $query->where('is_night_shift', false);
    }
}
