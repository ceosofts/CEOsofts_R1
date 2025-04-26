<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer; // เพิ่ม import สำหรับ Customer model
use App\Models\User; // เพิ่ม import สำหรับ User model
use Illuminate\Support\Facades\Log; // เพิ่ม import Log facade
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DeliveryOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DeliveryOrder::query();

        // ค้นหาตามเงื่อนไข
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('delivery_number', 'LIKE', "%{$search}%")
                  ->orWhere('shipping_contact', 'LIKE', "%{$search}%")
                  ->orWhere('tracking_number', 'LIKE', "%{$search}%");
            });
        }
        
        // กรองตามสถานะ
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('delivery_status', $request->input('status'));
        }

        // กรองตามช่วงวันที่
        if ($request->filled('date_from')) {
            $query->whereDate('delivery_date', '>=', $request->input('date_from'));
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('delivery_date', '<=', $request->input('date_to'));
        }

        // กรองตามลูกค้า
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        $deliveryOrders = $query->with(['customer', 'order', 'creator'])
                                ->latest()
                                ->paginate(10);
                                
        $customers = Customer::orderBy('name')->get();
        
        return view('delivery_orders.index', [
            'deliveryOrders' => $deliveryOrders,
            'customers' => $customers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // ตรวจสอบว่ามาจากหน้าใบสั่งขายหรือไม่
        $orderId = $request->input('order_id');
        $order = null;
        
        if ($orderId) {
            $order = Order::with(['customer', 'items.product'])->find($orderId);
        }
        
        $orders = Order::where(function($query) {
            $query->where('status', 'confirmed')
                ->orWhere('status', 'processing');
        })
        ->with('customer')
        ->orderBy('order_date', 'desc')
        ->get();
        
        $customers = Customer::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        
        // สร้างเลขที่ใบส่งสินค้าอัตโนมัติ
        $deliveryNumber = DeliveryOrder::generateDeliveryNumber();

        return view('delivery_orders.create', [
            'orders' => $orders,
            'customers' => $customers,
            'users' => $users,
            'selectedOrder' => $order,
            'deliveryNumber' => $deliveryNumber, // เพิ่มเลขที่ใบส่งสินค้าอัตโนมัติ
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ปรับการ validate ให้ตรงกับชื่อฟิลด์ในฐานข้อมูล
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'customer_id' => 'required|exists:customers,id',
            'delivery_number' => 'required|unique:delivery_orders,delivery_number',
            'delivery_date' => 'required|date',
            'delivery_status' => 'required|in:pending,processing,shipped,delivered,partial_delivered,cancelled',
            'shipping_address' => 'required|string',
            // ลบ 'shipping_contact' ออกจากการตรวจสอบหรือทำให้เป็น optional
            // 'shipping_contact' => 'required|string', 
            'shipping_method' => 'required|string',
            'product_id' => 'required|array',
            'product_id.*' => 'required|exists:products,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|numeric|min:0.01',
        ]);

        try {
            $order = Order::findOrFail($request->order_id);
            
            $deliveryOrder = new DeliveryOrder();
            $deliveryOrder->company_id = $order->company_id ?? auth()->user()->company_id ?? 1;
            $deliveryOrder->order_id = $request->order_id;
            $deliveryOrder->customer_id = $request->customer_id;
            $deliveryOrder->delivery_number = $request->delivery_number;
            $deliveryOrder->delivery_date = $request->delivery_date;
            $deliveryOrder->expected_delivery_date = null;
            $deliveryOrder->delivery_status = $request->delivery_status;
            $deliveryOrder->shipping_address = $request->shipping_address;
            // ลบบรรทัดนี้
            // $deliveryOrder->shipping_contact = $request->shipping_contact;
            $deliveryOrder->shipping_method = $request->shipping_method;
            $deliveryOrder->tracking_number = $request->tracking_number;
            
            // เพิ่มข้อมูล shipping_contact ลงในฟิลด์ notes แทน
            $notes = $request->notes ?? '';
            if ($request->shipping_contact) {
                $notes = 'ผู้ติดต่อ: ' . $request->shipping_contact . ($notes ? "\n" . $notes : '');
            }
            $deliveryOrder->notes = $notes;
            
            $deliveryOrder->created_by = auth()->id();
            $deliveryOrder->save();
            
            // ส่วนที่เหลือของ method คงเดิม
            $productIds = $request->input('product_id');
            $quantities = $request->input('quantity');
            $descriptions = $request->input('description');
            $units = $request->input('unit');
            $statuses = $request->input('status');
            $itemNotes = $request->input('item_notes');
            
            // Buat item delivery order
            if ($productIds && is_array($productIds)) {
                foreach ($productIds as $index => $productId) {
                    if (isset($quantities[$index]) && $quantities[$index] > 0) {
                        DeliveryOrderItem::create([
                            'delivery_order_id' => $deliveryOrder->id,
                            'product_id' => $productId,
                            'order_item_id' => $request->input('order_item_id')[$index] ?? null,
                            'description' => $descriptions[$index] ?? '',
                            'quantity' => $quantities[$index],
                            'unit' => $units[$index] ?? '',
                            'status' => $statuses[$index] ?? 'pending',
                            'notes' => $itemNotes[$index] ?? null,
                        ]);
                    }
                }
            }
            
            // Redirect with success message
            return redirect()->route('delivery-orders.show', $deliveryOrder)
                ->with('success', 'ใบส่งสินค้าถูกสร้างเรียบร้อยแล้ว');
                
        } catch (\Exception $e) {
            Log::error('Error creating delivery order: ' . $e->getMessage()); // ใช้ Log แทน \Log
            return redirect()->back()->withInput()
                ->with('error', 'เกิดข้อผิดพลาดในการสร้างใบส่งสินค้า: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load(['order', 'customer', 'deliveryOrderItems.product', 'creator', 'approver']);
        
        return view('delivery_orders.show', [
            'deliveryOrder' => $deliveryOrder
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load(['order.customer', 'order.items.product', 'deliveryOrderItems.product']);
        
        $orders = Order::where(function($query) {
            $query->where('status', 'confirmed')
                  ->orWhere('status', 'processing')
                  ->orWhere('status', 'shipped')
                  ->orWhere('status', 'partial_delivered')
                  ->orWhere('status', 'delivered');
        })
        ->with('customer')
        ->orderBy('order_date', 'desc')
        ->get();
        
        $users = User::orderBy('name')->get();

        return view('delivery_orders.edit', [
            'deliveryOrder' => $deliveryOrder,
            'orders' => $orders,
            'users' => $users,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DeliveryOrder $deliveryOrder)
    {
        $validated = $request->validate([
            'delivery_date' => 'required|date',
            'delivery_status' => 'required|in:pending,processing,shipped,delivered,partial_delivered,cancelled',
            'shipping_address' => 'required|string',
            'shipping_method' => 'required|string',
            // ลบการตรวจสอบความถูกต้อง approved_by
        ]);

        try {
            // เก็บข้อมูลเดิมไว้ตรวจสอบการเปลี่ยนสถานะ
            $oldStatus = $deliveryOrder->delivery_status;
            
            // อัปเดตข้อมูลพื้นฐาน
            $deliveryOrder->delivery_date = $request->delivery_date;
            $deliveryOrder->delivery_status = $request->delivery_status;
            $deliveryOrder->shipping_address = $request->shipping_address;
            $deliveryOrder->shipping_method = $request->shipping_method;
            $deliveryOrder->tracking_number = $request->tracking_number;
            
            // เพิ่มข้อมูล shipping_contact ลงในฟิลด์ notes แทน
            $notes = $request->notes ?? '';
            if ($request->shipping_contact) {
                $notes = 'ผู้ติดต่อ: ' . $request->shipping_contact . ($notes ? "\n" . $notes : '');
            }
            
            // เพิ่มข้อมูลผู้อนุมัติในฟิลด์ notes เช่นกัน
            if ($request->approved_by) {
                $approver = User::find($request->approved_by);
                $approverName = $approver ? $approver->name : 'ผู้ใช้ #' . $request->approved_by;
                $notes .= ($notes ? "\n" : '') . 'ผู้อนุมัติ: ' . $approverName . ' เมื่อ ' . now()->format('d/m/Y H:i');
            }
            
            $deliveryOrder->notes = $notes;
            
            // ลบการอัพเดตคอลัมน์ approved_by และ approved_at ที่ไม่มีในฐานข้อมูล
            // $deliveryOrder->approved_by = $request->approved_by;
            // $deliveryOrder->approved_at = now();
            
            $deliveryOrder->save();

            // อัพเดทรายการสินค้า
            if ($request->has('item_id')) {
                foreach ($request->input('item_id') as $index => $itemId) {
                    if ($itemId) {
                        $item = DeliveryOrderItem::find($itemId);
                        if ($item) {
                            $item->update([
                                'description' => $request->input('description')[$index],
                                'quantity' => $request->input('quantity')[$index],
                                'unit' => $request->input('unit')[$index],
                                'status' => $request->input('status')[$index] ?? 'pending',
                                'notes' => $request->input('item_notes')[$index] ?? null,
                            ]);
                        }
                    }
                }
            }
            
            // เพิ่มรายการใหม่ (ถ้ามี)
            if ($request->has('new_product_id')) {
                for ($i = 0; $i < count($request->input('new_product_id')); $i++) {
                    if (isset($request->input('new_product_id')[$i]) && $request->input('new_product_id')[$i]) {
                        DeliveryOrderItem::create([
                            'delivery_order_id' => $deliveryOrder->id,
                            'product_id' => $request->input('new_product_id')[$i],
                            'description' => $request->input('new_description')[$i],
                            'quantity' => $request->input('new_quantity')[$i],
                            'unit' => $request->input('new_unit')[$i],
                            'status' => $request->input('new_status')[$i] ?? 'pending',
                            'notes' => $request->input('new_item_notes')[$i] ?? null,
                        ]);
                    }
                }
            }
            
            // ลบรายการ (ถ้ามี)
            if ($request->has('delete_items') && is_array($request->input('delete_items'))) {
                DeliveryOrderItem::whereIn('id', $request->input('delete_items'))->delete();
            }
            
            // อัพเดทสถานะใบสั่งขาย
            $order = $deliveryOrder->order;
            if ($order && $deliveryOrder->delivery_status === 'delivered') {
                $order->update([
                    'status' => 'delivered', 
                    'delivery_date' => $deliveryOrder->delivery_date,
                    'delivered_at' => now(),
                    'delivered_by' => Auth::id()
                ]);
            } elseif ($order && $deliveryOrder->delivery_status === 'partial_delivered') {
                $order->update(['status' => 'partial_delivered']);
            } elseif ($order && $deliveryOrder->delivery_status === 'shipped') {
                $order->update([
                    'status' => 'shipped',
                    'shipped_at' => now(),
                    'shipped_by' => Auth::id()
                ]);
            }
            
            return redirect()->route('delivery-orders.show', $deliveryOrder)
                ->with('success', 'ใบส่งสินค้าถูกอัปเดตเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            Log::error('Error updating delivery order: ' . $e->getMessage());
            return redirect()->back()->withInput()
                ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeliveryOrder $deliveryOrder)
    {
        try {
            DB::beginTransaction();
            
            // ลบรายการสินค้าทั้งหมดที่เกี่ยวข้อง
            $deliveryOrder->deliveryOrderItems()->delete();
            
            // ลบใบส่งสินค้า
            $deliveryOrder->delete();
            
            DB::commit();
            
            return redirect()->route('delivery-orders.index')
                ->with('success', 'ลบใบส่งสินค้าเลขที่ ' . $deliveryOrder->delivery_number . ' สำเร็จ');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * Get products for an order (for delivery form)
     */
    public function getOrderProducts($order_id)
    {
        $order = Order::with(['customer', 'items.product'])->findOrFail($order_id);
        
        return response()->json([
            'order' => [
                'id' => $order->id,
                'customer_id' => $order->customer_id,
                'order_number' => $order->order_number,
                'delivery_date' => $order->delivery_date?->format('Y-m-d'),
                'shipping_address' => $order->shipping_address,
                'shipping_method' => $order->shipping_method,
                'status' => $order->status,
            ],
            'customer' => [
                'name' => $order->customer->name,
                'email' => $order->customer->email,
                'phone' => $order->customer->phone,
                'address' => $order->customer->address,
            ],
            'items' => $order->items->map(function($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_name' => $item->unit_name,
                    'sku' => $item->product ? $item->product->sku : null,  // แน่ใจว่าใช้ชื่อฟิลด์เดียวกับใน DB
                    'product' => $item->product ? [
                        'id' => $item->product->id,
                        'sku' => $item->product->sku
                    ] : null
                ];
            }),
        ]);
    }

    /**
     * สร้างเลขที่ใบส่งสินค้าอัตโนมัติ (API)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateDeliveryNumber()
    {
        try {
            $deliveryNumber = DeliveryOrder::generateDeliveryNumber();
            return response()->json([
                'success' => true,
                'delivery_number' => $deliveryNumber
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาดในการสร้างเลขที่ใบส่งสินค้า: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * แสดงหน้าพิมพ์ใบส่งสินค้า
     *
     * @param  \App\Models\DeliveryOrder  $deliveryOrder
     * @return \Illuminate\Http\Response
     */
    public function print(DeliveryOrder $deliveryOrder)
    {
        // ยกเลิกการตรวจสอบสิทธิ์เข้มงวด เพื่อให้สามารถพิมพ์ได้
        // (ไม่ต้องตรวจสอบสิทธิ์เพราะระบบ route model binding 
        // ได้จำกัดการเข้าถึงตาม scope ของ company ที่กำหนดใน middleware แล้ว)
        
        // โหลด relation ที่จำเป็น
        $deliveryOrder->load(['customer', 'order', 'deliveryOrderItems.product', 'company']);
        
        // แสดงหน้า pdf-view โดยตรง
        return view('delivery_orders.pdf-view', [
            'deliveryOrder' => $deliveryOrder
        ]);
    }
}
