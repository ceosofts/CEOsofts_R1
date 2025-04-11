<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * แสดงหน้า Dashboard หลัก
     */
    public function index(Request $request)
    {
        // สร้างข้อมูลจำลองสำหรับ dashboard
        $stats = [
            'employee_count' => 25,
            'customer_count' => 48,
            'invoice_count' => 156,
            'invoice_total' => 459850.75,
            'pending_order_count' => 12,
            'low_stock_products' => 5,
        ];
        
        // สร้างข้อมูลกิจกรรมล่าสุดจำลอง
        $recentActivities = collect([
            ['event' => 'created', 'description' => 'สร้างใบเสนอราคาใหม่ #QT-2023001', 'user' => ['name' => 'สมชาย ใจดี'], 'created_at' => now()->subMinutes(15)],
            ['event' => 'updated', 'description' => 'แก้ไขข้อมูลลูกค้า บริษัท ABC จำกัด', 'user' => ['name' => 'วิชัย รักเรียน'], 'created_at' => now()->subHours(2)],
            ['event' => 'deleted', 'description' => 'ลบใบสั่งซื้อ #PO-2023042', 'user' => ['name' => 'มานี ดีใจ'], 'created_at' => now()->subHours(5)],
        ]);
        
        return view('dashboard.index', compact('stats', 'recentActivities'));
    }
}
