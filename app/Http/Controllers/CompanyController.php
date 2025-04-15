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
}
