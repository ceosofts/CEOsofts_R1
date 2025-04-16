<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use App\Models\Company;
use App\Models\Product; // เพิ่มการ import คลาส Product
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // <-- เพิ่ม import สำหรับ Log Facade
use Illuminate\Support\Str; // เพิ่ม import สำหรับ Str
use Illuminate\Validation\Rule;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the product categories.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $userCompanyId = $user->current_company_id ?? 1;

        // ดึงค่า company_id จาก request หรือใช้ค่า 'all' เป็นค่าเริ่มต้น
        $selectedCompanyId = $request->input('company', 'all');
        
        // เก็บ session ถ้ามีการเลือกบริษัท
        if ($selectedCompanyId != 'all') {
            $request->session()->put('selected_company_id', $selectedCompanyId);
        }
        
        // สร้าง query และยกเลิก global scope ถ้าต้องการดูทั้งหมด
        if ($selectedCompanyId == 'all') {
            $query = ProductCategory::allCompanies();
        } else {
            $query = ProductCategory::query()->where('company_id', $selectedCompanyId);
        }
        
        $companyId = $selectedCompanyId;
        $debugInfo = [];
        
        // เก็บข้อมูล Debug
        $debugInfo['user_id'] = $user->id;
        $debugInfo['user_email'] = $user->email;
        $debugInfo['current_company_id'] = $userCompanyId;
        $debugInfo['selected_company_id'] = $companyId;
        
        // ค้นหาและกรอง
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // กรองตามสถานะ
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        // จัดเรียง - เปลี่ยนค่าเริ่มต้นจาก 'code' เป็น 'id'
        $sortField = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'asc');
        
        // แปลงชื่อฟิลด์จากหน้าเว็บเป็นชื่อในฐานข้อมูล
        if ($sortField == 'category_code') $sortField = 'code';
        if ($sortField == 'category_name') $sortField = 'name';
        
        // เพิ่มเงื่อนไขการเรียงลำดับเพิ่มเติม
        $query->orderBy($sortField, $sortDirection);
        
        // ถ้าไม่ได้เรียงตาม company_id หรือ id ให้เรียงตาม company_id เป็นลำดับรอง
        if ($sortField != 'company_id' && $sortField != 'id') {
            $query->orderBy('company_id', 'asc');
        }
        
        // บันทึก SQL และ bindings สำหรับ Debug
        $debugInfo['query_sql'] = $query->toSql();
        $debugInfo['query_bindings'] = $query->getBindings();
        
        // ข้อมูลสถิติ
        $debugInfo['total_categories'] = ProductCategory::count();
        
        // จำนวนหมวดหมู่แยกตามบริษัท
        $categoryCounts = [];
        $companies = Company::all();
        foreach ($companies as $company) {
            $count = ProductCategory::where('company_id', $company->id)->count();
            $categoryCounts[$company->id] = $count;
        }
        $debugInfo['category_counts'] = $categoryCounts;
        
        $categories = $query->paginate(15); // เพิ่มจำนวนรายการต่อหน้า
        
        // เพิ่มการเลือกบริษัท
        $companies = Company::select('id', 'company_name')->get(); 
        
        return view('products.categories.index', compact(
            'categories', 'debugInfo', 'companyId', 'companies'
        ));
    }

    /**
     * Show the form for creating a new product category.
     */
    public function create()
    {
        $companies = Company::all();
        return view('products.categories.create', compact('companies'));
    }

    /**
     * Store a newly created product category in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'category_code' => [
                'required', 
                'string', 
                'max:20', 
                Rule::unique('product_categories', 'code')->where(function ($query) use ($request) {
                    return $query->where('company_id', $request->company_id);
                })
            ],
            'category_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'is_active' => 'sometimes|boolean',
        ]);

        // กำหนดค่า is_active - แก้ไข created_by เหมือนกัน
        $data = [
            'code' => $validatedData['category_code'],
            'name' => $validatedData['category_name'],
            'description' => $validatedData['description'],
            'company_id' => $validatedData['company_id'],
            'is_active' => $request->has('is_active') ? 1 : 0,
            // 'created_by' => Auth::id(), // ลบฟิลด์นี้ด้วย ถ้าไม่มีใน schema
            // 'updated_by' => Auth::id(), // ลบฟิลด์นี้
            'slug' => \Str::slug($validatedData['category_name']),
            'level' => 0,
        ];

        $category = ProductCategory::create($data);

        return redirect()->route('product-categories.index')
            ->with('success', 'หมวดหมู่สินค้าถูกสร้างเรียบร้อยแล้ว');
    }

    /**
     * Display the specified product category.
     */
    public function show(ProductCategory $productCategory)
    {
        // หา ID ของหมวดหมู่นี้และหมวดหมู่ย่อยทั้งหมด
        $categoryIds = [$productCategory->id];
        $this->getAllChildCategoryIds($productCategory, $categoryIds);
        
        // ดึงข้อมูลสินค้าจากทุกบริษัทที่อยู่ในหมวดหมู่นี้และหมวดหมู่ย่อย
        $products = Product::whereIn('product_category_id', $categoryIds)
            ->with('company') // eager load ข้อมูลบริษัท
            ->orderBy('company_id') // เรียงตามบริษัท
            ->orderBy('name') // แล้วเรียงตามชื่อ
            ->get();
        
        return view('products.categories.show', compact('productCategory', 'products'));
    }

    /**
     * รวบรวม ID ของหมวดหมู่ย่อยทั้งหมดในโครงสร้างแบบต้นไม้
     */
    private function getAllChildCategoryIds(ProductCategory $category, &$categoryIds)
    {
        foreach ($category->children as $child) {
            $categoryIds[] = $child->id;
            $this->getAllChildCategoryIds($child, $categoryIds);
        }
    }

    /**
     * Show the form for editing the specified product category.
     */
    public function edit(ProductCategory $productCategory)
    {
        // โหลดความสัมพันธ์ที่จำเป็นให้ครบถ้วน
        $productCategory->load(['company', 'parent']);
        
        // ดึงข้อมูลบริษัททั้งหมด - ไม่กรองฟิลด์
        $companies = Company::all();
        
        // Debug: ดูโครงสร้างของข้อมูลบริษัท
        Log::info('Edit ProductCategory - Companies Structure:', [
            'count' => $companies->count(),
            'first_company' => $companies->first() ? $companies->first()->toArray() : null,
            'available_fields' => $companies->first() ? array_keys($companies->first()->toArray()) : []
        ]);
        
        // ดึงหมวดหมู่ที่เป็นไปได้สำหรับ parent
        $possibleParents = ProductCategory::where('id', '!=', $productCategory->id)
            ->where('company_id', $productCategory->company_id)
            ->orderBy('name')
            ->get();
        
        return view('products.categories.edit', compact('productCategory', 'companies', 'possibleParents'));
    }

    /**
     * Update the specified product category in storage.
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        $validatedData = $request->validate([
            'category_code' => [
                'required', 
                'string', 
                'max:20', 
                Rule::unique('product_categories', 'code')->ignore($productCategory->id)->where(function ($query) use ($request) {
                    return $query->where('company_id', $request->company_id);
                })
            ],
            'category_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'company_id' => 'required|exists:companies,id',
            'is_active' => 'sometimes|boolean',
        ]);

        // อัพเดทข้อมูล - ลบฟิลด์ updated_by ที่ไม่มีใน schema
        $data = [
            'code' => $validatedData['category_code'],
            'name' => $validatedData['category_name'],
            'description' => $validatedData['description'],
            'company_id' => $validatedData['company_id'],
            'is_active' => $request->has('is_active') ? 1 : 0,
            // 'updated_by' => Auth::id(), // ลบฟิลด์นี้ออกเนื่องจากไม่มีใน database
            'slug' => Str::slug($validatedData['category_name']),
        ];

        // เพิ่ม parent_id ถ้ามีการส่งมา
        if ($request->has('parent_id')) {
            $data['parent_id'] = $request->parent_id ?: null; // ถ้าเป็นค่าว่างให้เป็น null
        }

        $productCategory->update($data);

        return redirect()->route('product-categories.show', $productCategory->id)
            ->with('success', 'ปรับปรุงข้อมูลหมวดหมู่สินค้าเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified product category from storage.
     */
    public function destroy(ProductCategory $productCategory)
    {
        // ตรวจสอบว่ามีสินค้าในหมวดหมู่นี้หรือไม่
        if ($productCategory->products()->exists()) {
            return redirect()->route('product-categories.index')
                ->with('error', 'ไม่สามารถลบหมวดหมู่สินค้าได้ เนื่องจากมีสินค้าอยู่ในหมวดหมู่นี้');
        }

        $productCategory->delete();

        return redirect()->route('product-categories.index')
            ->with('success', 'ลบหมวดหมู่สินค้าเรียบร้อยแล้ว');
    }
}
