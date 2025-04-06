<?php

namespace App\Domain\Organization\Models;

use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'description',
        'parent_id',
        'is_active',
        'status',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'json',
    ];

    /**
     * Get the company that owns this department.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the parent department.
     */
    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    /**
     * Get the child departments.
     */
    public function children()
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    /**
     * Get all employees in this department.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Scope a query to only include active departments.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include root departments (no parent).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
}
