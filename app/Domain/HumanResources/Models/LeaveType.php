<?php

namespace App\Domain\HumanResources\Models;

use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveType extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'description',
        'days_allowed',
        'days_advance_notice',
        'requires_approval',
        'requires_attachment',
        'is_paid',
        'is_active',
        'color',
        'icon',
        'metadata'
    ];

    protected $casts = [
        'days_allowed' => 'float',
        'days_advance_notice' => 'integer',
        'requires_approval' => 'boolean',
        'requires_attachment' => 'boolean',
        'is_paid' => 'boolean',
        'is_active' => 'boolean',
        'metadata' => 'json'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    public function scopeRequiresApproval($query)
    {
        return $query->where('requires_approval', true);
    }
}
