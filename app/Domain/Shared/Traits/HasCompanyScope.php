<?php

namespace App\Domain\Shared\Traits;

use App\Domain\Settings\Services\CompanySessionService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasCompanyScope
{
    /**
     * Boot the company scope trait for a model.
     * This adds a global scope to only show records for the current company.
     *
     * @return void
     */
    public static function bootHasCompanyScope()
    {
        static::addGlobalScope('company', function (Builder $builder) {
            // Skip the scope if not in a web/api context or user not authenticated
            if (!app()->runningInConsole() && Auth::check()) {
                $companyId = app()->make(CompanySessionService::class)->getCurrentCompanyId();
                
                if ($companyId) {
                    $builder->where(function ($query) use ($companyId) {
                        $query->where('company_id', $companyId);
                    });
                }
            }
        });
    }
    
    /**
     * Get the current company ID from session or user context.
     *
     * @return int|null
     */
    private function getCurrentCompanyId()
    {
        return app()->make(CompanySessionService::class)->getCurrentCompanyId();
    }
    
    /**
     * Allow working with all companies by removing the company scope temporarily.
     *
     * @param  \Closure|null  $callback
     * @return mixed
     */
    public static function withoutCompanyScope(\Closure $callback = null)
    {
        if ($callback) {
            return static::withoutGlobalScope('company')->withoutCompanyConstraint()->scoped($callback);
        }
        
        return static::withoutGlobalScope('company')->withoutCompanyConstraint();
    }
    
    /**
     * Set the company constraint to be ignored in the query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutCompanyConstraint(Builder $query)
    {
        return $query->withoutGlobalScope('company');
    }
    
    /**
     * Set the company ID for the query.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $companyId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForCompany(Builder $query, int $companyId)
    {
        return $query->withoutGlobalScope('company')->where('company_id', $companyId);
    }
}
