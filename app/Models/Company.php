<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * ชื่อตาราง
     */
    protected $table = 'companies';

    /**
     * คอลัมน์ที่สามารถกำหนดค่าได้
     */
    protected $fillable = [
        'name',
        'code',
        'email',
        'phone',
        'tax_id',
        'address',
        'logo',
        'is_active',
    ];

    /**
     * คอลัมน์ที่ควรแปลงเป็นชนิดข้อมูลที่ไม่ใช่สตริง
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // เพิ่ม Log เพื่อดูว่า boot method ทำงานอย่างไร
        Log::info('Company model booted');

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

        // ถ้ามี Global Scope ที่กรองข้อมูลออก อาจจะอยู่ตรงนี้
        // ลองปิด scope เพื่อดูว่าช่วยแก้ปัญหาหรือไม่
        /*
        static::addGlobalScope('active', function($query) {
            // ปิด scope นี้ชั่วคราวเพื่อดูว่าเป็นสาเหตุของปัญหาหรือไม่
            // $query->where('is_active', true);
            Log::info('Company global scope applied');
        });
        */
    }

    /**
     * Debug: เพิ่ม accessor สำหรับการแสดงโลโก้
     */
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return null;
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

    /**
     * Debug function to list all used traits
     */
    public static function listTraits()
    {
        return class_uses_recursive(static::class);
    }
}
