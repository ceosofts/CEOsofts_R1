<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // If user is super admin, allow access to all companies
        if (auth()->user()->isSuperAdmin()) {
            return $next($request);
        }

        // If request contains company_id parameter, check if user has access to that company
        if ($request->route('company') && $request->route('company')->id !== auth()->user()->company_id) {
            abort(403, 'You do not have permission to access this company.');
        }

        // If requesting a resource with company_id field, check access
        if ($request->has('company_id') && $request->company_id != auth()->user()->company_id) {
            abort(403, 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
