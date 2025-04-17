<?php

namespace App\Http\Controllers;

use App\Models\BranchOffice;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log; // เพิ่มการ import Log Facade

class BranchOfficeController extends Controller
{
    /**
     * Display a listing of branch offices.
     */
    public function index(Request $request)
    {
        // สร้างคิวรี่
        $query = BranchOffice::with('company');
        
        // กรองตามบริษัท
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        
        // กรองตามสถานะ
        if ($request->filled('status')) {
            $query->where('is_active', $request->status == 'active');
        }
        
        // กรองตามประเภทสาขา
        if ($request->filled('type')) {
            if ($request->type === 'headquarters') {
                $query->where('is_headquarters', true);
            } else if ($request->type === 'branch') {
                $query->where('is_headquarters', false);
            }
        }
        
        // ค้นหาจากชื่อหรือรหัสสาขา
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('code', 'like', "%{$searchTerm}%")
                  ->orWhere('address', 'like', "%{$searchTerm}%");
            });
        }
        
        // จัดเรียงข้อมูล (เปลี่ยนค่าเริ่มต้นเป็น 'id')
        $sortField = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);
        
        // ดึงข้อมูลสาขาทั้งหมด
        $branchOffices = $query->paginate(10)->withQueryString();
        
        // Debug ข้อมูลสาขา
        Log::debug('Branch offices debug:', [
            'count' => $branchOffices->count(),
            'first_branch' => $branchOffices->isNotEmpty() ? [
                'id' => $branchOffices->first()->id,
                'name' => $branchOffices->first()->name,
                'code' => $branchOffices->first()->code,
                'formatted_code' => $branchOffices->first()->formatted_code,
            ] : null
        ]);
        
        // ดึงข้อมูลบริษัทสำหรับการกรอง
        $companies = Company::all();
        
        return view('organization.branch_offices.index', compact('branchOffices', 'companies'));
    }

    /**
     * Show the form for creating a new branch office.
     */
    public function create()
    {
        $companies = Company::all();
        $regions = [
            'กรุงเทพและปริมณฑล',
            'ภาคกลาง',
            'ภาคเหนือ',
            'ภาคตะวันออกเฉียงเหนือ',
            'ภาคตะวันออก',
            'ภาคตะวันตก',
            'ภาคใต้'
        ];
        
        // เพิ่มส่วนดึงข้อมูลพนักงานที่มีสถานะ active
        $managers = Employee::where('status', 'active')->get();
        
        // สร้างตัวอย่างรหัสสาขาสำหรับบริษัทแรก (ถ้ามี)
        $defaultCompany = $companies->isNotEmpty() ? $companies->first() : null;
        $nextBranchCode = $defaultCompany ? BranchOffice::generateBranchCode($defaultCompany->id) : "BRA-XX-XXX";
        
        return view('organization.branch_offices.create', compact('companies', 'regions', 'managers', 'nextBranchCode'));
    }

    /**
     * Store a newly created branch office in storage.
     */
    public function store(Request $request)
    {
        // ตรวจสอบข้อมูล
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'code' => [
                'nullable', // เปลี่ยนเป็น nullable เพื่อให้สามารถสร้างอัตโนมัติได้
                'string',
                'max:20',
                Rule::unique('branch_offices')->where(function ($query) use ($request) {
                    return $query->where('company_id', $request->company_id);
                })
            ],
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'is_headquarters' => 'sometimes|boolean',
            'manager_id' => 'nullable|exists:employees,id',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('branch-offices.create')
                ->withErrors($validator)
                ->withInput();
        }
        
        // จัดการกับข้อมูล metadata
        $metadata = [
            'region' => $request->region ?? null,
            'tax_branch_id' => $request->tax_branch_id ?? null,
            'opening_date' => $request->opening_date ?? null,
        ];
        
        // เพิ่ม metadata เพิ่มเติม
        if ($request->filled('additional_metadata_key') && $request->filled('additional_metadata_value')) {
            $metadata[$request->additional_metadata_key] = $request->additional_metadata_value;
        }

        // สร้างข้อมูลสำหรับบันทึก
        $data = $request->except(['_token', 'region', 'tax_branch_id', 'opening_date', 'additional_metadata_key', 'additional_metadata_value']);
        $data['metadata'] = $metadata;
        $data['is_headquarters'] = $request->has('is_headquarters');
        $data['is_active'] = $request->has('is_active');
        
        // ถ้าไม่มีรหัสสาขา ให้สร้างอัตโนมัติ
        if (empty($data['code'])) {
            $data['code'] = BranchOffice::generateBranchCode($request->company_id);
        }
        
        // ถ้าเป็นสำนักงานใหญ่และไม่ได้กำหนดรหัสสาขา ให้ใช้รหัสพิเศษ
        if ($data['is_headquarters'] && empty($request->code)) {
            $companyPrefix = str_pad($request->company_id, 2, '0', STR_PAD_LEFT);
            $data['code'] = "HQ-{$companyPrefix}";
        }
        
        // ถ้าเป็นสำนักงานใหญ่ ให้ตั้งค่า tax_branch_id เป็น 00000
        if ($data['is_headquarters']) {
            $metadata['tax_branch_id'] = '00000';
            $data['metadata'] = $metadata;
        }

        // บันทึกข้อมูล
        try {
            BranchOffice::create($data);
            
            return redirect()
                ->route('branch-offices.index')
                ->with('success', 'เพิ่มสาขาใหม่เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            Log::error('Error creating branch office: ' . $e->getMessage());
            
            return back()
                ->withErrors(['general' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified branch office.
     */
    public function show(BranchOffice $branchOffice)
    {
        // โหลดข้อมูลที่เกี่ยวข้อง
        $branchOffice->load('company', 'employees');
        
        // นับจำนวนพนักงานตามสถานะ
        $activeEmployees = $branchOffice->employees()->where('status', 'active')->count();
        $inactiveEmployees = $branchOffice->employees()->where('status', 'inactive')->count();
        
        return view('organization.branch_offices.show', compact(
            'branchOffice',
            'activeEmployees',
            'inactiveEmployees'
        ));
    }

    /**
     * Show the form for editing the specified branch office.
     */
    public function edit(BranchOffice $branchOffice)
    {
        $companies = Company::all();
        $regions = [
            'กรุงเทพและปริมณฑล',
            'ภาคกลาง',
            'ภาคเหนือ',
            'ภาคตะวันออกเฉียงเหนือ',
            'ภาคตะวันออก',
            'ภาคตะวันตก',
            'ภาคใต้'
        ];
        
        // เพิ่มส่วนดึงข้อมูลพนักงานที่มีสถานะ active
        $managers = Employee::where('status', 'active')->get();
        
        $metadata = $branchOffice->metadata;
        
        return view('organization.branch_offices.edit', compact('branchOffice', 'companies', 'regions', 'metadata', 'managers'));
    }

    /**
     * Update the specified branch office in storage.
     */
    public function update(Request $request, BranchOffice $branchOffice)
    {
        // ตรวจสอบข้อมูล (เพิ่ม manager_id)
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('branch_offices')->where(function ($query) use ($request) {
                    return $query->where('company_id', $request->company_id);
                })->ignore($branchOffice->id)
            ],
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_id' => 'nullable|exists:employees,id',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('branch-offices.edit', $branchOffice)
                ->withErrors($validator)
                ->withInput();
        }
        
        // จัดการกับข้อมูล metadata
        $metadata = $branchOffice->metadata ?? [];
        $metadata['region'] = $request->region;
        $metadata['tax_branch_id'] = $request->tax_branch_id;
        $metadata['opening_date'] = $request->opening_date;
        
        // เพิ่ม metadata เพิ่มเติม
        if ($request->filled('additional_metadata_key') && $request->filled('additional_metadata_value')) {
            $metadata[$request->additional_metadata_key] = $request->additional_metadata_value;
        }

        // อัพเดทข้อมูล
        try {
            $branchOffice->update([
                'company_id' => $request->company_id,
                'name' => $request->name,
                'code' => $request->code,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'is_headquarters' => $request->has('is_headquarters'),
                'is_active' => $request->has('is_active'),
                'metadata' => $metadata,
                'manager_id' => $request->manager_id, // เพิ่มการอัพเดต manager_id
            ]);
            
            return redirect()
                ->route('branch-offices.show', $branchOffice)
                ->with('success', 'อัพเดทข้อมูลสาขาเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            Log::error('Error updating branch office: ' . $e->getMessage());
            
            return back()
                ->withErrors(['general' => 'เกิดข้อผิดพลาดในการอัพเดทข้อมูล: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified branch office from storage.
     */
    public function destroy(BranchOffice $branchOffice)
    {
        // ตรวจสอบว่ามีพนักงานสังกัดสาขานี้หรือไม่
        $hasEmployees = $branchOffice->employees()->exists();
        
        if ($hasEmployees) {
            return redirect()
                ->route('branch-offices.index')
                ->with('error', 'ไม่สามารถลบสาขาได้เนื่องจากมีพนักงานสังกัดอยู่');
        }
        
        // ห้ามลบสำนักงานใหญ่ถ้าเป็นสาขาเดียว
        if ($branchOffice->is_headquarters) {
            $branchCount = BranchOffice::where('company_id', $branchOffice->company_id)->count();
            if ($branchCount <= 1) {
                return redirect()
                    ->route('branch-offices.index')
                    ->with('error', 'ไม่สามารถลบสำนักงานใหญ่ได้ เนื่องจากบริษัทต้องมีอย่างน้อยหนึ่งสาขา');
            }
        }
        
        try {
            $branchOffice->delete();
            return redirect()
                ->route('branch-offices.index')
                ->with('success', 'ลบข้อมูลสาขาเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            Log::error('Error deleting branch office: ' . $e->getMessage());
            return redirect()
                ->route('branch-offices.index')
                ->with('error', 'เกิดข้อผิดพลาดในการลบข้อมูล: ' . $e->getMessage());
        }
    }

    /**
     * Export branch offices data.
     */
    public function export(Request $request)
    {
        // สร้างคิวรี่
        $query = BranchOffice::with('company');
        
        // ใช้ filters เดียวกับ index method
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        
        if ($request->filled('status')) {
            $query->where('is_active', $request->status == 'active');
        }
        
        if ($request->filled('type')) {
            if ($request->type === 'headquarters') {
                $query->where('is_headquarters', true);
            } else if ($request->type === 'branch') {
                $query->where('is_headquarters', false);
            }
        }
        
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('code', 'like', "%{$searchTerm}%")
                  ->orWhere('address', 'like', "%{$searchTerm}%");
            });
        }
        
        // เรียงลำดับ (เปลี่ยนค่าเริ่มต้นเป็น 'id')
        $sortField = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);
        
        // ดึงข้อมูลทั้งหมด
        $branchOffices = $query->get();
        
        // สร้างชื่อไฟล์ที่เป็นเอกลักษณ์
        $filename = 'branch_offices_export_' . date('Ymd_His') . '.json';
        
        // สร้างไดเรกทอรีสำหรับส่งออกไฟล์ถ้ายังไม่มี
        $exportPath = storage_path('app/public/exports');
        if (!is_dir($exportPath)) {
            mkdir($exportPath, 0755, true);
        }
        
        // สร้างไฟล์ JSON สำหรับดาวน์โหลด
        $filePath = $exportPath . '/' . $filename;
        file_put_contents($filePath, $branchOffices->toJson(JSON_PRETTY_PRINT));
        
        // ส่งไฟล์กลับไปให้ผู้ใช้และลบไฟล์หลังจากดาวน์โหลด
        return response()->download($filePath, $filename, [
            'Content-Type' => 'application/json',
        ])->deleteFileAfterSend(true);
    }
}
