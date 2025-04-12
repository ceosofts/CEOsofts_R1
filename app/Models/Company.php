<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

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
        'code',
        'address',
        'phone',
        'email',
        'tax_id',
        'website',
        'logo',
        'status',
        'uuid',
        'ulid',
        'is_active',
        'settings',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'metadata' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($company) {
            // ตรวจสอบว่ามีคอลัมน์ uuid หรือ ulid
            $hasUuid = Schema::hasColumn('companies', 'uuid');
            $hasUlid = Schema::hasColumn('companies', 'ulid');

            // เพิ่มค่า UUID หรือ ULID ถ้ายังไม่มี
            if ($hasUuid && empty($company->uuid)) {
                $company->uuid = (string) Str::uuid();
            }
            if ($hasUlid && empty($company->ulid)) {
                $company->ulid = (string) Str::ulid();
            }

            // ทำให้ status และ is_active สอดคล้องกัน
            if (isset($company->status) && !isset($company->is_active)) {
                $company->is_active = $company->status === 'active';
            } elseif (isset($company->is_active) && !isset($company->status)) {
                $company->status = $company->is_active ? 'active' : 'inactive';
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
