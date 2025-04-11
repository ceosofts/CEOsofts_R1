<?php

namespace App\Infrastructure\MultiTenancy;

use App\Infrastructure\MultiTenancy\Exceptions\MissingCompanyScopeException;
use App\Infrastructure\Support\Services\CompanySessionService;
use Illuminate\Database\Eloquent\Builder;

trait HasCompanyScope
{
    /**
     * Boot the trait for a model.
     *
     * @return void
     */
    public static function bootHasCompanyScope()
    {
        static::addGlobalScope('company', function (Builder $builder) {
            if (!app()->runningInConsole() && !app()->runningUnitTests()) {
                $companyId = app(CompanySessionService::class)->getCurrentCompanyId();
                
                if (!$companyId) {
                    throw new MissingCompanyScopeException('No company selected for scoped operation');
                }
                
                $builder->where($builder->getModel()->getTable() . '.company_id', $companyId);
            }
        });
        
        static::creating(function ($model) {
            if (!$model->isDirty('company_id')) {
                $model->company_id = app(CompanySessionService::class)->getCurrentCompanyId();
            }
        });
    }
    
    /**
     * Remove the company scope from the query.
     *
     * @param Builder $builder
     * @return Builder
     */
    public function scopeAllCompanies(Builder $builder): Builder
    {
        return $builder->withoutGlobalScope('company');
    }
}
