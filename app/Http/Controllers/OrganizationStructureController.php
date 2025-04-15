<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\BranchOffice;
use App\Models\Department;
use App\Models\Position;
use App\Models\Employee;
use Illuminate\Support\Facades\Log;

class OrganizationStructureController extends Controller
{
    public function __construct()
    {
        // เพิ่ม default company ให้กับทุก request ที่เข้ามาที่ Controller นี้
        if (!session()->has('current_company_id')) {
            $firstCompany = Company::first();
            if ($firstCompany) {
                session(['current_company_id' => $firstCompany->id]);
                Log::info('Set default company in controller', ['company_id' => $firstCompany->id]);
            } else {
                Log::warning('No companies found in OrganizationStructureController constructor');
            }
        }
    }

    public function index()
    {
        try {
            Log::info('OrganizationStructureController@index: Starting method');
            
            // กำหนดบริษัทเริ่มต้นถ้าไม่มี
            if (!session()->has('current_company_id')) {
                $firstCompany = Company::first();
                if ($firstCompany) {
                    session(['current_company_id' => $firstCompany->id]);
                    Log::info('Set default company in session', ['company_id' => $firstCompany->id]);
                }
            }
            
            // ใช้ withoutGlobalScope เพื่อข้าม company scope ถ้าจำเป็น
            // เพื่อให้แสดงบริษัททั้งหมดในรายการ
            $companies = Company::with([
                'branchOffices',
                'departments' => function($query) {
                    $query->withoutGlobalScope('company');
                },
                'departments.positions'
            ])->get();
            
            Log::info('OrganizationStructureController@index: Companies loaded', [
                'count' => $companies->count(),
                'companies' => $companies->pluck('name', 'id')
            ]);
            
            // เพิ่มการ fallback กรณีไม่พบ view
            if (!view()->exists('organization.structure.index')) {
                Log::error('View organization.structure.index not found');
                return view('coming-soon', [
                    'feature' => 'organization-structure',
                    'displayName' => 'โครงสร้างองค์กร',
                ]);
            }
            
            return view('organization.structure.index', compact('companies'));
        } catch (\Exception $e) {
            Log::error('OrganizationStructureController@index: Exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return view('coming-soon', [
                'feature' => 'organization-structure',
                'displayName' => 'โครงสร้างองค์กร (เกิดข้อผิดพลาด: ' . $e->getMessage() . ')',
            ]);
        }
    }
    
    public function show($companyId)
    {
        try {
            // เก็บ company_id ในเซสชันเพื่อให้ scope ทำงานได้ถูกต้อง
            session(['current_company_id' => $companyId]);
            
            $company = Company::with([
                'branchOffices', 
                'departments' => function($query) {
                    // ใช้ withoutGlobalScope เผื่อกรณีที่มีการใช้ scope อื่น ๆ
                    $query->withoutGlobalScope('company');
                },
                'departments.positions', 
                'employees'
            ])->findOrFail($companyId);
            
            return view('organization.structure.show', compact('company'));
        } catch (\Exception $e) {
            Log::error('OrganizationStructureController@show: Exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return view('coming-soon', [
                'feature' => 'organization-structure-details',
                'displayName' => 'รายละเอียดโครงสร้างองค์กร',
            ]);
        }
    }
    
    public function edit($companyId)
    {
        $company = Company::with(['branchOffices', 'departments.positions'])->findOrFail($companyId);
        $departments = Department::where('company_id', $companyId)->get();
        $positions = Position::where('company_id', $companyId)->get();
        
        return view('organization.structure.edit', compact('company', 'departments', 'positions'));
    }
    
    public function update(Request $request, $companyId)
    {
        $validated = $request->validate([
            'parent_departments' => 'nullable|array',
            'department_positions' => 'nullable|array',
        ]);
        
        // อัพเดทความสัมพันธ์ระหว่างแผนก (parent-child)
        if ($request->has('parent_departments')) {
            foreach ($request->parent_departments as $departmentId => $parentId) {
                $department = Department::find($departmentId);
                if ($department) {
                    $department->parent_id = $parentId ?: null;
                    $department->save();
                }
            }
        }
        
        // อัพเดทความสัมพันธ์ระหว่างแผนกและตำแหน่ง
        if ($request->has('department_positions')) {
            foreach ($request->department_positions as $departmentId => $positionIds) {
                $department = Department::find($departmentId);
                if ($department) {
                    $department->positions()->sync($positionIds);
                }
            }
        }
        
        return redirect()->route('organization.structure.show', $companyId)
            ->with('success', 'อัพเดทโครงสร้างองค์กรเรียบร้อยแล้ว');
    }
    
    public function treeView($companyId)
    {
        $company = Company::with([
            'departments' => function($query) {
                $query->whereNull('parent_id')->with('childDepartments');
            },
            'branchOffices',
        ])->findOrFail($companyId);
        
        return view('organization.structure.tree', compact('company'));
    }
    
    // API สำหรับดึงข้อมูล Organization Chart
    public function getOrganizationData($companyId)
    {
        try {
            // บันทึก session company id เพื่อให้ scope ทำงานถูกต้อง
            session(['current_company_id' => $companyId]);

            $company = Company::with([
                'departments' => function($query) {
                    $query->withoutGlobalScope('company');
                },
                'departments.positions',
                'departments.employees',
                'branchOffices',
            ])->findOrFail($companyId);
            
            // เพิ่ม logging สำหรับ debug
            Log::info('Organization data being fetched', [
                'company_id' => $companyId,
                'departments_count' => $company->departments->count()
            ]);
            
            $structure = $this->buildOrganizationTree($company);

            // ตรวจสอบโครงสร้างข้อมูลที่สร้างขึ้นว่าถูกต้องหรือไม่
            if (empty($structure['children'])) {
                Log::warning('Organization chart structure has no children', [
                    'company_id' => $companyId,
                    'structure' => $structure
                ]);
            }
            
            return response()->json([
                'company' => $company->name,
                'structure' => $structure,
            ]);
        } catch (\Throwable $e) {  // เปลี่ยนจาก Exception เป็น Throwable เพื่อจับ Error ทุกประเภท
            Log::error('Error fetching organization data', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'เกิดข้อผิดพลาดในการโหลดข้อมูล',
                'message' => $e->getMessage(),
                'details' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null
            ], 500);
        }
    }
    
    private function buildOrganizationTree($company)
    {
        try {
            $tree = [
                'id' => 'company_' . $company->id,
                'name' => $company->name,
                'title' => 'บริษัท',
                'children' => [],
            ];
            
            // ใช้ departments collection ที่ load แล้วแทนการ query ใหม่
            $rootDepartments = $company->departments->where('parent_id', null);
            
            if ($rootDepartments->isEmpty()) {
                Log::warning('No root departments found for company', [
                    'company_id' => $company->id
                ]);
            }
            
            foreach ($rootDepartments as $department) {
                try {
                    $tree['children'][] = $this->buildDepartmentTree($department);
                } catch (\Exception $e) {
                    Log::error('Error building department tree', [
                        'department_id' => $department->id,
                        'error' => $e->getMessage()
                    ]);
                    // ข้ามแผนกที่มีปัญหาและทำงานต่อ
                    continue;
                }
            }
            
            return $tree;
        } catch (\Exception $e) {
            Log::error('Error in buildOrganizationTree', [
                'message' => $e->getMessage(),
                'company_id' => $company->id
            ]);
            
            // คืนค่าโครงสร้างขั้นต่ำเพื่อไม่ให้แอพพลิเคชันล่ม
            return [
                'id' => 'company_' . $company->id,
                'name' => $company->name,
                'title' => 'บริษัท',
                'children' => []
            ];
        }
    }
    
    private function buildDepartmentTree($department)
    {
        try {
            $node = [
                'id' => 'dept_' . $department->id,
                'name' => $department->name ?? 'ไม่ระบุชื่อแผนก',
                'title' => 'แผนก',
                'children' => [],
            ];
            
            // เพิ่ม check เพื่อป้องกัน null
            if ($department->childDepartments) {
                foreach ($department->childDepartments as $childDept) {
                    try {
                        $node['children'][] = $this->buildDepartmentTree($childDept);
                    } catch (\Exception $e) {
                        Log::error('Error in child department', [
                            'department_id' => $department->id,
                            'child_id' => $childDept->id,
                            'error' => $e->getMessage()
                        ]);
                        continue;
                    }
                }
            }
            
            // เพิ่ม null check ก่อนใช้ positions
            if ($department->positions) {
                foreach ($department->positions as $position) {
                    try {
                        $posNode = [
                            'id' => 'pos_' . $position->id,
                            'name' => $position->name ?? 'ไม่ระบุชื่อตำแหน่ง',
                            'title' => 'ตำแหน่ง',
                            'children' => [],
                        ];
                        
                        // เพิ่มพนักงานในตำแหน่ง
                        $employees = Employee::where('department_id', $department->id)
                            ->where('position_id', $position->id)
                            ->get();
                            
                        foreach ($employees as $employee) {
                            $posNode['children'][] = [
                                'id' => 'emp_' . $employee->id,
                                'name' => $employee->full_name ?? $employee->name ?? 'ไม่ระบุชื่อพนักงาน',
                                'title' => 'พนักงาน',
                                'image' => $employee->photo ? asset('storage/' . $employee->photo) : null,
                            ];
                        }
                        
                        $node['children'][] = $posNode;
                    } catch (\Exception $e) {
                        Log::error('Error processing position', [
                            'position_id' => $position->id,
                            'error' => $e->getMessage()
                        ]);
                        continue;
                    }
                }
            }
            
            return $node;
        } catch (\Exception $e) {
            Log::error('Error in buildDepartmentTree', [
                'message' => $e->getMessage(),
                'department_id' => $department->id ?? 'unknown'
            ]);
            
            // คืนค่าโครงสร้างขั้นต่ำเพื่อไม่ให้แอพพลิเคชันล่ม
            return [
                'id' => 'dept_' . ($department->id ?? 'unknown'),
                'name' => $department->name ?? 'แผนกที่มีข้อผิดพลาด',
                'title' => 'แผนก',
                'children' => []
            ];
        }
    }
}
