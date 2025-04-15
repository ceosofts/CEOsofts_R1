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
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PositionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, FetchPositionsAction $action)
    {
        $positions = $action->execute($request->all());
        $companies = Company::all();
        $departments = Department::all();

        return view('organization.positions.index', compact('positions', 'companies', 'departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::all();
        $departments = Department::all();

        return view('organization.positions.create', compact('companies', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, CreatePositionAction $action)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('positions')->where(function ($query) use ($request) {
                    return $query->where('company_id', $request->company_id)
                        ->whereNull('deleted_at'); // Only check undeleted records
                })
            ],
            'company_id' => 'nullable|exists:companies,id',
            'department_id' => 'nullable|exists:departments,id',
            'description' => 'nullable|string',
            'level' => 'nullable|integer',
            'min_salary' => 'nullable|numeric',
            'max_salary' => 'nullable|numeric',
            'metadata' => 'nullable|json',
            'is_active' => 'sometimes|boolean',
        ]);

        try {
            $position = $action->execute($validatedData);
            return redirect()
                ->route('positions.show', $position)
                ->with('success', 'ตำแหน่งถูกสร้างเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            Log::error('Failed to create position: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id, GetPositionAction $action)
    {
        $position = $action->execute($id);
        return view('organization.positions.show', compact('position'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id, GetPositionAction $action)
    {
        $position = $action->execute($id);
        $companies = Company::all();
        $departments = Department::all();

        return view('organization.positions.edit', compact('position', 'companies', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id, UpdatePositionAction $action)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('positions')->where(function ($query) use ($request) {
                    return $query->where('company_id', $request->company_id)
                        ->whereNull('deleted_at'); // Only check undeleted records
                })->ignore($id)
            ],
            'company_id' => 'nullable|exists:companies,id',
            'department_id' => 'nullable|exists:departments,id',
            'description' => 'nullable|string',
            'level' => 'nullable|integer',
            'min_salary' => 'nullable|numeric',
            'max_salary' => 'nullable|numeric',
            'metadata' => 'nullable|json',
            'is_active' => 'sometimes|boolean',
        ]);

        try {
            $position = $action->execute($id, $validatedData);
            return redirect()
                ->route('positions.show', $position)
                ->with('success', 'ตำแหน่งถูกอัปเดตเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            Log::error('Failed to update position: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id, DeletePositionAction $action)
    {
        try {
            $action->execute($id);
            return redirect()
                ->route('positions.index')
                ->with('success', 'ตำแหน่งถูกลบเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            Log::error('Failed to delete position: ' . $e->getMessage());
            return back()
                ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}
