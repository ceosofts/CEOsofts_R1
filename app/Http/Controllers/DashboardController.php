<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * แสดงหน้า Dashboard หลัก
     */
    public function index()
    {
        // ดึงข้อมูลสถิติพื้นฐานสำหรับแสดงในหน้า Dashboard
        $companyCount = Company::count();
        $departmentCount = Department::count();

        return view('dashboard.index', [
            'companyCount' => $companyCount,
            'departmentCount' => $departmentCount,
        ]);
    }
}
