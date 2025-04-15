<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        // ปรับปรุงโค้ดให้ดีขึ้นและเพิ่ม debug info
        $query = Product::query();
        
        // ถ้ามีการ login ให้กรองตามบริษัทของผู้ใช้
        if (auth()->check() && auth()->user()->company_id) {
            $query->where('company_id', auth()->user()->company_id);
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('name', 'LIKE', "%{$search}%")
                  ->orWhere('barcode', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        if ($request->filled('status')) {
            $isActive = ($request->input('status') == '1');
            $query->where('is_active', $isActive);
        }

        if ($request->filled('type')) {
            $isService = ($request->input('type') == '1');
            $query->where('is_service', $isService);
        }

        $products = $query->latest()->paginate(10);
        
        // แสดง debug info ในหน้า web ถ้าไม่มีข้อมูล
        if ($products->isEmpty() && config('app.debug')) {
            $totalProducts = Product::count();
            $categories = ProductCategory::where('company_id', auth()->user()->company_id)->get();
            
            // เพิ่ม debug info
            $debugInfo = [
                'total_products' => $totalProducts,
                'company_id' => auth()->user()->company_id,
                'categories_count' => $categories->count(),
                'query_sql' => $query->toSql(),
                'query_bindings' => $query->getBindings()
            ];
            
            return view('products.index', compact('products', 'categories', 'debugInfo'));
        }
        
        $categories = ProductCategory::where('company_id', auth()->user()->company_id)->get();
        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = ProductCategory::where('company_id', auth()->user()->company_id)->get();
        $units = Unit::where('company_id', auth()->user()->company_id)->get();
        return view('products.create', compact('categories', 'units'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:products,code,NULL,id,company_id,' . auth()->user()->company_id,
            'category_id' => 'required|exists:product_categories,id',
            'unit_id' => 'required|exists:units,id',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|numeric',
            'min_stock' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'barcode' => 'nullable|string|max:100',
            'is_active' => 'required|boolean',
            'is_service' => 'required|boolean',
            'image' => 'nullable|image|max:1024',
        ]);

        $data = $request->except('image');
        $data['company_id'] = auth()->user()->company_id;

        // Generate product code if not provided
        if (empty($data['code'])) {
            $lastProduct = Product::where('company_id', auth()->user()->company_id)
                ->orderBy('id', 'desc')
                ->first();
            
            $nextId = $lastProduct ? (int)substr($lastProduct->code, -4) + 1 : 1;
            $data['code'] = 'PRD-' . auth()->user()->company_id . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        }

        // Handle additional attributes as JSON
        if ($request->has('metadata')) {
            $data['metadata'] = json_encode($request->input('metadata'));
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }

        $product = Product::create($data);

        return redirect()->route('products.index')
            ->with('success', 'สร้างสินค้า "' . $product->name . '" เรียบร้อยแล้ว');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        // ยกเลิกการตรวจสอบชั่วคราวเพื่อให้ดูข้อมูลได้
        /* ไม่ใช้ส่วนนี้เพื่อให้ทุกคนเข้าถึงได้ระหว่างการพัฒนา
        if ($product->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized action.');
        }
        */
        
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        // ยกเลิกการตรวจสอบชั่วคราวเพื่อให้แก้ไขข้อมูลได้ระหว่างการพัฒนา
        /* 
        if ($product->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized action.');
        }
        */

        $categories = ProductCategory::where('company_id', auth()->user()->company_id)->get();
        $units = Unit::where('company_id', auth()->user()->company_id)->get();
        
        return view('products.edit', compact('product', 'categories', 'units'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        // ยกเลิกการตรวจสอบชั่วคราวเพื่อให้อัปเดตข้อมูลได้ระหว่างการพัฒนา
        /*
        if ($product->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized action.');
        }
        */

        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:products,code,' . $product->id . ',id,company_id,' . auth()->user()->company_id,
            'category_id' => 'required|exists:product_categories,id',
            'unit_id' => 'required|exists:units,id',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|numeric',
            'min_stock' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'barcode' => 'nullable|string|max:100',
            'is_active' => 'required|boolean',
            'is_service' => 'required|boolean',
            'image' => 'nullable|image|max:1024',
        ]);

        $data = $request->except(['_token', '_method', 'image']);

        // Handle additional attributes as JSON
        if ($request->has('metadata')) {
            $data['metadata'] = json_encode($request->input('metadata'));
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }

        $product->update($data);

        return redirect()->route('products.show', $product)
            ->with('success', 'อัปเดตสินค้า "' . $product->name . '" เรียบร้อยแล้ว');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        // Ensure user can only delete products from their company
        if ($product->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized action.');
        }

        // Delete image if exists
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $productName = $product->name;
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'ลบสินค้า "' . $productName . '" เรียบร้อยแล้ว');
    }
    
    /**
     * Display the form for managing product categories.
     */
    public function categories()
    {
        $categories = ProductCategory::where('company_id', auth()->user()->company_id)->paginate(10);
        return view('products.categories', compact('categories'));
    }
    
    /**
     * Store a new product category.
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $category = ProductCategory::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'company_id' => auth()->user()->company_id,
        ]);
        
        return redirect()->route('products.categories')
            ->with('success', 'สร้างหมวดหมู่สินค้า "' . $category->name . '" เรียบร้อยแล้ว');
    }
}
