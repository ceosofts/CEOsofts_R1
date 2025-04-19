<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetDefaultCompany
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !session()->has('current_company_id')) {
            // ดึงบริษัทแรกของผู้ใช้
            $user = Auth::user();
            $company = $user->companies()->first();
            
            if ($company) {
                session(['current_company_id' => $company->id]);
            } else {
                // ถ้าไม่มีบริษัท ให้ใช้ค่าเริ่มต้น
                session(['current_company_id' => 1]);
            }
        }
        
        return $next($request);
    }
}
