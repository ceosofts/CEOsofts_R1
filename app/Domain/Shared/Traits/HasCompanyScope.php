<?php

namespace App\Domain\Shared\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasCompanyScope
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    protected static function bootHasCompanyScope()
    {
        // Apply scope only when authenticated
        if (Auth::check()) {
            // Get company ID safely - either from current_company_id or default to empty array
            $companyIds = [Auth::user()->current_company_id ?? 0];

            static::addGlobalScope('company', function (Builder $builder) use ($companyIds) {
                // Add company_id condition only if table has company_id column
                if (in_array('company_id', $builder->getModel()->getFillable())) {
                    $builder->whereIn('company_id', $companyIds);
                }
            });
        }
    }

    /**
     * Without company scope.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutCompanyScope($query)
    {
        return $query->withoutGlobalScope('company');
    }

    /**
     * ความสัมพันธ์กับบริษัท
     */
    public function company()
    {
        return $this->belongsTo(\App\Domain\Organization\Models\Company::class);
    }

    /**
     * กำหนดค่า company_id โดยอัตโนมัติจากผู้ใช้ปัจจุบัน
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (Auth::check() && !$model->company_id) {
                $model->company_id = Auth::user()->current_company_id;
            }
        });
    }
}
