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
use Illuminate\Support\Facades\Schema; // เพิ่มบรรทัดนี้

class ExecutiveDashboardController extends Controller
{
    public function index()
    {
        // สรุปข้อมูลองค์กร
        $organizationStats = [
            'companies' => Company::count(),
            'departments' => Department::count(),
            'positions' => Position::count(),
            'employees' => Employee::count(),
            'active_employees' => Employee::where('status', 'active')->count(),
        ];

        // สรุปข้อมูลการขาย
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $salesStats = [
            'customers' => Customer::count(),
            'customers_this_month' => Customer::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count(),
            'quotations' => Quotation::count(),
            'quotations_this_month' => Quotation::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count(),
            'orders' => Order::count(),
            'orders_this_month' => Order::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->count(),
        ];

        // ข้อมูลการขายรายเดือนสำหรับกราฟ
        $monthlySales = $this->getMonthlySalesData();
        
        // ข้อมูลพนักงานแยกตามแผนก
        $employeesByDepartment = $this->getEmployeesByDepartment();

        return view('executive.dashboard', compact(
            'organizationStats', 
            'salesStats', 
            'monthlySales', 
            'employeesByDepartment'
        ));
    }

    /**
     * ดึงข้อมูลการขายรายเดือนสำหรับกราฟ
     */
    private function getMonthlySalesData()
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
                    $monthlySale = Order::whereMonth('order_date', $i)
                        ->whereYear('order_date', $currentYear)
                        ->sum('total_amount');
                } else if (Schema::hasColumn('orders', 'created_at')) {
                    // ถ้าไม่มี order_date ให้ใช้ created_at แทน
                    $monthlySale = Order::whereMonth('created_at', $i)
                        ->whereYear('created_at', $currentYear)
                        ->sum('total_amount');
                } else {
                    $monthlySale = 0;
                }
                    
                $sales[] = $monthlySale ?: 0;
            } catch (\Exception $e) {
                \Log::error('Error getting monthly sales: ' . $e->getMessage());
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
    private function getEmployeesByDepartment()
    {
        $departments = Department::withCount('employees')->get();
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
