<?php

namespace App\Shared\Traits;

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
        static::creating(function ($model) {
            if (!isset($model->company_id) && Auth::check()) {
                $model->company_id = Auth::user()->company_id;
            }
        });

        static::addGlobalScope('company', function (Builder $builder) {
            if (Auth::check() && !app()->runningInConsole() && !Auth::user()->isSuperAdmin()) {
                return $builder->where('company_id', Auth::user()->company_id);
            }
        });
    }

    /**
     * Get the company that owns the model.
     */
    public function company()
    {
        return $this->belongsTo(\App\Domains\Organization\Models\Company::class);
    }
}
