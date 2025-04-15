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
        $query = Product::query();
        
        // Apply search filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }
        
        // Apply category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }
        
        // Apply type filter
        if ($request->filled('type')) {
            if ($request->input('type') === 'product') {
                $query->where('is_service', false);
            } else if ($request->input('type') === 'service') {
                $query->where('is_service', true);
            }
        }
        
        $products = $query->with(['category', 'unitRelation'])->paginate(10);
        
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = ProductCategory::where('is_active', true)->get();
        $units = Unit::where('is_active', true)->get();
        return view('products.create', compact('categories', 'units'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:products',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:product_categories,id',
            'unit_id' => 'nullable|exists:units,id',
            'unit' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
            'is_service' => 'boolean',
            'is_inventory_tracked' => 'boolean',
            'image' => 'nullable|image|max:2048',
            'barcode' => 'nullable|string|max:50',
            'sku' => 'nullable|string|max:100',
            'weight' => 'nullable|numeric|min:0',
            'weight_unit' => 'nullable|string|max:10',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'dimension_unit' => 'nullable|string|max:10',
            'location' => 'nullable|string|max:100',
            'condition' => 'nullable|string|max:50',
            'warranty' => 'nullable|string|max:255',
        ]);
        
        // Generate UUID
        $validated['uuid'] = (string) Str::uuid();
        
        // Set company_id from session
        $validated['company_id'] = session('current_company_id');
        
        // Set initial stock quantity
        if (!$validated['is_service']) {
            $validated['stock_quantity'] = $request->input('stock_quantity', 0);
            $validated['current_stock'] = $validated['stock_quantity'];
            $validated['inventory_status'] = $validated['stock_quantity'] > 0 ? 'in_stock' : 'out_of_stock';
        }
        
        // Generate code if not provided
        if (empty($validated['code'])) {
            $validated['code'] = 'P' . str_pad(Product::max('id') + 1, 5, '0', STR_PAD_LEFT);
        }
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }
        
        // Prepare metadata
        $metadata = [
            'specifications' => $request->input('specifications', []),
        ];
        
        if ($request->filled('brand')) {
            $metadata['brand'] = $request->input('brand');
        }
        
        $validated['metadata'] = json_encode($metadata);
        
        // Create product
        $product = Product::create($validated);
        
        return redirect()->route('products.show', $product)
            ->with('success', 'สร้างสินค้าเรียบร้อยแล้ว');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'unitRelation']);
        
        // Get stock movements
        $stockMovements = [];
        if ($product->is_inventory_tracked) {
            $stockMovements = $product->stockMovements()
                ->orderBy('date', 'desc')
                ->limit(10)
                ->get();
        }
        
        return view('products.show', compact('product', 'stockMovements'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = ProductCategory::where('is_active', true)->get();
        $units = Unit::where('is_active', true)->get();
        
        // Parse metadata
        if (is_string($product->metadata)) {
            $product->metadata = json_decode($product->metadata, true);
        }
        
        return view('products.edit', compact('product', 'categories', 'units'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:products,code,' . $product->id,
            'description' => 'nullable|string',
            'category_id' => 'required|exists:product_categories,id',
            'unit_id' => 'nullable|exists:units,id',
            'unit' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0',
            'is_service' => 'boolean',
            'is_inventory_tracked' => 'boolean',
            'image' => 'nullable|image|max:2048',
            'barcode' => 'nullable|string|max:50',
            'sku' => 'nullable|string|max:100',
            'weight' => 'nullable|numeric|min:0',
            'weight_unit' => 'nullable|string|max:10',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'dimension_unit' => 'nullable|string|max:10',
            'location' => 'nullable|string|max:100',
            'condition' => 'nullable|string|max:50',
            'warranty' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_bestseller' => 'boolean',
            'is_new' => 'boolean',
        ]);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = $path;
        }
        
        // Prepare metadata
        $metadata = is_string($product->metadata) 
            ? json_decode($product->metadata, true) ?? []
            : ($product->metadata ?? []);
            
        if ($request->filled('specifications')) {
            $metadata['specifications'] = $request->input('specifications');
        }
        
        if ($request->filled('brand')) {
            $metadata['brand'] = $request->input('brand');
        }
        
        $validated['metadata'] = json_encode($metadata);
        
        // Update product
        $product->update($validated);
        
        return redirect()->route('products.show', $product)
            ->with('success', 'อัพเดตสินค้าเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        // Delete image if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();
        
        return redirect()->route('products.index')
            ->with('success', 'ลบสินค้าเรียบร้อยแล้ว');
    }
    
    /**
     * Display product stock history.
     */
    public function stockHistory(Product $product)
    {
        if (!$product->is_inventory_tracked || $product->is_service) {
            return redirect()->route('products.show', $product)
                ->with('warning', 'สินค้านี้ไม่มีการติดตามสต็อก');
        }
        
        $stockMovements = $product->stockMovements()
            ->orderBy('date', 'desc')
            ->paginate(15);
            
        return view('products.stock-history', compact('product', 'stockMovements'));
    }
}
