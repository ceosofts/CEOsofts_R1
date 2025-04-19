<?php

namespace App\Http\Controllers;

use App\Models\Order;
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
            
            return response()->json([
                'success' => true,
                'message' => 'ข้อมูลถูกต้อง สามารถอัปเดตได้',
                'validated_data' => $validated,
                'original_request' => $request->all()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 422);
        }
    }
}
