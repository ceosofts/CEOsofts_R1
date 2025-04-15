<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Company;

class SetDefaultCompany
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // If company is already selected, continue
        if (session()->has('current_company_id')) {
            return $next($request);
        }

        // Try to get the first company
        try {
            $company = Company::first();
            if ($company) {
                session(['current_company_id' => $company->id]);
            }
        } catch (\Exception $e) {
            // Log the error but continue
            \Log::error('Failed to set default company: ' . $e->getMessage());
        }

        return $next($request);
    }
}
