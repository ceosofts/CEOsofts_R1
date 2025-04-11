<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get current company
        $companyId = session('current_company_id');
        
        // Quick stats
        $stats = $this->getCompanyStats($companyId);
        
        // Recent activities
        $recentActivities = $this->getRecentActivities($companyId);
        
        // Department structure
        $departmentStructure = $this->getDepartmentStructure($companyId);
        
        return view('dashboard.index', [
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'departmentStructure' => $departmentStructure
        ]);
    }
    
    /**
     * Get company statistics.
     *
     * @param int $companyId
     * @return array
     */
    private function getCompanyStats($companyId)
    {
        return [
            'departments' => Department::where('company_id', $companyId)->count(),
            'employees' => Employee::where('company_id', $companyId)->count(),
            'activeEmployees' => Employee::where('company_id', $companyId)->where('is_active', true)->count(),
        ];
    }
    
    /**
     * Get recent activities for the company.
     *
     * @param int $companyId
     * @return \Illuminate\Support\Collection
     */
    private function getRecentActivities($companyId)
    {
        // If you have an activity log table, use it here
        // This is just placeholder logic
        return collect([
            [
                'type' => 'employee_added',
                'description' => 'พนักงานใหม่ถูกเพิ่มเข้าระบบ',
                'created_at' => now()->subHours(2),
                'user' => Auth::user()->name
            ],
            [
                'type' => 'department_updated',
                'description' => 'แผนกถูกอัปเดต',
                'created_at' => now()->subDays(1),
                'user' => Auth::user()->name
            ],
        ]);
    }
    
    /**
     * Get department structure for the company.
     *
     * @param int $companyId
     * @return \Illuminate\Support\Collection
     */
    private function getDepartmentStructure($companyId)
    {
        // Get root departments
        $rootDepartments = Department::with('allChildren')
            ->where('company_id', $companyId)
            ->whereNull('parent_id')
            ->get();
            
        return $rootDepartments;
    }
    
    /**
     * Switch to a different company.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Company $company
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switchCompany(Request $request, Company $company)
    {
        // Check if user belongs to this company
        $user = Auth::user();
        if (!$user->companies->contains($company->id)) {
            return back()->with('error', 'คุณไม่มีสิทธิ์เข้าถึงบริษัทนี้');
        }
        
        // Set the new company as current
        session(['current_company_id' => $company->id]);
        
        // Update the default company if requested
        if ($request->has('set_default')) {
            DB::table('company_user')
                ->where('user_id', $user->id)
                ->update(['is_default' => false]);
                
            DB::table('company_user')
                ->where('user_id', $user->id)
                ->where('company_id', $company->id)
                ->update(['is_default' => true]);
        }
        
        return redirect()->route('dashboard')
            ->with('success', "เปลี่ยนเป็นบริษัท {$company->name} เรียบร้อยแล้ว");
    }
}
