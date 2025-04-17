<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Switch company and return to the previous page
     *
     * @param \App\Models\Company $company
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function switchCompany(Company $company, Request $request)
    {
        // เก็บ company ID ในเซสชัน
        session(['current_company_id' => $company->id]);
        
        // ถ้ามี ref parameter ให้กลับไปที่หน้าเดิม
        if ($request->has('ref')) {
            return redirect()->to($request->ref);
        }
        
        // ถ้าเป็นการเรียกผ่าน AJAX ให้ส่งค่ากลับเป็น JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'เปลี่ยนบริษัทเรียบร้อยแล้ว'
            ]);
        }
        
        // ค่าเริ่มต้น ให้กลับไปที่หน้า employees index
        return redirect()->route('employees.index');
    }

    public function index(Request $request)
    {
        $query = Company::query();
        
        // ค้นหาจากชื่อบริษัทหรือเลขทะเบียน
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('tax_id', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%");
            });
        }
        
        // กรองตามสถานะ
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }
        
        // เรียงลำดับ
        $query->orderBy('name', 'asc');
        
        $companies = $query->paginate(10)->withQueryString();
        
        return view('organization.companies.index', compact('companies'));
    }
}
