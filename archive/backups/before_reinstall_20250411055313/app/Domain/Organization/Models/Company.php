<?php

namespace App\Domain\Organization\Models;

use App\Domain\Shared\Traits\HasCompanyScope;
use App\Domain\Shared\Traits\HasUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasUlid, SoftDeletes;
    
    protected $fillable = [
        'name',
        'tax_id',
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'is_active',
        'settings'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];
    
    /**
     * แผนกในบริษัท
     */
    public function departments()
    {
        return $this->hasMany(Department::class);
    }
    
    /**
     * พนักงานในบริษัท
     */
    public function employees()
    {
        return $this->hasMany(\App\Domain\HumanResources\Models\Employee::class);
    }
    
    /**
     * สาขาของบริษัท
     */
    public function branches()
    {
        return $this->hasMany(BranchOffice::class);
    }
}
