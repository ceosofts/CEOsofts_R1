<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Domain\Organization\Models\Company as DomainCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    /**
     * Display a listing of the companies.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        try {
            // ใช้แค่ App\Models\Company โดยตรง ไม่ต้องตรวจสอบ DomainCompany
            $companies = Company::orderBy('name')->paginate(10);

            // เพิ่ม debug log ให้ชัดเจนมากขึ้น
            Log::debug('CompanyController@index: Found ' . $companies->count() . ' companies');

            // Debug ข้อมูล แสดงข้อมูลแต่ละบริษัทเพื่อตรวจสอบ
            foreach ($companies as $index => $company) {
                Log::debug("Company #{$index}: {$company->name}, active: " .
                    ($company->is_active ? 'yes' : 'no') . ", status: {$company->status}");
            }

            return view('organization.companies.index', compact('companies'));
        } catch (\Exception $e) {
            Log::error('Error in CompanyController@index: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return view('organization.companies.index', [
                'companies' => collect([]),
                'error' => 'เกิดข้อผิดพลาดในการดึงข้อมูล: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show the form for creating a new company.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('organization.companies.create');
    }

    /**
     * Store a newly created company in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:companies',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'tax_id' => 'nullable|string|max:30',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        // รองรับ UUID/ULID (ถ้ามีในระบบ)
        $validated['uuid'] = (string) Str::ulid();

        // รองรับการอัพโหลดโลโก้ (ถ้ามี)
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('company_logos', 'public');
            $validated['logo'] = $logoPath;
        }

        // สร้างข้อมูล metadata (ถ้าจำเป็น)
        $validated['metadata'] = [
            'created_from' => $request->ip(),
            'settings' => [
                'uses_fiscal_year' => $request->has('uses_fiscal_year'),
                'fiscal_year_start' => $request->input('fiscal_year_start', '01-01'),
            ],
        ];

        // ตรวจสอบว่ามีทั้ง model ทั้งใน App\Models และใน Domain
        $companyModel = class_exists(DomainCompany::class) ? DomainCompany::class : Company::class;
        $company = $companyModel::create($validated);

        return redirect()->route('companies.index')
            ->with('success', 'บริษัทถูกสร้างเรียบร้อยแล้ว');
    }

    /**
     * Display the specified company.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\View\View
     */
    public function show(Company $company)
    {
        return view('organization.companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified company.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\View\View
     */
    public function edit(Company $company)
    {
        return view('organization.companies.edit', compact('company'));
    }

    /**
     * Update the specified company in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:companies,code,' . $company->id,
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'tax_id' => 'nullable|string|max:30',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048',
            'status' => 'required|in:active,inactive',
        ]);

        // รองรับการอัพโหลดโลโก้ (ถ้ามี)
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('company_logos', 'public');
            $validated['logo'] = $logoPath;
        }

        // อัปเดต metadata (ถ้าจำเป็น)
        $metadata = $company->metadata ?? [];
        $metadata['updated_from'] = $request->ip();
        $metadata['settings'] = [
            'uses_fiscal_year' => $request->has('uses_fiscal_year'),
            'fiscal_year_start' => $request->input('fiscal_year_start', '01-01'),
        ];
        $validated['metadata'] = $metadata;

        $company->update($validated);

        return redirect()->route('companies.index')
            ->with('success', 'ข้อมูลบริษัทถูกอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified company from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Company $company)
    {
        // ตรวจสอบว่าบริษัทนี้มีข้อมูลที่เกี่ยวข้องหรือไม่
        if ($company->departments()->count() > 0 || $company->employees()->count() > 0) {
            return redirect()->route('companies.index')
                ->with('error', 'ไม่สามารถลบบริษัทได้เนื่องจากมีข้อมูลที่เกี่ยวข้อง');
        }

        $company->delete();

        return redirect()->route('companies.index')
            ->with('success', 'บริษัทถูกลบเรียบร้อยแล้ว');
    }
}
