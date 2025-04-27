<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasCompanyScope;
use Illuminate\Support\Facades\DB;

class Unit extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

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
        'abbreviation',
        'description',
        'is_active',
        'base_unit_id',
        'conversion_factor',
        'is_default',
        'type',
        'category',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'conversion_factor' => 'decimal:5',
    ];

    /**
     * Get the base unit for this unit.
     */
    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    /**
     * Get the derived units for this base unit.
     */
    public function derivedUnits()
    {
        return $this->hasMany(Unit::class, 'base_unit_id');
    }

    /**
     * Get the company that owns the unit.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the products that use this unit.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * สร้างรหัสหน่วยอัตโนมัติในรูปแบบ UNI-{category_code}-{sequential_number}
     * 
     * @param int $companyId รหัสบริษัท
     * @param string $category ประเภทของหน่วย (เช่น weight, volume, quantity)
     * @return string
     */
    public static function generateUnitCode($companyId, $category = null)
    {
        // สร้างรหัสประเภท 2 หลักตามประเภทหน่วย หรือใช้ค่าเริ่มต้นเป็น 01
        $categoryCode = '01'; // ค่าเริ่มต้นสำหรับ quantity
        
        if ($category === 'weight') {
            $categoryCode = '02';
        } elseif ($category === 'volume') {
            $categoryCode = '03';
        } elseif ($category === 'length') {
            $categoryCode = '04';
        } elseif ($category === 'area') {
            $categoryCode = '05';
        }
        
        // ค้นหาเลขลำดับถัดไปสำหรับประเภทนี้ในบริษัท
        $lastUnit = self::where('company_id', $companyId)
            ->where('code', 'like', "UNI-{$categoryCode}-%")
            ->orderBy('code', 'desc')
            ->first();
            
        $sequentialNumber = 1;
        
        if ($lastUnit) {
            // ดึงเลขลำดับจากรหัสล่าสุด และเพิ่มขึ้น 1
            $parts = explode('-', $lastUnit->code);
            if (count($parts) >= 3) {
                $sequentialNumber = (int) $parts[2] + 1;
            }
        }
        
        // จัดรูปแบบ sequential number เป็น 3 หลัก (เช่น 001, 012, 123)
        return "UNI-{$categoryCode}-" . sprintf('%03d', $sequentialNumber);
    }
}
