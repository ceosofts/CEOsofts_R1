<?php

namespace App\Domain\Organization\Models;

use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'company_id',
        'department_id',
        'name',
        'code',
        'level',
        'is_active',
        'min_salary',
        'max_salary',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
        'metadata' => 'json',
    ];

    /**
     * Get the company that owns this position.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the department this position belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the employees in this position.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Scope a query to only include active positions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include positions of a specific level.
     */
    public function scopeOfLevel($query, $level)
    {
        return $query->where('level', $level);
    }
}
