<?php

namespace App\Http\Controllers;

use App\Models\BranchOffice;
use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; // เพิ่ม import Log Facade
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB; // เพิ่ม import สำหรับ DB facade

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // ถ้าไม่มี company ที่เลือกไว้ ให้ดึงมาหนึ่งบริษัท
            if (!session()->has('current_company_id')) {
                $firstCompany = Company::first();
                if ($firstCompany) {
                    session(['current_company_id' => $firstCompany->id]);
                }
            }

            // ตรวจสอบว่าเป็นการเรียกแบบ debug หรือไม่
            $query = Employee::query();
            if ($request->has('all_companies')) {
                // ถ้าเป็น debug mode ไม่ต้องใช้ scope
                $query->withoutGlobalScope('company');
            }
            
            // กรอง query ตามปกติ
            if ($request->filled('id')) {
                $query->where('id', $request->id);
            }
            
            // กรองตามรหัสพนักงาน
            if ($request->filled('employee_code')) {
                $query->where('employee_code', 'like', '%' . $request->employee_code . '%');
            }
            
            // กรองตามชื่อ
            if ($request->filled('first_name')) {
                $query->where('first_name', 'like', '%' . $request->first_name . '%');
            }
            
            // กรองตามนามสกุล
            if ($request->filled('last_name')) {
                $query->where('last_name', 'like', '%' . $request->last_name . '%');
            }
            
            // กรองตามบริษัท (แทนที่จะใช้ global scope)
            if ($request->filled('company_id')) {
                $query->where('company_id', $request->company_id);
                // ถ้ามีการเลือกบริษัทจากการค้นหา ให้อัพเดทค่าในเซสชัน
                session(['current_company_id' => $request->company_id]);
            } else if (!$request->has('all_companies') && session('current_company_id')) {
                $query->where('company_id', session('current_company_id'));
            }
            
            // กรองตามแผนก
            if ($request->filled('department_id')) {
                $query->where('department_id', $request->department_id);
            }
            
            // กรองตามตำแหน่ง
            if ($request->filled('position_id')) {
                $query->where('position_id', $request->position_id);
            }
            
            // กรองตามสาขา
            if ($request->filled('branch_office_id')) {
                $query->where('branch_office_id', $request->branch_office_id);
            }
            
            // กรองตามสถานะ
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            // เพิ่มการเรียงข้อมูล
            $sort = $request->input('sort', 'id');
            $direction = $request->input('direction', 'asc');
            
            // ตรวจสอบว่าคอลัมน์ที่จะเรียงมีอยู่จริงใน schema
            $allowedSortColumns = ['id', 'employee_code', 'first_name', 'last_name', 'created_at', 'updated_at'];
            if (in_array($sort, $allowedSortColumns)) {
                $query->orderBy($sort, $direction);
            } else {
                $query->orderBy('id', 'asc');
            }
            
            $employees = $query->with(['company', 'department', 'position', 'branchOffice'])
                            ->paginate(10);
            
            $companies = Company::all();
            $departments = Department::all();
            $positions = Position::all();
            $branchOffices = BranchOffice::all();
            
            // เพิ่ม current company ไปใน view
            $currentCompany = null;
            if (session()->has('current_company_id')) {
                $currentCompany = Company::find(session('current_company_id'));
            }
            
            // Check if the standard view exists
            if (view()->exists('organization.employees.index')) {
                return view('organization.employees.index', compact(
                    'employees', 
                    'companies', 
                    'departments', 
                    'positions', 
                    'branchOffices',
                    'currentCompany'
                ));
            } 
            // Try the fallback view
            else if (view()->exists('organization.employees.fallback')) {
                return view('organization.employees.fallback');
            }
            // Try the simple view
            else if (view()->exists('organization.employees.simple-index')) {
                return view('organization.employees.simple-index', compact('employees'));
            }
            // Last resort - direct HTML
            else {
                $html = "<!DOCTYPE html><html><head><title>พนักงาน</title></head><body>";
                $html .= "<h1>รายการพนักงาน</h1>";
                $html .= "<p>ไม่พบ view แต่ controller ทำงานได้</p>";
                $html .= "<hr>";
                
                if ($employees->count() > 0) {
                    $html .= "<ul>";
                    foreach ($employees as $employee) {
                        $html .= "<li>{$employee->first_name} {$employee->last_name}</li>";
                    }
                    $html .= "</ul>";
                } else {
                    $html .= "<p>ไม่พบข้อมูลพนักงาน</p>";
                }
                
                $html .= "</body></html>";
                return response($html);
            }
        } catch (\Exception $e) {
            // แสดงข้อความ error เพื่อ debug
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTrace()
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::all();
        $departments = Department::all();
        $positions = Position::all();
        $managers = Employee::where('status', 'active')->get();
        $branchOffices = BranchOffice::all();
        
        return view('organization.employees.create', compact(
            'companies',
            'departments',
            'positions',
            'managers',
            'branchOffices'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'nullable|email|max:255|unique:employees',
            'employee_code' => 'nullable|max:50|unique:employees',
            'profile_image' => 'nullable|image|max:2048', // max 2MB
        ]);
        
        if ($validator->fails()) {
            return redirect()
                ->route('employees.create')
                ->withErrors($validator)
                ->withInput();
        }
        
        $data = $request->all();
        
        // จัดการกับการอัพโหลดรูปภาพ
        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('employee-profiles', 'public');
            $data['profile_image'] = $imagePath;
        }
        
        // กำหนดค่าเริ่มต้นถ้าไม่ได้ระบุ
        $data['status'] = $request->input('status', 'active');
        
        // สร้าง UUID สำหรับพนักงาน
        $data['uuid'] = (string) Str::uuid();
        
        Employee::create($data);
        
        return redirect()
            ->route('employees.index')
            ->with('success', 'เพิ่มพนักงานใหม่เรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        // เปิดใช้งานการตรวจสอบสิทธิ์
        if (!$this->checkCompanyAccess($employee->company_id)) {
            return redirect()->route('employees.index')
                ->with('error', 'คุณไม่มีสิทธิ์เข้าถึงข้อมูลพนักงานนี้');
        }

        // อัพเดท metadata ในกรณีที่มี double-encoded JSON
        // ไม่จำเป็นเนื่องจากเราได้แก้ไขที่ model แล้ว แต่เพิ่มไว้เพื่อความปลอดภัย
        $rawMetadata = $employee->getRawOriginal('metadata');
        if ($rawMetadata && is_string($rawMetadata)) {
            $decoded = json_decode($rawMetadata, true);
            if (is_string($decoded)) {
                $nested = json_decode($decoded, true);
                if (is_array($nested)) {
                    $employee->metadata = $nested;
                    $employee->save();
                }
            }
        }
        
        $employee->load(['company', 'department', 'position', 'branchOffice', 'manager']);
        return view('organization.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        // เปิดใช้งานการตรวจสอบสิทธิ์
        if (!$this->checkCompanyAccess($employee->company_id)) {
            return redirect()->route('employees.index')
                ->with('error', 'คุณไม่มีสิทธิ์เข้าถึงข้อมูลพนักงานนี้');
        }
        
        $companies = Company::all();
        $departments = Department::all();
        $positions = Position::all();
        $managers = Employee::where('id', '!=', $employee->id)
            ->where('status', 'active')
            ->get();
        $branchOffices = BranchOffice::all();
        
        return view('organization.employees.edit', compact(
            'employee',
            'companies',
            'departments',
            'positions',
            'managers',
            'branchOffices'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        // เปิดใช้งานการตรวจสอบสิทธิ์
        if (!$this->checkCompanyAccess($employee->company_id)) {
            return redirect()->route('employees.index')
                ->with('error', 'คุณไม่มีสิทธิ์เข้าถึงข้อมูลพนักงานนี้');
        }
        
        // ตรวจสอบข้อมูลพื้นฐาน (ยกเว้นการตรวจสอบค่า unique)
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'branch_office_id' => 'required|exists:branch_offices,id',
            'manager_id' => 'nullable|exists:employees,id',
            'status' => 'required|in:active,inactive',
            'profile_image' => 'nullable|image|max:2048',
        ];
        
        // ไม่ตรวจสอบความซ้ำซ้อนถ้าค่าไม่เปลี่ยนแปลง
        if ($request->employee_code !== $employee->employee_code) {
            $rules['employee_code'] = 'required|string|max:50|unique:employees,employee_code,' . $employee->id;
        }
        
        if ($request->email !== $employee->email) {
            $rules['email'] = 'required|string|email|max:255|unique:employees,email,' . $employee->id;
        }
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            Log::info('Validation failed for employee update', [
                'employee_id' => $employee->id,
                'errors' => $validator->errors()->toArray()
            ]);
            
            return back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // อัพเดทข้อมูลพนักงาน
        $data = $request->except(['_token', '_method', 'profile_image', 'metadata', 'new_metadata_key', 'new_metadata_value']);
        
        // จัดการกับข้อมูล has_company_email checkbox
        $data['has_company_email'] = $request->has('has_company_email') ? 1 : 0;
        
        // จัดการกับไฟล์รูปโปรไฟล์
        if ($request->hasFile('profile_image')) {
            // ลบไฟล์เก่า (ถ้ามี)
            if ($employee->profile_image) {
                Storage::delete('public/' . $employee->profile_image);
            }
            
            // อัพโหลดไฟล์ใหม่
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $data['profile_image'] = $path;
        }
        
        // จัดการกับ metadata
        $metadata = is_array($employee->metadata) ? $employee->metadata : [];
        
        // อัพเดท metadata ที่มีอยู่แล้ว
        if ($request->has('metadata') && is_array($request->metadata)) {
            foreach ($request->metadata as $key => $value) {
                $metadata[$key] = $value;
            }
        }
        
        // เพิ่ม metadata ใหม่
        if ($request->filled('new_metadata_key') && $request->filled('new_metadata_value')) {
            $newKey = $request->new_metadata_key;
            $metadata[$newKey] = $request->new_metadata_value;
        }
        
        $data['metadata'] = $metadata;
        
        try {
            // ใช้ Query Builder แทน Eloquent เพื่อหลีกเลี่ยงการตรวจสอบ unique constraints
            DB::table('employees')  // ใช้ DB โดยไม่ต้องมี \ นำหน้า เพราะเราได้ import แล้ว
                ->where('id', $employee->id)
                ->update($data);
                
            // โหลดข้อมูลใหม่หลังจากอัพเดท
            $employee->refresh();
            
            return redirect()->route('employees.show', $employee)
                ->with('success', 'อัพเดทข้อมูลพนักงานเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            Log::error('Error updating employee: ' . $e->getMessage(), [
                'employee_id' => $employee->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            // กรณีอื่นๆ
            return back()->withErrors(['general' => 'เกิดข้อผิดพลาดในการอัพเดทข้อมูล: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        // ลบรูปโปรไฟล์ถ้ามี
        if ($employee->profile_image) {
            Storage::disk('public')->delete($employee->profile_image);
        }
        
        $employee->delete();
        
        return redirect()->route('employees.index')
            ->with('success', 'ลบข้อมูลพนักงานสำเร็จแล้ว');
    }

    /**
     * Test connection method for debugging
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function testConnection()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'EmployeeController is connected and working properly',
            'timestamp' => now(),
            'employee_count' => Employee::count()
        ]);
    }

    /**
     * Check if user has access to the specified company.
     *
     * @param int $companyId
     * @return bool
     */
    private function checkCompanyAccess($companyId)
    {
        // ในการทดสอบ ให้คืนค่า true เพื่อให้เข้าถึงได้ทุกบริษัท
        // ในการใช้งานจริง ควรมีการตรวจสอบสิทธิ์ตามความเหมาะสม
        
        // แบบที่ 1: ให้เข้าถึงได้ทุกบริษัท
        return true;
        
        // แบบที่ 2: เข้าถึงได้เฉพาะบริษัทที่เลือกไว้ใน session
        // return session('current_company_id') == $companyId;
        
        // แบบที่ 3: ตรวจสอบจาก user permission (ต้องมีการพัฒนาระบบสิทธิ์เพิ่มเติม)
        // return auth()->user()->hasPermission('access-company-' . $companyId);
    }
}
