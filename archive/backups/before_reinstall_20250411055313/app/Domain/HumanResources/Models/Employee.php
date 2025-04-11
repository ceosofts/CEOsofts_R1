<?php

namespace App\Domain\HumanResources\Models;

use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Department;
use App\Domain\Organization\Models\Position;
use App\Domain\Organization\Models\BranchOffice;
use App\Domain\Settings\Models\User;
use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Employee extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'uuid',
        'company_id',
        'department_id',
        'position_id',
        'branch_office_id',
        'employee_code',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'id_card_number',
        'birth_date',
        'hire_date',
        'status',
        'metadata'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'hire_date' => 'date',
        'metadata' => 'json'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = (string) Str::uuid();
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function branchOffice()
    {
        return $this->belongsTo(BranchOffice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
