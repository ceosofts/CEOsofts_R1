<?php

namespace App\Shared\Traits;

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
        // Add global scope to only show records from the current company
        static::addGlobalScope('company', function (Builder $builder) {
            // Skip in console or when testing
            if (app()->runningInConsole() || app()->runningUnitTests()) {
                return;
            }

            // When user is logged in and has a current company selected
            if (Auth::check() && session()->has('current_company_id')) {
                $builder->where('company_id', session('current_company_id'));
            }
        });

        // Automatically set the company_id when creating a new record
        static::creating(function ($model) {
            if (!$model->company_id && session()->has('current_company_id')) {
                $model->company_id = session('current_company_id');
            }
        });
    }

    /**
     * Scope a query to only include records from a specific company.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $companyId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Get the company that owns the model.
     */
    public function company()
    {
        return $this->belongsTo(\App\Domains\Organization\Models\Company::class);
    }
}
