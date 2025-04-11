<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Department::class);

        $query = Department::query();

        // Filter by parent department
        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        // Filter by name
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('name_en', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        // Get root departments if no parent_id is specified
        if (!$request->has('parent_id')) {
            $query->whereNull('parent_id');
        }

        $departments = $query->orderBy('name')->paginate(10);

        return view('organization.departments.index', [
            'departments' => $departments,
            'parent' => $request->parent_id ? Department::find($request->parent_id) : null
        ]);
    }

    /**
     * Show the form for creating a new department.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $this->authorize('create', Department::class);

        $parentDepartment = null;
        if ($request->has('parent_id')) {
            $parentDepartment = Department::find($request->parent_id);
        }

        // Get list of all departments for dropdown
        $departments = Department::whereNull('parent_id')
                        ->orderBy('name')
                        ->get();

        return view('organization.departments.create', [
            'parentDepartment' => $parentDepartment,
            'departments' => $departments
        ]);
    }

    /**
     * Store a newly created department in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', Department::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50|unique:departments,code',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:departments,id',
            'is_active' => 'sometimes|boolean'
        ]);

        // Set default company_id from session
        $validated['company_id'] = session('current_company_id');
        $validated['is_active'] = $request->has('is_active');

        $department = Department::create($validated);

        return redirect()->route('departments.index')
                ->with('success', 'แผนกถูกสร้างเรียบร้อยแล้ว');
    }

    /**
     * Display the specified department.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\View\View
     */
    public function show(Department $department)
    {
        $this->authorize('view', $department);

        $children = Department::where('parent_id', $department->id)
                    ->orderBy('name')
                    ->get();

        return view('organization.departments.show', [
            'department' => $department,
            'children' => $children
        ]);
    }

    /**
     * Show the form for editing the specified department.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\View\View
     */
    public function edit(Department $department)
    {
        $this->authorize('update', $department);

        // Get list of all departments for dropdown
        $departments = Department::where('id', '!=', $department->id)
                        ->whereNull('parent_id')
                        ->orderBy('name')
                        ->get();

        return view('organization.departments.edit', [
            'department' => $department,
            'departments' => $departments
        ]);
    }

    /**
     * Update the specified department in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Department $department)
    {
        $this->authorize('update', $department);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:50|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:departments,id',
            'is_active' => 'sometimes|boolean'
        ]);

        // Prevent circular reference
        if ($validated['parent_id'] == $department->id) {
            return back()->withErrors(['parent_id' => 'แผนกไม่สามารถเป็นแผนกย่อยของตัวเองได้']);
        }

        $validated['is_active'] = $request->has('is_active');

        $department->update($validated);

        return redirect()->route('departments.index')
                ->with('success', 'แผนกถูกอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified department from storage.
     *
     * @param  \App\Models\Department  $department
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Department $department)
    {
        $this->authorize('delete', $department);

        // Check if there are child departments
        $hasChildren = Department::where('parent_id', $department->id)->exists();
        if ($hasChildren) {
            return back()->withErrors(['delete' => 'ไม่สามารถลบแผนกนี้ได้ เนื่องจากมีแผนกย่อย']);
        }

        // Check if there are employees in this department
        $hasEmployees = $department->employees()->exists();
        if ($hasEmployees) {
            return back()->withErrors(['delete' => 'ไม่สามารถลบแผนกนี้ได้ เนื่องจากมีพนักงานในแผนกนี้']);
        }

        try {
            DB::beginTransaction();
            
            // Delete related data if needed
            
            // Delete the department
            $department->delete();
            
            DB::commit();
            
            return redirect()->route('departments.index')
                    ->with('success', 'แผนกถูกลบเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['delete' => 'เกิดข้อผิดพลาดในการลบแผนกนี้: ' . $e->getMessage()]);
        }
    }
}
