<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

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
        $user = Auth::user();
        // ตรวจสอบว่าผู้ใช้มีสิทธิ์เข้าถึงบริษัทหรือเป็น superadmin
        $isSuperAdmin = $user->hasRole('superadmin') || $user->hasRole('admin');
        
        if ($isSuperAdmin || $user->companies->contains($company->id)) {
            // เก็บ company ID ในเซสชัน (ใช้ key ที่สอดคล้องกับระบบ)
            session(['company_id' => $company->id]);
            
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
        } else {
            // กรณีไม่มีสิทธิ์
            return redirect()->back()->with('error', 'คุณไม่มีสิทธิ์เข้าถึงบริษัทนี้');
        }
    }

    /**
     * Get all companies accessible by the current user
     *
     * @return \Illuminate\Http\Response
     */
    public function listAccessibleCompanies()
    {
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('superadmin') || $user->hasRole('admin');
        
        $companies = $isSuperAdmin ? Company::all() : $user->companies;
        
        return response()->json([
            'companies' => $companies,
            'is_super_admin' => $isSuperAdmin
        ]);
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

    /**
     * ส่งคำขอสิทธิ์เข้าถึงบริษัท
     */
    public function requestAccess(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'reason' => 'required|string|max:1000',
        ]);

        // บันทึกคำขอลง log หรือตาราง access_requests (สามารถพัฒนาต่อในอนาคต)
        \Log::info('ผู้ใช้ ' . auth()->user()->name . ' (ID: ' . auth()->id() . ') ต้องการขอสิทธิ์เข้าถึงบริษัท: ' . $request->company_name);
        \Log::info('เหตุผล: ' . $request->reason);
        
        // ส่งอีเมลแจ้งผู้ดูแลระบบ (สามารถพัฒนาต่อในอนาคต)
        // Mail::to('admin@example.com')->send(new CompanyAccessRequestMail($request->all()));

        return redirect()->route('executive.dashboard')
            ->with('success', 'ส่งคำขอสิทธิ์เข้าถึงบริษัทเรียบร้อยแล้ว ทางผู้ดูแลระบบจะติดต่อกลับโดยเร็วที่สุด');
    }
}
