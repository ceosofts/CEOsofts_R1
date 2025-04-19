<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Quotation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NewExecutiveController extends Controller
{
    public function dashboard()
    {
        // ดึงข้อมูลผู้ใช้ปัจจุบัน
        $user = Auth::user();
        
        // ตรวจสอบว่าผู้ใช้เป็น admin หรือ superadmin หรือไม่
        $isAdmin = $user->hasRole(['superadmin', 'admin']);
        
        // ดึงข้อมูลบริษัทที่ผู้ใช้มีสิทธิ์เข้าถึง
        $userCompanies = $isAdmin ? Company::all() : $user->companies;
        
        // ถ้าไม่มีบริษัทเลย ให้ส่งข้อความแจ้งเตือน
        if ($userCompanies->isEmpty()) {
            return view('executive.new-dashboard', [
                'error' => 'คุณไม่มีสิทธิ์เข้าถึงข้อมูลบริษัทใดๆ กรุณาติดต่อผู้ดูแลระบบ',
                'userCompanies' => collect([])
            ]);
        }
        
        // ดึงบริษัทที่เลือกจาก session หรือใช้บริษัทแรกจากรายการที่มีสิทธิ์
        $companyId = session('company_id');
        if (!$companyId || !$userCompanies->contains('id', $companyId)) {
            $companyId = $userCompanies->first()->id;
            session(['company_id' => $companyId]);
        }
        
        // ดึงข้อมูลบริษัท
        $company = Company::find($companyId);
        
        // สรุปข้อมูลสถิติ - โดยใช้ SQL query ตรงๆ เพื่อหลีกเลี่ยง scope
        $stats = [
            // ข้อมูลทั่วไป
            'departments_count' => DB::table('departments')->where('company_id', $companyId)->count(),
            'employees_count' => DB::table('employees')->where('company_id', $companyId)->count(),
            'active_employees' => DB::table('employees')
                ->where('company_id', $companyId)
                ->where('status', 'active')
                ->count(),
                
            // ข้อมูลการขาย
            'customers_count' => DB::table('customers')
                ->where('company_id', $companyId)
                ->count(),
            'orders_count' => DB::table('orders')
                ->where('company_id', $companyId)
                ->count(),
            'orders_this_month' => DB::table('orders')
                ->where('company_id', $companyId)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
            'quotations_count' => DB::table('quotations')
                ->where('company_id', $companyId)
                ->count()
        ];
        
        // ดึงข้อมูลออเดอร์ล่าสุด 5 รายการ
        $recentOrders = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('orders.company_id', $companyId)
            ->select('orders.*', 'customers.name as customer_name')
            ->orderBy('orders.created_at', 'desc')
            ->limit(5)
            ->get();
            
        // ดึงข้อมูลลูกค้าล่าสุด 5 ราย
        $recentCustomers = DB::table('customers')
            ->where('company_id', $companyId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // ส่งข้อมูลไปแสดงผล
        return view('executive.new-dashboard', [
            'company' => $company,
            'userCompanies' => $userCompanies,
            'stats' => $stats,
            'isAdmin' => $isAdmin,
            'recentOrders' => $recentOrders,
            'recentCustomers' => $recentCustomers
        ]);
    }
    
    public function switchCompany(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id'
        ]);
        
        // บันทึกบริษัทที่เลือกลงใน session
        session(['company_id' => $request->company_id]);
        
        return redirect()->route('executive.new-dashboard')
            ->with('success', 'เปลี่ยนบริษัทเรียบร้อยแล้ว');
    }
}
