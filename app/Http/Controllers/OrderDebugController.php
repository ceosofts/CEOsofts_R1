<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Company;
use App\Models\Product;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class OrderDebugController extends Controller
{
    public function checkConnection()
    {
        $result = [
            'success' => false,
            'connection' => null,
            'table_exists' => false,
            'order_columns' => [],
            'session' => [
                'company_id' => session('company_id'),
                'current_company_id' => session('current_company_id')
            ],
            'auth' => [
                'logged_in' => Auth::check(),
                'user_id' => Auth::id(),
                'user_name' => Auth::check() ? Auth::user()->name : null
            ],
            'tables' => [],
            'orders_count' => 0,
            'errors' => []
        ];
        
        try {
            // ตรวจสอบการเชื่อมต่อฐานข้อมูล
            $connection = DB::connection()->getPdo();
            $result['success'] = true;
            $result['connection'] = [
                'driver' => DB::connection()->getDriverName(),
                'database' => DB::connection()->getDatabaseName()
            ];
            
            // ตรวจสอบตาราง orders
            $result['table_exists'] = Schema::hasTable('orders');
            
            if ($result['table_exists']) {
                $result['order_columns'] = Schema::getColumnListing('orders');
                
                // นับจำนวน orders
                $companyId = session('company_id') ?? session('current_company_id') ?? 1;
                $result['orders_count'] = Order::where('company_id', $companyId)->count();
                $result['orders_sample'] = Order::where('company_id', $companyId)->take(5)->get(['id', 'order_number', 'customer_id', 'company_id']);
            }
            
            // ดึงรายชื่อตารางทั้งหมด
            if (DB::connection()->getDriverName() === 'sqlite') {
                $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
                $result['tables'] = array_map(function($table) {
                    return $table->name;
                }, $tables);
            } else {
                $tables = DB::select('SHOW TABLES');
                $result['tables'] = array_map(function($table) {
                    $table = (array) $table;
                    return reset($table);
                }, $tables);
            }
        } catch (\Exception $e) {
            $result['errors'][] = $e->getMessage();
        }
        
        return response()->json($result);
    }
    
    public function fixCompanyId()
    {
        $result = [
            'success' => false,
            'previous_company_id' => session('company_id'),
            'user_companies' => [],
            'actions' => []
        ];
        
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $companies = $user->companies;
                
                $result['user_companies'] = $companies->map(function($company) {
                    return [
                        'id' => $company->id,
                        'name' => $company->name
                    ];
                });
                
                if ($companies->count() > 0) {
                    // กำหนดบริษัทแรกเป็น company_id ปัจจุบัน
                    $firstCompany = $companies->first();
                    Session::put('company_id', $firstCompany->id);
                    Session::put('current_company_id', $firstCompany->id);
                    $result['actions'][] = "Set company_id to {$firstCompany->id}";
                    $result['success'] = true;
                } else {
                    // ถ้าไม่มีบริษัท ให้สร้างบริษัทตัวอย่าง
                    $result['actions'][] = "No companies found for user";
                    
                    // ตรวจสอบว่ามีบริษัทในระบบหรือไม่
                    $anyCompany = Company::first();
                    if ($anyCompany) {
                        Session::put('company_id', $anyCompany->id);
                        Session::put('current_company_id', $anyCompany->id);
                        $result['actions'][] = "Set company_id to existing company {$anyCompany->id}";
                        $result['success'] = true;
                    } else {
                        $result['actions'][] = "No companies exist in system";
                    }
                }
            } else {
                $result['actions'][] = "User not logged in";
            }
            
            // อัพเดทค่าหลังจากการแก้ไข
            $result['new_company_id'] = session('company_id');
            $result['new_current_company_id'] = session('current_company_id');
            
        } catch (\Exception $e) {
            $result['errors'][] = $e->getMessage();
        }
        
        return response()->json($result);
    }
}
