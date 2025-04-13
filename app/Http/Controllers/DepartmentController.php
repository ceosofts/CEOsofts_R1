<?php

namespace App\Http\Controllers;

use App\Domain\Organization\Actions\Departments\CreateDepartmentAction;
use App\Domain\Organization\Actions\Departments\DeleteDepartmentAction;
use App\Domain\Organization\Actions\Departments\FetchDepartmentsAction;
use App\Domain\Organization\Actions\Departments\GetDepartmentAction;
use App\Domain\Organization\Actions\Departments\UpdateDepartmentAction;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Department;
use App\Domain\Organization\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    /**
     * แสดงรายการแผนกทั้งหมด
     */
    public function index(Request $request, FetchDepartmentsAction $fetchDepartmentsAction): View
    {
        // Collect all filters from request
        $filters = $request->only([
            'id',
            'name',
            'company_id',
            'parent_id',
            'search',
            'sort',
            'direction',
            'status'
        ]);

        // Fetch departments with filters
        $departments = $fetchDepartmentsAction->execute($filters);

        // Fetch all departments for parent dropdown (for filtering)
        $allDepartments = $fetchDepartmentsAction->execute(['per_page' => 1000])->items();

        // Fetch companies for company dropdown
        $companies = Company::all();

        return view('organization.departments.index', compact(
            'departments',
            'allDepartments',
            'companies'
        ));
    }

    /**
     * แสดงหน้าฟอร์มสร้างแผนกใหม่
     */
    public function create()
    {
        // ดึงรายการแผนกให้เลือกเป็นแผนกแม่ (parent)
        $departments = Department::select('id', 'name')->orderBy('name')->get();

        // ดึงรายการตำแหน่งเพื่อกำหนดหัวหน้าแผนก
        $positions = Position::select('id', 'title')->orderBy('title')->get();

        // ดึงรายชื่อบริษัททั้งหมด (เฉพาะ admin)
        $companies = [];
        if (Auth::user()->can('manage-all-companies')) {
            $companies = Company::select('id', 'name')->orderBy('name')->get();
        }

        return view('organization.departments.create', compact('departments', 'positions', 'companies'));
    }

    /**
     * บันทึกแผนกใหม่
     */
    public function store(Request $request, CreateDepartmentAction $action)
    {
        try {
            // ตรวจสอบข้อมูล
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'nullable|string|max:50',
                'description' => 'nullable|string|max:1000',
                'is_active' => 'nullable|boolean',
                'status' => 'nullable|string|max:50',
                'company_id' => 'nullable|exists:companies,id',
                'parent_id' => 'nullable|exists:departments,id',
                'metadata' => 'nullable|json',
            ]);

            // แปลง JSON string เป็น array หากจำเป็น
            if (isset($validated['metadata']) && is_string($validated['metadata'])) {
                $validated['metadata'] = json_decode($validated['metadata'], true);
            }

            // สร้างแผนกใหม่
            $department = $action->execute($validated);

            return redirect()->route('departments.show', $department->id)
                ->with('success', 'สร้างแผนกใหม่เรียบร้อยแล้ว');
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * แสดงรายละเอียดของแผนก
     */
    public function show($id, GetDepartmentAction $action)
    {
        try {
            // ดึงข้อมูลแผนกพร้อมความสัมพันธ์ที่เกี่ยวข้อง
            $department = $action->execute($id);

            return view('organization.departments.show', compact('department'));
        } catch (\Exception $e) {
            return redirect()->route('departments.index')->with('error', 'ไม่พบแผนกที่ระบุ');
        }
    }

    /**
     * แสดงหน้าฟอร์มแก้ไขแผนก
     */
    public function edit(Department $department)
    {
        // เปลี่ยนจากเดิมที่อาจมีการกรองข้อมูลบริษัท เป็นดึงทั้งหมด
        $companies = Company::all();

        // ดึงรายการแผนกให้เลือกเป็นแผนกแม่ (parent)
        $departments = Department::where('id', '!=', $department->id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // ดึงรายการตำแหน่งเพื่อกำหนดหัวหน้าแผนก
        $positions = Position::select('id', 'title')->orderBy('title')->get();

        return view('organization.departments.edit', compact('department', 'companies', 'departments', 'positions'));
    }

    /**
     * อัปเดตข้อมูลแผนก
     */
    public function update(Request $request, $id, UpdateDepartmentAction $action)
    {
        try {
            // ตรวจสอบข้อมูล
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'nullable|string|max:50',
                'description' => 'nullable|string|max:1000',
                'is_active' => 'nullable|boolean',
                'status' => 'nullable|string|max:50',
                'parent_id' => 'nullable|exists:departments,id',
                'metadata' => 'nullable|json',
            ]);

            // ตรวจสอบว่าไม่ได้กำหนดตัวเองเป็น parent
            if (isset($validated['parent_id']) && $validated['parent_id'] == $id) {
                return redirect()->back()
                    ->withErrors(['parent_id' => 'ไม่สามารถกำหนดตัวเองเป็นแผนกแม่ได้'])
                    ->withInput();
            }

            // อัปเดตข้อมูลแผนก
            $department = $action->execute($id, $validated);

            // แก้ไขตรงนี้: ให้ redirect กลับไปที่หน้าแรกของแผนก แทนหน้า show
            return redirect()->route('departments.index')
                ->with('success', 'อัปเดตข้อมูลแผนกเรียบร้อยแล้ว');
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * ลบแผนก
     */
    public function destroy($id, DeleteDepartmentAction $action)
    {
        try {
            $action->execute($id);
            return redirect()->route('departments.index')
                ->with('success', 'ลบแผนกเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()->route('departments.index')
                ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}
