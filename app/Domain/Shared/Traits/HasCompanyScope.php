<?php

namespace App\Domain\Shared\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasCompanyScope
{
    /**
     * Boot the trait.
     * ใช้สำหรับกำหนด scope ของ model ให้อยู่ในขอบเขตของบริษัทเดียวกัน
     */
    protected static function bootHasCompanyScope()
    {
        // จัดการ global scope เมื่อมีการสร้างหรือดึงข้อมูล
        static::addGlobalScope('company', function (Builder $builder) {
            // ตรวจสอบว่ามี user ที่ login อยู่และมี company_id หรือไม่
            if (auth()->check() && auth()->user()->company_id) {
                $builder->where('company_id', auth()->user()->company_id);
            }
        });

        // เมื่อมีการสร้าง record ใหม่
        static::creating(function ($model) {
            // ถ้าไม่มีการกำหนด company_id และผู้ใช้ login อยู่
            if (!$model->company_id && auth()->check() && auth()->user()->company_id) {
                $model->company_id = auth()->user()->company_id;
            }
        });
    }

    /**
     * ขอบเขตแบบ local สำหรับกำหนดเงื่อนไขดึงข้อมูลเฉพาะบริษัท
     */
    public function scopeOfCompany($query, $companyId = null)
    {
        if ($companyId) {
            return $query->where('company_id', $companyId);
        }

        if (auth()->check() && auth()->user()->company_id) {
            return $query->where('company_id', auth()->user()->company_id);
        }

        return $query;
    }
}
