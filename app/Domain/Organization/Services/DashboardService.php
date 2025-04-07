<?php

namespace App\Domain\Organization\Services;

use App\Domain\Sales\Models\Invoice;
use App\Domain\Sales\Models\Order;
use App\Domain\Sales\Models\Quotation;
use App\Domain\Sales\Models\Customer;
use App\Domain\HumanResources\Models\Employee;
use App\Domain\HumanResources\Models\Leave;
use App\Domain\Inventory\Models\Product;
use App\Domain\Shared\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * ดึงข้อมูลสถิติของบริษัทปัจจุบัน
     */
    public function getCompanyStats()
    {
        return [
            'employee_count' => Employee::count(),
            'customer_count' => Customer::count(),
            'invoice_count' => Invoice::count(),
            'invoice_total' => Invoice::sum('total'),
            'pending_order_count' => Order::where('status', 'pending')->count(),
            'low_stock_products' => Product::whereRaw('current_stock < min_stock')->count(),
        ];
    }
    
    /**
     * ดึงข้อมูลกิจกรรมล่าสุดในระบบ
     */
    public function getRecentActivities()
    {
        return ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }
    
    /**
     * ดึงข้อมูลยอดขายรายเดือน
     */
    public function getMonthlySalesData()
    {
        return Invoice::where('status', '!=', 'cancelled')
            ->select(DB::raw('MONTH(issue_date) as month'), DB::raw('SUM(total) as total'))
            ->whereYear('issue_date', date('Y'))
            ->groupBy(DB::raw('MONTH(issue_date)'))
            ->orderBy(DB::raw('MONTH(issue_date)'))
            ->get()
            ->pluck('total', 'month')
            ->toArray();
    }
    
    /**
     * ดึงข้อมูลประสิทธิภาพของทีม
     */
    public function getTeamPerformance()
    {
        // ตัวอย่างข้อมูล
        return [
            'sales_team_quota' => 1000000,
            'sales_team_current' => 780000,
            'hr_efficiency' => 85,
            'inventory_accuracy' => 96,
            'customer_satisfaction' => 92,
        ];
    }
    
    /**
     * ดึงข้อมูลเป้าการขาย
     */
    public function getSalesQuotaData()
    {
        // ตัวอย่างข้อมูล
        return [
            'quota' => 500000,
            'achieved' => 380000,
            'remaining' => 120000,
            'percentage' => 76,
            'forecast' => 520000,
        ];
    }
    
    /**
     * ดึงข้อมูลสินค้าขายดีที่สุด
     */
    public function getTopProducts()
    {
        // ให้เชื่อมโยงกับข้อมูลจริงในฐานข้อมูล
        return Product::withCount(['orderItems as ordered_quantity' => function ($query) {
                $query->select(DB::raw('SUM(quantity)'));
            }])
            ->orderBy('ordered_quantity', 'desc')
            ->limit(5)
            ->get();
    }
    
    /**
     * ดึงข้อมูลสถิติลูกค้า
     */
    public function getCustomerStats()
    {
        return [
            'total' => Customer::count(),
            'active' => Customer::where('status', 'active')->count(),
            'new_this_month' => Customer::whereMonth('created_at', date('m'))->count(),
            'top_customers' => Customer::withCount('invoices')
                ->withSum('invoices', 'total')
                ->orderBy('invoices_sum_total', 'desc')
                ->limit(5)
                ->get(),
        ];
    }
    
    /**
     * ดึงข้อมูลสถิติพนักงาน
     */
    public function getEmployeeStats()
    {
        return [
            'total' => Employee::count(),
            'active' => Employee::where('status', 'active')->count(),
            'on_leave_today' => Leave::where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->where('status', 'approved')
                ->count(),
            'department_distribution' => Employee::select('department_id', DB::raw('count(*) as count'))
                ->groupBy('department_id')
                ->with('department:id,name')
                ->get(),
        ];
    }
    
    /**
     * ดึงข้อมูลสถิติการลางาน
     */
    public function getLeaveStats()
    {
        return [
            'pending_approval' => Leave::where('status', 'pending')->count(),
            'approved_this_month' => Leave::where('status', 'approved')
                ->whereMonth('created_at', date('m'))
                ->count(),
            'by_type' => Leave::select('leave_type_id', DB::raw('count(*) as count'))
                ->groupBy('leave_type_id')
                ->with('leaveType:id,name')
                ->get(),
        ];
    }
}
