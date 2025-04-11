<?php

namespace App\Domain\Inventory\Models;

use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'symbol',
        'base_unit_id',
        'conversion_factor',
        'is_base_unit',
        'is_active',
        'metadata'
    ];

    protected $casts = [
        'conversion_factor' => 'float',
        'is_base_unit' => 'boolean',
        'is_active' => 'boolean',
        'metadata' => 'json'
    ];

    /**
     * Get the company that owns this unit.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the base unit if this is a derived unit.
     */
    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    /**
     * Get all derived units for this base unit.
     */
    public function derivedUnits()
    {
        return $this->hasMany(Unit::class, 'base_unit_id');
    }

    /**
     * Get all products using this unit.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope a query to only include base units.
     */
    public function scopeBaseUnits($query)
    {
        return $query->where('is_base_unit', true);
    }

    /**
     * Scope a query to only include active units.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Convert a quantity from this unit to another unit.
     */
    public function convertTo($quantity, Unit $targetUnit)
    {
        if ($this->id === $targetUnit->id) {
            return $quantity;
        }

        if ($this->base_unit_id === $targetUnit->id) {
            return $quantity * $this->conversion_factor;
        }

        if ($targetUnit->base_unit_id === $this->id) {
            return $quantity / $targetUnit->conversion_factor;
        }

        return null; // Cannot convert between unrelated units
    }
}
