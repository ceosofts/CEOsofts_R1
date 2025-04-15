<?php

namespace App\Http\Controllers;

use App\Domain\Organization\Actions\Positions\CreatePositionAction;
use App\Domain\Organization\Actions\Positions\DeletePositionAction;
use App\Domain\Organization\Actions\Positions\FetchPositionsAction;
use App\Domain\Organization\Actions\Positions\GetPositionAction;
use App\Domain\Organization\Actions\Positions\UpdatePositionAction;
use App\Domain\Organization\Models\Company;
use App\Domain\Organization\Models\Department;
use App\Domain\Organization\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PositionController extends Controller
{
    /**
     * แสดงรายการตำแหน่งทั้งหมด
     */
    public function index(Request $request, FetchPositionsAction $fetchPositionsAction): View
    {
        // Collect all filters from request
        $filters = $request->only([
            'id',
            'name', // เปลี่ยนจาก title เป็น name ตามโครงสร้างฐานข้อมูลจริง
            'company_id',
            'department_id',
            'search',
            'sort',
            'direction',
            'status'
        ]);

        // Fetch positions with filters
        $positions = $fetchPositionsAction->execute($filters);

        // Fetch departments for dropdown
        $departments = Department::select('id', 'name')->orderBy('name')->get();

        // Fetch companies for company dropdown
        $companies = Company::all();

        return view('organization.positions.index', compact(
            'positions',
            'departments',
            'companies'
        ));
    }

    /**
     * แสดงหน้าฟอร์มสร้างตำแหน่งใหม่
     */
    public function create()
    {
        // ดึงรายการแผนก
        $departments = Department::select('id', 'name')->orderBy('name')->get();

        // ดึงรายชื่อบริษัททั้งหมด (เฉพาะ admin)
        $companies = [];
        if (Auth::user()->can('manage-all-companies')) {
            $companies = Company::select('id', 'name')->orderBy('name')->get();
        }

        return view('organization.positions.create', compact('departments', 'companies'));
    }

    /**
     * บันทึกตำแหน่งใหม่
     */
    public function store(Request $request, CreatePositionAction $action)
    {
        try {
            // ตรวจสอบข้อมูล
            $validated = $request->validate([
                'name' => 'required|string|max:255', // เปลี่ยนจาก title เป็น name
                'code' => 'nullable|string|max:50',
                'description' => 'nullable|string|max:1000',
                'is_active' => 'nullable|boolean',
                'status' => 'nullable|string|max:50',
                'company_id' => 'nullable|exists:companies,id',
                'department_id' => 'nullable|exists:departments,id',
                'metadata' => 'nullable|json',
                'level' => 'nullable|integer',
                'min_salary' => 'nullable|numeric',
                'max_salary' => 'nullable|numeric',
            ]);

            // แปลง JSON string เป็น array หากจำเป็น
            if (isset($validated['metadata']) && is_string($validated['metadata'])) {
                $validated['metadata'] = json_decode($validated['metadata'], true);
            }

            // สร้างตำแหน่งใหม่
            $position = $action->execute($validated);

            return redirect()->route('positions.index')
                ->with('success', 'สร้างตำแหน่งใหม่เรียบร้อยแล้ว');
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * แสดงรายละเอียดของตำแหน่ง
     */
    public function show($id, GetPositionAction $action)
    {
        try {
            // ดึงข้อมูลตำแหน่งพร้อมความสัมพันธ์ที่เกี่ยวข้อง
            $position = $action->execute($id);

            return view('organization.positions.show', compact('position'));
        } catch (\Exception $e) {
            return redirect()->route('positions.index')->with('error', 'ไม่พบตำแหน่งที่ระบุ');
        }
    }

    /**
     * แสดงหน้าฟอร์มแก้ไขตำแหน่ง
     */
    public function edit(Position $position)
    {
        // ดึงข้อมูลบริษัททั้งหมด
        $companies = Company::all();

        // ดึงรายการแผนก
        $departments = Department::select('id', 'name')->orderBy('name')->get();

        return view('organization.positions.edit', compact('position', 'companies', 'departments'));
    }

    /**
     * อัปเดตข้อมูลตำแหน่ง
     */
    public function update(Request $request, $id, UpdatePositionAction $action)
    {
        try {
            // ตรวจสอบข้อมูล
            $validated = $request->validate([
                'name' => 'required|string|max:255', // เปลี่ยนจาก title เป็น name
                'code' => 'nullable|string|max:50',
                'description' => 'nullable|string|max:1000',
                'is_active' => 'nullable|boolean',
                'status' => 'nullable|string|max:50',
                'company_id' => 'nullable|exists:companies,id',
                'department_id' => 'nullable|exists:departments,id',
                'metadata' => 'nullable|json',
                'level' => 'nullable|integer',
                'min_salary' => 'nullable|numeric',
                'max_salary' => 'nullable|numeric',
            ]);

            // อัปเดตข้อมูลตำแหน่ง
            $position = $action->execute($id, $validated);

            return redirect()->route('positions.index')
                ->with('success', 'อัปเดตข้อมูลตำแหน่งเรียบร้อยแล้ว');
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * ลบตำแหน่ง
     */
    public function destroy($id, DeletePositionAction $action)
    {
        try {
            $action->execute($id);
            return redirect()->route('positions.index')
                ->with('success', 'ลบตำแหน่งเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return redirect()->route('positions.index')
                ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}
