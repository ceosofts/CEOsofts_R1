<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; // เพิ่มการนำเข้า Log facade

class CompaniesController extends Controller
{
    /**
     * แสดงรายการบริษัททั้งหมด
     */
    public function index()
    {
        // ดึงข้อมูลบริษัททั้งหมดพร้อมการแบ่งหน้า
        $companies = Company::orderBy('created_at', 'desc')->paginate(10);

        // ส่งข้อมูลไปยัง view
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
     * บันทึกข้อมูลบริษัทที่สร้างใหม่
     */
    public function store(Request $request)
    {
        // ตรวจสอบข้อมูลที่ส่งมา
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:companies',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'tax_id' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|max:2048', // รูปภาพขนาดไม่เกิน 2MB
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // สร้างข้อมูลบริษัท
        $company = new Company();
        $company->name = $request->name;
        $company->code = $request->code;
        $company->email = $request->email;
        $company->phone = $request->phone;
        $company->tax_id = $request->tax_id;
        $company->address = $request->address;
        $company->is_active = $request->has('is_active') ? 1 : 0;

        // อัปโหลดโลโก้ (ถ้ามี)
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('company_logos', 'public');
            $company->logo = $path;
        }

        $company->save();

        return redirect()->route('companies.index')
            ->with('success', 'สร้างบริษัทใหม่สำเร็จ');
    }

    /**
     * แสดงข้อมูลรายละเอียดของบริษัท
     */
    public function show(Company $company)
    {
        // เพิ่ม Debug Info
        Log::info('Company Show Method Called', ['id' => $company->id, 'name' => $company->name]);

        try {
            // พยายามโหลดความสัมพันธ์แบบมี error handling
            $company->load(['departments', 'positions', 'employees']);
            Log::info('Relationships loaded successfully');
        } catch (\Exception $e) {
            Log::error('Error loading relationships', ['error' => $e->getMessage()]);
        }

        try {
            // ลองใช้ view หลัก
            return view('organization.companies.show', [
                'company' => $company,
                'debug' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('Error rendering view', ['error' => $e->getMessage()]);

            // ถ้าเกิดข้อผิดพลาดให้ใช้ view แบบพื้นฐานแทน
            return view('organization.companies.simple-show', [
                'company' => $company
            ]);
        }
    }

    /**
     * แสดงฟอร์มสำหรับแก้ไขข้อมูลบริษัท
     */
    public function edit(Company $company)
    {
        return view('organization.companies.edit', compact('company'));
    }

    /**
     * อัปเดตข้อมูลบริษัทในฐานข้อมูล
     */
    public function update(Request $request, Company $company)
    {
        // ปรับแต่งค่า is_active ในข้อมูลที่ส่งมา
        $data = $request->all();

        // กำหนดค่า is_active เป็น boolean ชัดเจน (true หรือ false)
        $data['is_active'] = $request->has('is_active') ? true : false;

        // ตรวจสอบข้อมูลที่ส่งมา
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:companies,code,' . $company->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'tax_id' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'registration_number' => 'nullable|string|max:50',
            'registration_date' => 'nullable|date',
            'registered_capital' => 'nullable|numeric',
            'business_type' => 'nullable|string|max:100',
            'company_type' => 'nullable|string|max:100',
            'branch_code' => 'nullable|string|max:50',
            'branch_name' => 'nullable|string|max:100',
            'branch_type' => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:255',
            'contact_position' => 'nullable|string|max:100',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'settings' => 'nullable|json',
            'metadata' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // อัปเดตข้อมูลทั่วไป
        $company->name = $data['name'];
        $company->code = $data['code'];
        $company->email = $data['email'];
        $company->phone = $data['phone'];
        $company->tax_id = $data['tax_id'];
        $company->address = $data['address'];
        $company->website = $data['website'] ?? null;
        $company->is_active = $data['is_active']; // ใช้ค่าที่แปลงแล้ว
        $company->status = $data['is_active'] ? 'active' : 'inactive';

        // อัปเดตข้อมูลการจดทะเบียน
        $company->registration_number = $data['registration_number'];
        $company->registration_date = $data['registration_date'];
        $company->registered_capital = $data['registered_capital'];
        $company->business_type = $data['business_type'];
        $company->company_type = $data['company_type'];

        // อัปเดตข้อมูลสาขา
        $company->branch_code = $data['branch_code'];
        $company->branch_name = $data['branch_name'];
        $company->branch_type = $data['branch_type'];

        // อัปเดตข้อมูลผู้ติดต่อ
        $company->contact_person = $data['contact_person'];
        $company->contact_position = $data['contact_position'];
        $company->contact_email = $data['contact_email'];
        $company->contact_phone = $data['contact_phone'];

        // อัปเดตข้อมูลการตั้งค่าและข้อมูลเพิ่มเติม
        $company->settings = $data['settings'];
        $company->metadata = $data['metadata'];

        // อัปโหลดโลโก้ใหม่ (ถ้ามี)
        if ($request->hasFile('logo')) {
            // ลบไฟล์เก่าถ้ามี
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }

            $path = $request->file('logo')->store('company_logos', 'public');
            $company->logo = $path;
        }

        $company->save();

        return redirect()->route('companies.show', $company)
            ->with('success', 'อัปเดตข้อมูลบริษัทสำเร็จ');
    }

    /**
     * ลบบริษัทออกจากฐานข้อมูล
     */
    public function destroy(Company $company)
    {
        // ตรวจสอบว่าสามารถลบได้หรือไม่
        // เช่น ถ้ามีการเชื่อมโยงกับข้อมูลอื่น อาจจะไม่อนุญาตให้ลบ

        try {
            // ลบไฟล์โลโก้ถ้ามี
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }

            $company->delete();
            return redirect()->route('companies.index')
                ->with('success', 'ลบบริษัทสำเร็จ');
        } catch (\Exception $e) {
            return redirect()->route('companies.index')
                ->with('error', 'ไม่สามารถลบบริษัทได้ เนื่องจากอาจมีข้อมูลที่เชื่อมโยงอยู่');
        }
    }
}
