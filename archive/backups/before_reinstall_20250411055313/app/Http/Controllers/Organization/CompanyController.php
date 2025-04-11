<?php

namespace App\Http\Controllers\Organization;

use App\Domain\Organization\Actions\CreateCompanyAction;
use App\Domain\Organization\Actions\UpdateCompanyAction;
use App\Domain\Organization\Models\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\CompanyStoreRequest;
use App\Http\Requests\Organization\CompanyUpdateRequest;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * แสดงรายการบริษัท
     */
    public function index()
    {
        $companies = Company::orderBy('name')->paginate(10);
        return view('organization.companies.index', compact('companies'));
    }

    /**
     * แสดงฟอร์มสำหรับสร้างบริษัทใหม่
     */
    public function create()
    {
        return view('organization.companies.create');
    }

    /**
     * บันทึกข้อมูลบริษัทใหม่
     */
    public function store(CompanyStoreRequest $request, CreateCompanyAction $action)
    {
        $company = $action->execute($request->validated());
        
        return redirect()->route('companies.index')
            ->with('success', "สร้างบริษัท {$company->name} สำเร็จแล้ว");
    }

    /**
     * แสดงข้อมูลรายละเอียดของบริษัท
     */
    public function show(Company $company)
    {
        return view('organization.companies.show', compact('company'));
    }

    /**
     * แสดงฟอร์มสำหรับแก้ไขข้อมูลบริษัท
     */
    public function edit(Company $company)
    {
        return view('organization.companies.edit', compact('company'));
    }

    /**
     * อัพเดทข้อมูลบริษัท
     */
    public function update(CompanyUpdateRequest $request, Company $company, UpdateCompanyAction $action)
    {
        $action->execute($company, $request->validated());
        
        return redirect()->route('companies.index')
            ->with('success', "อัพเดทข้อมูลบริษัท {$company->name} สำเร็จแล้ว");
    }

    /**
     * ลบข้อมูลบริษัท
     */
    public function destroy(Company $company)
    {
        $name = $company->name;
        $company->delete();
        
        return redirect()->route('companies.index')
            ->with('success', "ลบบริษัท {$name} สำเร็จแล้ว");
    }
}
