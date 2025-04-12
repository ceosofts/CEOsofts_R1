<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'code',
        'symbol',
        'base_unit_id',
        'conversion_factor',
        'description',
        'type',
        'category',
        'is_default',
        'is_system',
        'is_active',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'conversion_factor' => 'decimal:5',
        'is_default' => 'boolean',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * บริษัทที่เป็นเจ้าของหน่วยวัดนี้
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * หน่วยวัดพื้นฐานของหน่วยนี้ (ถ้ามี)
     */
    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    /**
     * หน่วยวัดที่แปลงมาจากหน่วยนี้
     */
    public function derivedUnits()
    {
        return $this->hasMany(Unit::class, 'base_unit_id');
    }

    /**
     * ผู้สร้างข้อมูล
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * ผู้อัปเดตข้อมูลล่าสุด
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
