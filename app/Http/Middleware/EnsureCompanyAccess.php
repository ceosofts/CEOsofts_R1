<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use Illuminate\Support\Facades\Log;

class EnsureCompanyAccess
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
        // ถ้าผู้ใช้ไม่ได้ล็อกอิน ให้ redirect ไปยังหน้าล็อกอิน
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        $companyId = session('company_id');
        
        // ถ้าเป็น superadmin หรือ admin อนุญาตให้เข้าถึงทุกบริษัท
        if ($user->hasRole(['superadmin', 'admin'])) {
            // ถ้ายังไม่ได้เลือกบริษัท ให้เลือกบริษัทแรกในระบบโดยอัตโนมัติ
            if (!$companyId) {
                $firstCompany = Company::first();
                if ($firstCompany) {
                    session(['company_id' => $firstCompany->id]);
                    Log::info('Auto-selected first company for admin: ' . $firstCompany->id);
                } else {
                    Log::warning('No companies found in the system for admin user');
                }
            }
            return $next($request);
        }

        // สำหรับผู้ใช้ทั่วไป ตรวจสอบว่ามีสิทธิ์เข้าถึงบริษัทที่เลือกหรือไม่
        $accessibleCompanies = $user->companies;
        
        // ถ้าผู้ใช้ไม่มีสิทธิ์เข้าถึงบริษัทใดๆ
        if ($accessibleCompanies->isEmpty()) {
            Log::warning("User {$user->id} has no accessible companies");
            return redirect()->route('executive.dashboard')
                ->with('error', 'คุณไม่มีสิทธิ์เข้าถึงข้อมูลบริษัทใด ๆ กรุณาติดต่อผู้ดูแลระบบ');
        }
        
        // ถ้าไม่มีบริษัทที่เลือก หรือเลือกบริษัทที่ไม่มีสิทธิ์
        if (!$companyId || !$accessibleCompanies->contains('id', $companyId)) {
            // เลือกบริษัทแรกที่มีสิทธิ์เข้าถึงโดยอัตโนมัติ
            $firstCompany = $accessibleCompanies->first();
            session(['company_id' => $firstCompany->id]);
            Log::info("Auto-selected company: {$firstCompany->id} for user: {$user->id}");
        }
        
        return $next($request);
    }
}
