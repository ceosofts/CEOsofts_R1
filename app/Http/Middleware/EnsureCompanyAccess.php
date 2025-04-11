<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // If user is not logged in, redirect to login
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check if user has selected a company
        if (!session()->has('current_company_id')) {
            // If user has no companies, show error
            if ($user->companies->isEmpty()) {
                return response()->view('errors.company-required');
            }
            
            // Otherwise, set the default company or the first company
            $defaultCompany = $user->companies->firstWhere('pivot.is_default', true);
            $company = $defaultCompany ?: $user->companies->first();
            
            session(['current_company_id' => $company->id]);
        }
        
        return $next($request);
    }
}
