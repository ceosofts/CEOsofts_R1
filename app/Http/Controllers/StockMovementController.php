<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockMovementController extends Controller
{
    /**
     * แสดงรายการเคลื่อนไหวของสินค้า
     */
    public function index()
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        $stockMovements = StockMovement::where('company_id', $companyId)
            ->with(['product', 'unit'])
            ->latest()
            ->paginate(10);

        return view('stock-movements.index', compact('stockMovements'));
    }

    /**
     * แสดงหน้าสำหรับเพิ่มรายการเคลื่อนไหวสินค้าใหม่
     */
    public function create()
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        $products = Product::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $units = Unit::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $movementTypes = ['in' => 'รับเข้า', 'out' => 'เบิกออก', 'adjust' => 'ปรับยอด'];

        return view('stock-movements.create', compact('products', 'units', 'movementTypes'));
    }

    /**
     * บันทึกรายการเคลื่อนไหวสินค้าใหม่
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'movement_type' => 'required|in:in,out,adjust',
            'quantity' => 'required|numeric|min:0.01',
            'unit_id' => 'required|exists:units,id',
            'reference_type' => 'nullable|string|max:50',
            'reference_id' => 'nullable|string|max:50',
            'note' => 'nullable|string|max:255',
            'movement_date' => 'required|date',
        ]);

        // เตรียมข้อมูลสำหรับบันทึก
        $stockData = array_merge($validated, [
            'company_id' => $companyId,
            'created_by' => $user->id
        ]);

        // ถ้าเป็นการเบิกออกให้เปลี่ยนค่า quantity เป็นลบ
        if ($validated['movement_type'] == 'out') {
            $stockData['quantity'] = -abs($validated['quantity']);
        }

        // บันทึกข้อมูล
        $stockMovement = StockMovement::create($stockData);

        // อัปเดตจำนวนสินค้าคงเหลือในตาราง products
        $product = Product::find($validated['product_id']);
        if ($product) {
            $currentStock = $product->stock_quantity ?? 0;
            
            if ($validated['movement_type'] == 'in') {
                $product->stock_quantity = $currentStock + $validated['quantity'];
            } elseif ($validated['movement_type'] == 'out') {
                $product->stock_quantity = $currentStock - $validated['quantity'];
            } else { // adjust
                $product->stock_quantity = $validated['quantity'];
            }
            
            $product->save();
        }

        return redirect()->route('stock-movements.index')
            ->with('success', 'บันทึกการเคลื่อนไหวสินค้าเรียบร้อยแล้ว');
    }

    /**
     * แสดงรายละเอียดรายการเคลื่อนไหวสินค้า
     */
    public function show(StockMovement $stockMovement)
    {
        $user = Auth::user();

        // ตรวจสอบสิทธิ์การเข้าถึง
        if ($stockMovement->company_id !== $user->company_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('stock-movements.show', compact('stockMovement'));
    }

    /**
     * แสดงหน้าแก้ไขรายการเคลื่อนไหวสินค้า
     */
    public function edit(StockMovement $stockMovement)
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        // ตรวจสอบสิทธิ์การเข้าถึง
        if ($stockMovement->company_id !== $companyId) {
            abort(403, 'Unauthorized action.');
        }

        $products = Product::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $units = Unit::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $movementTypes = ['in' => 'รับเข้า', 'out' => 'เบิกออก', 'adjust' => 'ปรับยอด'];

        return view('stock-movements.edit', compact('stockMovement', 'products', 'units', 'movementTypes'));
    }

    /**
     * อัปเดตรายการเคลื่อนไหวสินค้า
     */
    public function update(Request $request, StockMovement $stockMovement)
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        // ตรวจสอบสิทธิ์การเข้าถึง
        if ($stockMovement->company_id !== $companyId) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'movement_type' => 'required|in:in,out,adjust',
            'quantity' => 'required|numeric|min:0.01',
            'unit_id' => 'required|exists:units,id',
            'reference_type' => 'nullable|string|max:50',
            'reference_id' => 'nullable|string|max:50',
            'note' => 'nullable|string|max:255',
            'movement_date' => 'required|date',
        ]);

        // เตรียมข้อมูลสำหรับบันทึก
        $stockData = array_merge($validated, [
            'updated_by' => $user->id
        ]);

        // ถ้าเป็นการเบิกออกให้เปลี่ยนค่า quantity เป็นลบ
        if ($validated['movement_type'] == 'out') {
            $stockData['quantity'] = -abs($validated['quantity']);
        }

        // อัปเดตข้อมูล
        $oldProductId = $stockMovement->product_id;
        $oldQuantity = $stockMovement->quantity;
        $stockMovement->update($stockData);

        // อัปเดตจำนวนสินค้าคงเหลือในตาราง products
        // ลบผลกระทบจากรายการเดิม
        if ($oldProductId) {
            $oldProduct = Product::find($oldProductId);
            if ($oldProduct) {
                $oldProduct->stock_quantity -= $oldQuantity;
                $oldProduct->save();
            }
        }
        
        // เพิ่มผลกระทบจากรายการใหม่
        $product = Product::find($validated['product_id']);
        if ($product) {
            $quantity = $validated['movement_type'] == 'out' ? -abs($validated['quantity']) : $validated['quantity'];
            $product->stock_quantity += $quantity;
            $product->save();
        }

        return redirect()->route('stock-movements.index')
            ->with('success', 'อัปเดตการเคลื่อนไหวสินค้าเรียบร้อยแล้ว');
    }

    /**
     * ลบรายการเคลื่อนไหวสินค้า
     */
    public function destroy(StockMovement $stockMovement)
    {
        $user = Auth::user();
        
        // ตรวจสอบสิทธิ์การเข้าถึง
        if ($stockMovement->company_id !== $user->company_id) {
            abort(403, 'Unauthorized action.');
        }

        // อัปเดตจำนวนสินค้าคงเหลือในตาราง products (ลบผลกระทบ)
        $product = Product::find($stockMovement->product_id);
        if ($product) {
            $product->stock_quantity -= $stockMovement->quantity;
            $product->save();
        }

        // ลบรายการ
        $stockMovement->delete();

        return redirect()->route('stock-movements.index')
            ->with('success', 'ลบรายการเคลื่อนไหวสินค้าเรียบร้อยแล้ว');
    }
}
