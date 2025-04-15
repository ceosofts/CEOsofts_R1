<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BranchOffice extends Model
{
    use HasFactory, SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'code',
        'name_th',
        'name_en',
        'name',
        'address_th',
        'address_en',
        'tax_id',
        'phone',
        'fax',
        'email',
        'website',
        'is_headquarters',
        'status',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_headquarters' => 'boolean',
        'metadata' => 'array',
    ];
    
    /**
     * Get the company that owns the branch office.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
    
    /**
     * Get the manager for the branch office.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    /**
     * Get the employees for the branch office.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the name attribute
     * 
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->name_th ?? $this->name_en ?? '';
    }
}
