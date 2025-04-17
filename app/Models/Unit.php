<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        'is_active',
        'description',
        'type',
        'category',
        'is_default',
        'is_system',
        'created_by',
        'updated_by'
    ];

    // ตรวจสอบว่ามี accessor, mutator, หรือ event listeners ที่อาจแก้ไข description
    // protected static function boot()
    // {
    //     parent::boot();
    //     static::creating(function ($model) {
    //         // มีการกำหนดค่า description หรือไม่?
    //     });
    // }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the company that owns the unit.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the base unit for the unit.
     */
    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    /**
     * Get the products for the unit.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public static function generateUnitCode($companyId)
    {
        $prefix = 'UNI';
        $companyPart = str_pad($companyId, 2, '0', STR_PAD_LEFT);
        $likePattern = "{$prefix}-{$companyPart}-%";
        $lastUnit = self::where('company_id', $companyId)
            ->where('code', 'like', $likePattern)
            ->orderByDesc('id')
            ->first();

        if ($lastUnit && preg_match("/^{$prefix}-{$companyPart}-(\d{3})$/", $lastUnit->code, $m)) {
            $nextNumber = intval($m[1]) + 1;
        } else {
            $nextNumber = 1;
        }
        
        $numberPart = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        $code = "{$prefix}-{$companyPart}-{$numberPart}";
        
        // เพิ่ม log เพื่อดูว่าสร้างรหัสอะไร
        Log::info("Generated unit code: {$code} for company: {$companyId}");
        
        return $code;
    }

    public static function normalizeAllUnitCodes()
    {
        // ใช้ transaction เพื่อให้แน่ใจว่าการทำงานสำเร็จทั้งหมดหรือล้มเหลวทั้งหมด
        return DB::transaction(function() {
            $count = 0;
            // ดึงหน่วยทั้งหมดและจัดกลุ่มตามบริษัท
            $unitsByCompany = self::all()->groupBy('company_id');
            
            foreach ($unitsByCompany as $companyId => $units) {
                $runningNumber = 1;
                
                foreach ($units as $unit) {
                    $prefix = 'UNI';
                    $companyPart = str_pad($companyId, 2, '0', STR_PAD_LEFT);
                    $numberPart = str_pad($runningNumber, 3, '0', STR_PAD_LEFT);
                    $newCode = "{$prefix}-{$companyPart}-{$numberPart}";
                    
                    // ถ้าโค้ดใหม่ไม่เหมือนโค้ดเก่า ให้อัปเดต
                    if ($unit->code !== $newCode) {
                        $unit->code = $newCode;
                        $unit->save();
                        $count++;
                    }
                    
                    $runningNumber++;
                }
            }
            
            return $count;
        });
    }
}
