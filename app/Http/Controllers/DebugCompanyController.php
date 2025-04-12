<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class DebugCompanyController extends Controller
{
    public function show($id)
    {
        // ดึงข้อมูลบริษัทโดยตรง
        $company = Company::find($id);

        if (!$company) {
            return response()->json([
                'error' => 'Company not found',
                'id' => $id,
                'companies_count' => Company::count(),
                'first_company_id' => Company::first() ? Company::first()->id : null,
            ]);
        }

        // พยายามโหลด relationships
        try {
            $company->load(['departments', 'positions', 'employees']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error loading relationships',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        // ตรวจสอบว่า View สามารถ render ได้หรือไม่
        if (!View::exists('debug.company-debug')) {
            return response()->json([
                'error' => 'Debug view does not exist',
                'company' => $company->toArray(),
            ]);
        }

        return view('debug.company-debug', compact('company'));
    }

    public function list()
    {
        // แสดงรายการบริษัททั้งหมดเพื่อตรวจสอบ
        $companies = Company::all();

        return response()->json([
            'count' => $companies->count(),
            'companies' => $companies->map(function ($company) {
                return [
                    'id' => $company->id,
                    'name' => $company->name,
                    'code' => $company->code,
                    'is_active' => $company->is_active,
                    'created_at' => $company->created_at,
                ];
            }),
        ]);
    }

    public function testRelationships($id)
    {
        // ทดสอบความสัมพันธ์โดยตรง
        $company = Company::find($id);

        if (!$company) {
            return response()->json(['error' => 'Company not found']);
        }

        return response()->json([
            'company' => $company->name,
            'departments_exists' => DB::table('departments')->where('company_id', $id)->exists(),
            'departments_count' => DB::table('departments')->where('company_id', $id)->count(),
            'positions_exists' => DB::table('positions')->where('company_id', $id)->exists(),
            'positions_count' => DB::table('positions')->where('company_id', $id)->count(),
            'employees_exists' => DB::table('employees')->where('company_id', $id)->exists(),
            'employees_count' => DB::table('employees')->where('company_id', $id)->count(),
        ]);
    }
}
