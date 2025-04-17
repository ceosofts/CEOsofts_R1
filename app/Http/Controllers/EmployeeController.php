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
        // แก้ไขให้มีการโหลด relationship ที่จำเป็นอย่างชัดเจน
        $query = Employee::with([
            'company', 
            'department',
            'position', // ไม่จำกัดฟิลด์ที่เลือก เพื่อให้สามารถดึงชื่อตำแหน่งได้ถูกต้อง
            'branchOffice', 
            'manager'
        ]);
        
        // เลือกบริษัทปัจจุบัน
        $currentCompanyId = null;
        $currentCompany = null;
        
        // ถ้ามีการระบุบริษัทใน URL ให้ใช้ค่านั้น
        if ($request->has('company_id') && !empty($request->company_id)) {
            $currentCompanyId = $request->company_id;
            session(['current_company_id' => $request->company_id]);
        }
        // ถ้ามีการเลือกแสดงทุกบริษัท
        else if ($request->has('all_companies')) {
            session()->forget('current_company_id');
        }
        // ถ้ายังไม่มีการเลือกใดๆ และมีการบันทึกบริษัทไว้ใน session
        else if (session('current_company_id')) {
            $currentCompanyId = session('current_company_id');
        }
        
        // ถ้ามีการตั้งค่าบริษัท ให้กรองตามบริษัทนั้น
        if ($currentCompanyId) {
            $query->where('company_id', $currentCompanyId);
            $currentCompany = Company::find($currentCompanyId);
        }
        
        // ทำการค้นหาและกรองต่างๆ หากมีการระบุ
        if ($request->filled('id')) {
            $query->where('id', $request->id);
        }
        
        // กรอง query ตามปกติ
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
        
        // จัดเรียงข้อมูล - แก้ไขให้เรียงตาม id เป็นค่าเริ่มต้น
        $sortField = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);
        
        // ดึงข้อมูลและเพิ่ม withQueryString() เพื่อรักษา parameter การค้นหา
        $employees = $query->paginate(15)->withQueryString();
        
        // ดึงข้อมูลสำหรับตัวกรอง รวมถึงความสัมพันธ์ที่จำเป็น
        $companies = Company::all();
        $departments = Department::with('company')->get();
        $positions = Position::with('department.company')->get();
        $branchOffices = BranchOffice::with('company')->get();
        
        // ใช้ employee count ในการแสดงผลการค้นหา
        $totalEmployees = Employee::count();
        $filteredCount = $employees->total();
        
        return view('organization.employees.index', compact(
            'employees', 'companies', 'departments', 'positions', 'branchOffices', 'currentCompany',
            'totalEmployees', 'filteredCount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::all();
        
        // โหลดความสัมพันธ์ของแผนกและบริษัท
        $departments = Department::with('company')->get();
        
        // โหลดความสัมพันธ์ของตำแหน่ง แผนก และบริษัท
        $positions = Position::with('department.company')->get();
        
        $managers = Employee::where('status', 'active')->get();
        $branchOffices = BranchOffice::all();
        
        // สร้างตัวอย่างรหัสที่จะใช้จริงสำหรับบริษัทแรก (ถ้ามี)
        $defaultCompany = $companies->isNotEmpty() ? $companies->first() : null;
        $nextEmployeeCode = $defaultCompany ? 
            Employee::generateEmployeeCode($defaultCompany->id) : 
            "EMP-XX-XXX";
        
        return view('organization.employees.create', compact(
            'companies',
            'departments',
            'positions',
            'managers',
            'branchOffices',
            'nextEmployeeCode',
            'defaultCompany'
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
            'id_card_number' => 'nullable|max:20',
            'tax_id' => 'nullable|max:20',
            'passport_number' => 'nullable|max:20',
            'company_email' => 'nullable|email|max:255|unique:employees',
            'bank_account' => 'nullable|max:50',
            'social_security_number' => 'nullable|max:20',
        ]);
        
        if ($validator->fails()) {
            return redirect()
                ->route('employees.create')
                ->withErrors($validator)
                ->withInput();
        }
        
        $data = $request->all();
        
        // สร้างรหัสพนักงานตามรูปแบบใหม่ถ้าไม่ได้ระบุ
        if (empty($data['employee_code'])) {
            $data['employee_code'] = Employee::generateEmployeeCode($data['company_id']);
        }
        
        // จัดการกับการอัพโหลดรูปภาพ
        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('employee-profiles', 'public');
            $data['profile_image'] = $imagePath;
        }
        
        // กำหนดค่าเริ่มต้นถ้าไม่ได้ระบุ
        $data['status'] = $request->input('status', 'active');
        
        // กำหนดค่า hire_date เป็นวันที่ปัจจุบันถ้าไม่ได้ระบุ
        if (empty($data['hire_date'])) {
            $data['hire_date'] = now()->format('Y-m-d');
        }
        
        // สร้าง UUID สำหรับพนักงาน
        $data['uuid'] = (string) Str::uuid();
        
        // จัดการฟิลด์ boolean checkbox
        $data['has_company_email'] = $request->has('has_company_email') ? 1 : 0;
        
        // ถ้าไม่มีอีเมล์บริษัท ให้เซ็ตค่าเป็น null
        if (!$data['has_company_email']) {
            $data['company_email'] = null;
        }
        
        // เก็บ log ข้อมูลที่จะบันทึก
        Log::info('Creating new employee', [
            'employee_code' => $data['employee_code'],
            'name' => $data['first_name'] . ' ' . $data['last_name'],
            'company_id' => $data['company_id']
        ]);
        
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
     * Export employees data to a downloadable file.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        // Create a query with the same filters from index method
        $query = Employee::with(['company', 'department', 'position', 'branchOffice', 'manager']);
        
        // Apply company filter if specific company selected
        $currentCompanyId = null;
        
        if ($request->filled('company_id')) {
            $currentCompanyId = $request->company_id;
            $query->where('company_id', $currentCompanyId);
        } elseif (session('current_company_id') && !$request->has('all_companies')) {
            $currentCompanyId = session('current_company_id');
            $query->where('company_id', $currentCompanyId);
        }
        
        // Apply other filters from the request
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        
        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }
        
        if ($request->filled('branch_office_id')) {
            $query->where('branch_office_id', $request->branch_office_id);
        }
        
        if ($request->filled('employee_code')) {
            $query->where('employee_code', 'like', '%' . $request->employee_code . '%');
        }
        
        if ($request->filled('first_name')) {
            $query->where('first_name', 'like', '%' . $request->first_name . '%');
        }
        
        if ($request->filled('last_name')) {
            $query->where('last_name', 'like', '%' . $request->last_name . '%');
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Get all filtered employees
        $employees = $query->get();
        
        // Generate filename with timestamp
        $filename = 'employees_export_' . date('Ymd_His') . '.json';
        
        // Create exports directory if it doesn't exist
        $exportPath = storage_path('app/public/exports');
        if (!is_dir($exportPath)) {
            mkdir($exportPath, 0755, true);
        }
        
        // Save the data to a JSON file
        $filePath = $exportPath . '/' . $filename;
        file_put_contents($filePath, $employees->toJson(JSON_PRETTY_PRINT));
        
        // Return the file as download
        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/json',
        ])->deleteFileAfterSend(true);
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