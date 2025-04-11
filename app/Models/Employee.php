<?php

namespace App\Models;

use App\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Employee extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'department_id',
        'position_id',
        'employee_code',
        'title',
        'first_name',
        'last_name', 
        'nickname',
        'email',
        'phone',
        'birth_date',
        'hire_date',
        'termination_date',
        'id_card_no',
        'address',
        'emergency_contact',
        'emergency_phone',
        'salary',
        'bank_account_no',
        'bank_name',
        'profile_photo',
        'is_active',
        'uuid',
        'metadata'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'birth_date' => 'date',
        'hire_date' => 'date',
        'termination_date' => 'date',
        'salary' => 'decimal:2',
        'metadata' => 'array'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            if (!$employee->uuid) {
                $employee->uuid = (string) Str::uuid();
            }
            
            // ถ้ายังไม่มีรหัสพนักงาน ให้สร้างรหัสพนักงานอัตโนมัติ
            if (!$employee->employee_code) {
                $company = Company::find($employee->company_id);
                $prefix = $company ? substr($company->name, 0, 3) : 'EMP';
                $prefix = strtoupper(Str::ascii($prefix));
                
                $latestEmployee = self::where('company_id', $employee->company_id)
                    ->orderBy('id', 'desc')
                    ->first();
                
                $nextId = $latestEmployee ? $latestEmployee->id + 1 : 1;
                $employee->employee_code = $prefix . str_pad($nextId, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Get the company that owns the employee.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the department that owns the employee.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the position that owns the employee.
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Get the work shifts for the employee.
     */
    public function workShifts()
    {
        return $this->belongsToMany(WorkShift::class, 'employee_work_shifts')
            ->withPivot('effective_date', 'end_date', 'is_active')
            ->withTimestamps();
    }

    /**
     * Get the current work shift of the employee.
     */
    public function currentWorkShift()
    {
        return $this->workShifts()
            ->wherePivot('is_active', true)
            ->wherePivot('effective_date', '<=', now())
            ->wherePivot(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->orderByPivot('effective_date', 'desc')
            ->first();
    }

    /**
     * Get the employee's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Scope a query to only include active employees.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
