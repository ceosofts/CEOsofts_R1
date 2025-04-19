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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class ExecutiveDashboardController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            
            // ตรวจสอบว่าผู้ใช้มี role superadmin หรือ admin
            $isAdmin = $user->hasRole(['superadmin', 'admin']);
            
            // ดึงข้อมูลบริษัททั้งหมดที่ผู้ใช้มีสิทธิ์เข้าถึง
            $userCompanies = $isAdmin ? Company::all() : $user->companies;
            
            // ถ้าไม่มีบริษัทที่เข้าถึงได้
            if ($userCompanies->isEmpty()) {
                Log::warning('User ID '.$user->id.' has no accessible companies');
                return view('executive.dashboard', [
                    'error' => 'คุณไม่มีสิทธิ์เข้าถึงข้อมูลบริษัทใด ๆ กรุณาติดต่อผู้ดูแลระบบ',
                    'debug' => 'No companies available for this user',
                    'userCompanies' => collect([])
                ]);
            }
            
            // ดึงข้อมูลบริษัทปัจจุบันจาก Session
            $companyId = session('company_id');
            
            // เลือกบริษัทแรกอัตโนมัติถ้ายังไม่มีหรือไม่ถูกต้อง (ต้องทำก่อน query อื่นๆ)
            if (!$companyId || !$userCompanies->pluck('id')->contains($companyId)) {
                $companyId = $userCompanies->first()->id;
                session(['company_id' => $companyId]);
                config(['company.id' => $companyId]);
                app()->instance('company_id', $companyId);
                
                // บันทึกค่า company_id ลงใน PHP global เพื่อให้แน่ใจว่าใช้งานได้ทุกที่
                $GLOBALS['company_id'] = $companyId;
                
                Log::info("Auto-selected company ID: $companyId for user ID: {$user->id}");
            } else {
                // กำหนด context ให้กับ app/container (สำหรับ multi-tenant scope)
                app()->instance('company_id', $companyId);
                config(['company.id' => $companyId]);
                
                // บันทึกค่า company_id ลงใน PHP global เพื่อให้แน่ใจว่าใช้งานได้ทุกที่
                $GLOBALS['company_id'] = $companyId;
            }

            // ตรวจสอบอีกครั้งว่ามี company_id จริงก่อน query ใดๆ ที่ใช้ scope
            if (!$companyId) {
                Log::error("No company_id available after auto-selection");
                return view('executive.dashboard', [
                    'error' => 'ไม่พบข้อมูลบริษัทในระบบ',
                    'debug' => 'No company_id available after auto-selection',
                    'userCompanies' => $userCompanies
                ]);
            }

            // ดึงข้อมูลบริษัทที่เลือก
            $company = Company::find($companyId);
            
            // หากไม่พบข้อมูลบริษัท (ซึ่งไม่น่าเกิดขึ้นหลังจากตรวจสอบด้านบนแล้ว)
            if (!$company) {
                Log::error("Company not found with ID: {$companyId}");
                return view('executive.dashboard', [
                    'error' => 'ไม่พบข้อมูลบริษัทในระบบ',
                    'debug' => 'Selected company not found',
                    'userCompanies' => $userCompanies
                ]);
            }
            
            // สรุปข้อมูลองค์กร
            $organizationStats = [
                'departments' => Department::where('company_id', $companyId)->count(),
                'positions' => Position::count(),
                'employees' => Employee::where('company_id', $companyId)->count(),
                'active_employees' => Employee::where('company_id', $companyId)
                    ->where('status', 'active')->count(),
            ];

            // สรุปข้อมูลการขาย
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            
            $salesStats = [
                'customers' => Customer::where('company_id', $companyId)->count(),
                'customers_this_month' => Customer::where('company_id', $companyId)
                    ->whereMonth('created_at', $currentMonth)
                    ->whereYear('created_at', $currentYear)
                    ->count(),
                'quotations' => Quotation::where('company_id', $companyId)->count(),
                'quotations_this_month' => Quotation::where('company_id', $companyId)
                    ->whereMonth('created_at', $currentMonth)
                    ->whereYear('created_at', $currentYear)
                    ->count(),
                'orders' => Order::where('company_id', $companyId)->count(),
                'orders_this_month' => Order::where('company_id', $companyId)
                    ->whereMonth('created_at', $currentMonth)
                    ->whereYear('created_at', $currentYear)
                    ->count(),
            ];

            // คำสั่งซื้อล่าสุด
            $recentOrders = Order::where('company_id', $companyId)
                ->with(['customer', 'employee'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                
            // ใบเสนอราคาล่าสุด
            $recentQuotations = Quotation::where('company_id', $companyId)
                ->with(['customer', 'employee'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                
            // ข้อมูลพนักงานแยกตามแผนก
            $employeesByDepartment = $this->getEmployeesByDepartment($companyId);
            
            // ข้อมูลการขายรายเดือนสำหรับกราฟ
            $monthlySales = $this->getMonthlySalesData($companyId);

            Log::info('Dashboard loaded successfully for company: ' . $company->name);
            
            return view('executive.dashboard', compact(
                'company',
                'userCompanies',
                'isAdmin',
                'organizationStats', 
                'salesStats', 
                'monthlySales', 
                'employeesByDepartment',
                'recentOrders',
                'recentQuotations'
            ));
            
        } catch (\Exception $e) {
            Log::error('เกิดข้อผิดพลาดใน Executive Dashboard: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
                'session_company_id' => session('company_id')
            ]);

            // ตรวจสอบและตั้งค่า company_id ใน session และ context อีกรอบในกรณี error
            $user = Auth::user();
            $isAdmin = $user ? $user->hasRole(['superadmin', 'admin']) : false;
            $userCompanies = $isAdmin ? Company::all() : ($user ? $user->companies : collect([]));
            $companyId = session('company_id');
            if (!$companyId && $userCompanies->count() > 0) {
                $companyId = $userCompanies->first()->id;
                session(['company_id' => $companyId]);
                config(['company.id' => $companyId]);
                app()->instance('company_id', $companyId);
                Log::info("Auto-selected company ID (in catch): $companyId for user ID: " . ($user ? $user->id : 'guest'));
            }

            return view('executive.dashboard', [
                'error' => 'เกิดข้อผิดพลาดในการโหลดข้อมูล Dashboard',
                'debug' => $e->getMessage(),
                'userCompanies' => $userCompanies
            ]);
        }
    }

    /**
     * สลับบริษัทที่ต้องการดูข้อมูล
     */
    public function switchCompany(Request $request)
    {
        try {
            $request->validate([
                'company_id' => 'required|exists:companies,id'
            ]);
            
            $user = Auth::user();
            $companyId = $request->company_id;
            $isAdmin = $user->hasRole(['superadmin', 'admin']);
            
            // ตรวจสอบว่าผู้ใช้มีสิทธิ์เข้าถึงบริษัทนี้หรือไม่
            if ($isAdmin || $user->companies->contains('id', $companyId)) {
                session(['company_id' => $companyId]);
                Log::info('User switched company to: ' . $companyId);
                
                return redirect()->route('executive.dashboard')
                    ->with('success', 'เปลี่ยนบริษัทสำเร็จ');
            }
            
            return redirect()->route('executive.dashboard')
                ->with('error', 'คุณไม่มีสิทธิ์เข้าถึงบริษัทนี้');
                
        } catch (\Exception $e) {
            Log::error('Error switching company: ' . $e->getMessage());
            return redirect()->route('executive.dashboard')
                ->with('error', 'เกิดข้อผิดพลาดในการเปลี่ยนบริษัท');
        }
    }

    /**
     * ดึงข้อมูลการขายรายเดือนสำหรับกราฟ
     */
    private function getMonthlySalesData($companyId)
    {
        $currentYear = Carbon::now()->year;
        $months = [];
        $sales = [];
        
        // ดึงข้อมูลคำสั่งซื้อรายเดือนในปีปัจจุบัน
        for ($i = 1; $i <= 12; $i++) {
            $monthName = Carbon::create()->month($i)->format('M');
            $months[] = $monthName;
            
            // ดึงข้อมูลยอดขายสรุปรายเดือน
            try {
                // ตรวจสอบว่ามีคอลัมน์ order_date หรือไม่
                if (Schema::hasColumn('orders', 'order_date')) {
                    $monthlySale = Order::where('company_id', $companyId)
                        ->whereMonth('order_date', $i)
                        ->whereYear('order_date', $currentYear)
                        ->sum('total_amount');
                } else {
                    // ถ้าไม่มี order_date ให้ใช้ created_at แทน
                    $monthlySale = Order::where('company_id', $companyId)
                        ->whereMonth('created_at', $i)
                        ->whereYear('created_at', $currentYear)
                        ->sum('total_amount');
                }
                    
                $sales[] = $monthlySale ?: 0;
            } catch (\Exception $e) {
                Log::error('Error getting monthly sales: ' . $e->getMessage());
                $sales[] = 0;
            }
        }
        
        return [
            'labels' => $months,
            'data' => $sales
        ];
    }
    
    /**
     * ดึงข้อมูลพนักงานแยกตามแผนก
     */
    private function getEmployeesByDepartment($companyId)
    {
        $departments = Department::where('company_id', $companyId)
            ->withCount(['employees' => function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            }])
            ->get();
        $labels = $departments->pluck('name')->toArray();
        $data = $departments->pluck('employees_count')->toArray();
        
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
    
    /**
     * ดึงข้อมูลสรุปสำหรับผู้บริหารระดับสูง
     */
    public function executiveSummary()
    {
        // ข้อมูลพนักงาน
        $totalEmployees = Employee::count();
        $newEmployeesThisMonth = Employee::whereMonth('hire_date', Carbon::now()->month)
            ->whereYear('hire_date', Carbon::now()->year)
            ->count();
        
        // ข้อมูลการขาย
        $totalSales = Order::sum('total_amount');
        $salesThisMonth = Order::whereMonth('order_date', Carbon::now()->month)
            ->whereYear('order_date', Carbon::now()->year)
            ->sum('total_amount');
        
        // ข้อมูลลูกค้า
        $totalCustomers = Customer::count();
        $newCustomersThisMonth = Customer::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        
        // ข้อมูลคำขอใบเสนอราคา
        $pendingQuotations = Quotation::where('status', 'pending')->count();
        
        $summary = compact(
            'totalEmployees', 
            'newEmployeesThisMonth', 
            'totalSales', 
            'salesThisMonth',
            'totalCustomers',
            'newCustomersThisMonth',
            'pendingQuotations'
        );
        
        return view('executive.summary', compact('summary'));
    }
}
