<?php

namespace App\Models;

use App\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'name_en',
        'code',
        'description',
        'is_active',
        'parent_id',
        'company_id',
        'branch_office_id',
        'level',
        'department_code',
        'manager_id',
        'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the company that owns the department.
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
     * Get all child departments (recursive).
     */
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    /**
     * Get the employees for the department.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Scope a query to only include active departments.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include root departments (no parent).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get the branch office that owns the department.
     */
    public function branchOffice()
    {
        return $this->belongsTo(BranchOffice::class);
    }

    /**
     * Get the positions for the department.
     */
    public function positions()
    {
        return $this->belongsToMany(Position::class, 'department_position');
    }

    /**
     * Get the parent department.
     */
    public function parentDepartment()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    /**
     * Get the child departments (recursive).
     */
    public function childDepartments()
    {
        return $this->hasMany(Department::class, 'parent_id')->with('childDepartments');
    }

    /**
     * Get the manager of the department.
     */
    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    /**
     * Determine if the department is a leaf department (no child departments).
     */
    public function isLeafDepartment()
    {
        return $this->childDepartments->isEmpty();
    }
}
