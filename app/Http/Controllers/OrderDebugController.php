<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class OrderDebugController extends Controller
{
    public function testUpdate(Request $request, Order $order)
    {
        try {
            // จำลองการตรวจสอบเงื่อนไขการอัปเดต แต่ไม่ได้อัปเดตจริง
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'order_number' => 'required|unique:orders,order_number,' . $order->id,
                // ... ลดรายการ validation rule ลงเพื่อทดสอบ
            ]);
            
            // เพิ่มข้อมูลสินค้าพร้อมรหัสสินค้าและหน่วย
            $productsData = [];
            if ($order->items) {
                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    $unit = null;
                    
                    // ดึงข้อมูลหน่วยจากหลายแหล่ง (item, product, quotation)
                    if ($item->unit_id) {
                        $unit = Unit::find($item->unit_id);
                    } elseif ($product && $product->unit_id) {
                        $unit = Unit::find($product->unit_id);
                    }
                    
                    $productsData[] = [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $product ? $product->name : 'Unknown',
                        'product_code' => $product ? $product->code ?? $product->sku ?? '' : '',
                        'quantity' => $item->quantity,
                        'unit_id' => $item->unit_id ?? ($product ? $product->unit_id : null),
                        'unit_name' => $unit ? $unit->name : '',
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'ข้อมูลถูกต้อง สามารถอัปเดตได้',
                'validated_data' => $validated,
                'original_request' => $request->all(),
                'products_data' => $productsData // แสดงข้อมูลสินค้าพร้อมรหัสและหน่วย
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 422);
        }
    }
    
    /**
     * แสดงรายละเอียดสินค้าในใบสั่งขายพร้อมรหัสสินค้าและหน่วย
     */
    public function showOrderItems(Order $order)
    {
        $order->load(['items.product.unit', 'customer', 'quotation.items']);
        
        $items = $order->items->map(function ($item) {
            // ดึงข้อมูลหน่วยจากหลายแหล่ง
            $unitName = '';
            
            if ($item->unit_id && $unit = \App\Models\Unit::find($item->unit_id)) {
                $unitName = $unit->name;
            } elseif ($item->product && $item->product->unit) {
                $unitName = $item->product->unit->name;
            }
            
            // ดึงข้อมูลจากใบเสนอราคา (ถ้ามี)
            $quotationItemData = null;
            if ($order->quotation) {
                foreach ($order->quotation->items as $quotationItem) {
                    if ($quotationItem->product_id == $item->product_id) {
                        $quotationItemData = [
                            'quantity' => $quotationItem->quantity,
                            'unit_price' => $quotationItem->unit_price,
                            'unit' => $quotationItem->unit ? $quotationItem->unit->name : ''
                        ];
                        break;
                    }
                }
            }
            
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_code' => $item->product ? $item->product->code ?? $item->product->sku ?? '' : '',
                'product_name' => $item->product ? $item->product->name : 'Unknown',
                'description' => $item->description ?? '',
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'unit_id' => $item->unit_id ?? ($item->product ? $item->product->unit_id : null),
                'unit_name' => $unitName,
                'total' => $item->quantity * $item->unit_price,
                'from_quotation' => $quotationItemData,
            ];
        });
        
        return response()->json([
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer ? $order->customer->name : 'Unknown',
                'quotation_number' => $order->quotation ? $order->quotation->quotation_number : null,
            ],
            'items' => $items
        ]);
    }
}
