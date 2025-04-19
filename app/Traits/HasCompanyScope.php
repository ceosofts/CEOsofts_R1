<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasCompanyScope
{
    /**
     * Boot the has company scope trait for a model.
     *
     * @return void
     */
    protected static function bootHasCompanyScope()
    {
        static::addGlobalScope('company', function (Builder $builder) {
            // ตรวจสอบว่ามี user ที่ login หรือไม่ และมี company_id หรือไม่
            if (Auth::check()) {
                // ใช้ company_id จาก session ถ้ามี หรือใช้จาก user ที่ login อยู่
                $companyId = session('company_id') ?? Auth::user()->company_id;
                
                // ใส่เงื่อนไขในการ query เฉพาะข้อมูลของบริษัทที่ login อยู่
                if ($companyId) {
                    $builder->where('company_id', $companyId);
                }
            }
        });
    }

    /**
     * ยกเลิก company scope ชั่วคราว
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutCompanyScope($query)
    {
        return $query->withoutGlobalScope('company');
    }
    
    /**
     * ดึงข้อมูลทั้งหมดโดยไม่คำนึงถึงบริษัท
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function allCompanies()
    {
        return static::withoutGlobalScope('company');
    }
}
