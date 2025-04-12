<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // ในอนาคตจะมีการแสดงรายการตำแหน่งที่นี่
        return view('organization.positions.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('organization.positions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'department_id' => 'required|exists:departments,id',
            'description' => 'nullable|string',
        ]);

        Position::create($validated);

        return redirect()->route('positions.index')
            ->with('success', 'ตำแหน่งถูกสร้างเรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     */
    public function show(Position $position)
    {
        return view('organization.positions.show', compact('position'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Position $position)
    {
        return view('organization.positions.edit', compact('position'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'department_id' => 'required|exists:departments,id',
            'description' => 'nullable|string',
        ]);

        $position->update($validated);

        return redirect()->route('positions.index')
            ->with('success', 'ตำแหน่งถูกอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Position $position)
    {
        $position->delete();

        return redirect()->route('positions.index')
            ->with('success', 'ตำแหน่งถูกลบเรียบร้อยแล้ว');
    }

    /**
     * Export positions to Excel/CSV.
     */
    public function export()
    {
        // สำหรับฟังก์ชันการส่งออกในอนาคต
        return response()->json(['message' => 'Export feature is coming soon']);
    }

    /**
     * Import positions from Excel/CSV.
     */
    public function import(Request $request)
    {
        // สำหรับฟังก์ชันการนำเข้าในอนาคต
        return redirect()->route('positions.index')
            ->with('info', 'Import feature is coming soon');
    }
}
