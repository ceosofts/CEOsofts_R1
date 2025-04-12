<?php

namespace App\Domain\Shared\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasCompanyScope
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function bootHasCompanyScope()
    {
        static::addGlobalScope('company', function (Builder $builder) {
            // กรองตามบริษัทของผู้ใช้ที่ล็อกอินอยู่ ยกเว้นกรณีผู้ดูแลระบบ
            if (Auth::check() && !Auth::user()->is_admin && Auth::user()->company_id) {
                $builder->where('company_id', Auth::user()->company_id);
            }
        });
    }

    /**
     * Scope a query to only include records from the current user's company.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompany($query)
    {
        if (Auth::check() && Auth::user()->company_id) {
            return $query->where('company_id', Auth::user()->company_id);
        }

        return $query;
    }
}
