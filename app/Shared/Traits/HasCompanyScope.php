<?php

namespace App\Shared\Traits;

use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Builder;

trait HasCompanyScope
{
    /**
     * Boot the trait.
     */
    protected static function bootHasCompanyScope()
    {
        // เพิ่ม CompanyScope ที่เราเพิ่งสร้าง
        static::addGlobalScope(new CompanyScope);

        // ตรวจสอบ scope ที่เพิ่มเติมใน query และเพิ่ม method ยกเลิก scope ถ้าจำเป็น
        static::addGlobalScope('company', function (Builder $builder) {
            if (Auth::check()) {
                $companyId = session('selected_company_id', Auth::user()->current_company_id);
                if ($companyId && $companyId !== 'all') { // เพิ่มการเช็ค 'all'
                    $builder->where('company_id', $companyId);
                }
            }
        });
    }

    /**
     * Method สำหรับข้ามการกรอง company_id (ใช้สำหรับดูข้อมูลทั้งหมด)
     */
    public function scopeAllCompanies($query)
    {
        return $query->withoutGlobalScope('company')->withoutGlobalScope(CompanyScope::class);
    }

    /**
     * Get the company that owns the model.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
