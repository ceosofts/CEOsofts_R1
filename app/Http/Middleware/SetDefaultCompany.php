<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Log;

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
        // ตรวจสอบว่ามีการ set ค่า default company_id หรือบังคับกรอง company_id หรือไม่
        if (!session()->has('current_company_id')) {
            $firstCompany = Company::first();
            if ($firstCompany) {
                session(['current_company_id' => $firstCompany->id]);
                Log::info('Set default company in middleware', ['company_id' => $firstCompany->id]);
            }
        }
        
        return $next($request);
    }
}
