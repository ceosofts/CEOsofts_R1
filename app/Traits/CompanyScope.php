<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait CompanyScope
{
    /**
     * Boot the company scope trait for a model.
     *
     * @return void
     */
    public static function bootCompanyScope()
    {
        static::addGlobalScope('company', function (Builder $builder) {
            // Get company ID from session
            $companyId = session('current_company_id');
            
            // If no company is selected, try to get the first company
            if (!$companyId) {
                // Check if we're in debug mode and bypass scope if needed
                if (config('app.debug') && request()->has('debug_bypass_company_scope')) {
                    return;
                }
                
                // Try to find the first company
                try {
                    $firstCompany = \App\Models\Company::first();
                    if ($firstCompany) {
                        $companyId = $firstCompany->id;
                        session(['current_company_id' => $companyId]);
                    }
                } catch (\Exception $e) {
                    // Just use no scope if this fails
                    return;
                }
            }
            
            // Apply the company scope if we have a company ID
            if ($companyId) {
                $builder->where('company_id', $companyId);
            }
        });
    }
}
