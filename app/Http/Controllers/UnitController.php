<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\Company;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        $query = Unit::query()->with('baseUnit', 'company');

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('symbol', 'like', "%{$search}%");
            });
        }
        $units = $query->orderBy('id', 'asc')->paginate(20)->withQueryString();
        $companies = Company::all();

        return view('units.index', compact('units', 'companies'));
    }

    public function create()
    {
        $companies = Company::all();
        $baseUnits = Unit::where('is_active', 1)->get();
        $defaultCompany = $companies->first();
        $nextCode = $defaultCompany ? Unit::generateUnitCode($defaultCompany->id) : 'UNI-01-001';
        return view('units.create', compact('companies', 'baseUnits', 'nextCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50',
            'symbol' => 'nullable|string|max:20',
            'base_unit_id' => 'nullable|exists:units,id',
            'conversion_factor' => 'required|numeric|min:0.0001',
            'is_active' => 'nullable',
            'description' => 'nullable|string|max:255',
            'type' => 'required|string|max:30',
            'category' => 'nullable|string|max:30',
            'is_default' => 'nullable',
            'is_system' => 'nullable',
        ]);
        $validated['is_active'] = $request->has('is_active');
        $validated['is_default'] = $request->has('is_default');
        $validated['is_system'] = $request->has('is_system');

        // Auto-generate code if not provided
        if (empty($validated['code'])) {
            $validated['code'] = Unit::generateUnitCode($validated['company_id']);
        } else {
            // ตรวจสอบ uniqueness
            $exists = Unit::where('company_id', $validated['company_id'])
                ->where('code', $validated['code'])
                ->exists();
            if ($exists) {
                return back()->withErrors(['code' => 'รหัสนี้ถูกใช้แล้วในบริษัทนี้'])->withInput();
            }
        }

        Unit::create($validated);

        return redirect()->route('units.index')->with('success', 'เพิ่มหน่วยนับเรียบร้อยแล้ว');
    }

    public function edit(Unit $unit)
    {
        $companies = Company::all();
        $baseUnits = Unit::where('is_active', 1)->where('id', '!=', $unit->id)->get();
        // แนะนำ code ถัดไปสำหรับบริษัทนี้ (ถ้าต้องการ)
        $nextCode = Unit::generateUnitCode($unit->company_id);
        return view('units.edit', compact('unit', 'companies', 'baseUnits', 'nextCode'));
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:50|unique:units,code,' . $unit->id, // เปลี่ยนเป็น nullable
            'symbol' => 'nullable|string|max:20',
            'base_unit_id' => 'nullable|exists:units,id',
            'conversion_factor' => 'required|numeric|min:0.0001',
            'is_active' => 'nullable',
            'description' => 'nullable|string|max:255',
            'type' => 'required|string|max:30',
            'category' => 'nullable|string|max:30',
            'is_default' => 'nullable',
            'is_system' => 'nullable',
        ]);
        $validated['is_active'] = $request->has('is_active');
        $validated['is_default'] = $request->has('is_default');
        $validated['is_system'] = $request->has('is_system');

        // ตรวจสอบและปรับปรุงรหัส ถ้ามีการเปลี่ยนบริษัท หรือรหัสไม่ตรงรูปแบบ
        if (empty($validated['code']) || $unit->company_id != $validated['company_id'] || 
            !preg_match("/^UNI-\d{2}-\d{3}$/", $validated['code'])) {
            $validated['code'] = Unit::generateUnitCode($validated['company_id']);
        }

        try {
            $unit->update($validated);
            return redirect()->route('units.index')->with('success', 'แก้ไขหน่วยนับเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('units.index')->with('success', 'ลบหน่วยนับเรียบร้อยแล้ว');
    }

    public function show(Unit $unit)
    {
        // โหลดความสัมพันธ์ที่จำเป็น (ถ้ายังไม่ได้ eager load)
        $unit->load(['company', 'baseUnit']);
        return view('units.show', compact('unit'));
    }
}
