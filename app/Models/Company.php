<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'name_en',
        'tax_id',
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'is_active',
        'settings',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($company) {
            if (!$company->uuid) {
                $company->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the departments for the company.
     */
    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Get the positions for the company.
     */
    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    /**
     * Get the employees for the company.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the users associated with the company.
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('is_default')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include active companies.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
