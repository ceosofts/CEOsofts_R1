<?php

namespace App\Domain\Organization\Models;

use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchOffice extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'address',
        'phone',
        'email',
        'is_headquarters',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_headquarters' => 'boolean',
        'is_active' => 'boolean',
        'metadata' => 'json',
    ];

    /**
     * Get the company that owns this branch office.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    /**
     * Scope a query to only include headquarters.
     */
    public function scopeHeadquarters($query)
    {
        return $query->where('is_headquarters', true);
    }
}
