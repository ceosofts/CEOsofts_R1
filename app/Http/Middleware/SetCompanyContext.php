<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SetCompanyContext
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
        if (!Auth::check()) {
            return $next($request);
        }
        
        $user = Auth::user();
        $isAdmin = $user->hasRole(['superadmin', 'admin']);
        
        // ดึงข้อมูลบริษัทจาก session
        $companyId = session('company_id');
        
        // ถ้าไม่มีข้อมูลในเซสชัน
        if (!$companyId) {
            // ถ้าเป็น admin ให้เลือกบริษัทแรกในระบบ
            if ($isAdmin) {
                $company = Company::first();
                if ($company) {
                    $companyId = $company->id;
                }
            } 
            // ถ้าเป็นผู้ใช้ทั่วไป ให้เลือกบริษัทแรกที่มีสิทธิ์เข้าถึง
            else {
                $company = $user->companies()->first();
                if ($company) {
                    $companyId = $company->id;
                }
            }
            
            if ($companyId) {
                session(['company_id' => $companyId]);
                Log::info("Set company context: $companyId for user: {$user->id}");
            }
        }

        // กำหนดค่าให้กับ config และ app container เพื่อให้เข้าถึงได้ทั่วระบบ
        config(['company.id' => $companyId]);
        app()->instance('company_id', $companyId);
        
        return $next($request);
    }
}
