<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

        return view('delivery_orders.create', [
            'orders' => $orders,
            'customers' => $customers,
            'users' => $users,
            'selectedOrder' => $order,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'delivery_number' => 'required|string|unique:delivery_orders,delivery_number',
            'delivery_date' => 'required|date',
            'delivery_status' => 'required|string',
            'shipping_address' => 'required|string',
            'shipping_contact' => 'required|string',
            'shipping_method' => 'nullable|string',
            'tracking_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'product_id' => 'array',
            'description' => 'array',
            'quantity' => 'array',
            'unit' => 'array',
            'status' => 'array',
            'item_notes' => 'array',
        ]);

        try {
            DB::beginTransaction();
            
            $order = Order::findOrFail($request->input('order_id'));
            
            $deliveryOrder = DeliveryOrder::create([
                'company_id' => $order->company_id,
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'delivery_number' => $request->input('delivery_number'),
                'delivery_date' => $request->input('delivery_date'),
                'delivery_status' => $request->input('delivery_status'),
                'shipping_address' => $request->input('shipping_address'),
                'shipping_contact' => $request->input('shipping_contact'),
                'shipping_method' => $request->input('shipping_method'),
                'tracking_number' => $request->input('tracking_number'),
                'notes' => $request->input('notes'),
                'created_by' => Auth::id(),
                'approved_by' => $request->input('approved_by'),
                'approved_at' => $request->filled('approved_by') ? now() : null,
                'metadata' => [
                    'source' => 'web_form',
                    'timestamp' => now()->timestamp
                ],
            ]);
            
            // สร้างรายการสินค้า
            if ($request->has('product_id')) {
                for ($i = 0; $i < count($request->input('product_id')); $i++) {
                    if ($request->input('product_id')[$i]) {
                        DeliveryOrderItem::create([
                            'delivery_order_id' => $deliveryOrder->id,
                            'order_item_id' => $request->input('order_item_id')[$i] ?? null,
                            'product_id' => $request->input('product_id')[$i],
                            'description' => $request->input('description')[$i],
                            'quantity' => $request->input('quantity')[$i],
                            'unit' => $request->input('unit')[$i],
                            'status' => $request->input('status')[$i] ?? 'pending',
                            'notes' => $request->input('item_notes')[$i] ?? null,
                            'metadata' => [
                                'source' => 'web_form',
                                'timestamp' => now()->timestamp
                            ],
                        ]);
                    }
                }
            }
            
            // อัพเดทสถานะใบสั่งขาย
            if ($deliveryOrder->delivery_status === 'delivered') {
                $order->update([
                    'status' => 'delivered', 
                    'delivery_date' => $deliveryOrder->delivery_date,
                    'delivered_at' => now(),
                    'delivered_by' => Auth::id()
                ]);
            } elseif ($deliveryOrder->delivery_status === 'partial_delivered') {
                $order->update(['status' => 'partial_delivered']);
            } else if ($deliveryOrder->delivery_status === 'shipped') {
                $order->update([
                    'status' => 'shipped',
                    'shipped_at' => now(),
                    'shipped_by' => Auth::id()
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('delivery-orders.show', $deliveryOrder)
                ->with('success', 'สร้างใบส่งสินค้าเลขที่ ' . $deliveryOrder->delivery_number . ' สำเร็จ');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
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
            'delivery_status' => 'required|string',
            'shipping_address' => 'required|string',
            'shipping_contact' => 'required|string',
            'shipping_method' => 'nullable|string',
            'tracking_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'product_id' => 'array',
            'description' => 'array',
            'quantity' => 'array',
            'unit' => 'array',
            'status' => 'array',
            'item_notes' => 'array',
        ]);

        try {
            DB::beginTransaction();
            
            $deliveryOrder->update([
                'delivery_date' => $request->input('delivery_date'),
                'delivery_status' => $request->input('delivery_status'),
                'shipping_address' => $request->input('shipping_address'),
                'shipping_contact' => $request->input('shipping_contact'),
                'shipping_method' => $request->input('shipping_method'),
                'tracking_number' => $request->input('tracking_number'),
                'notes' => $request->input('notes'),
                'approved_by' => $request->input('approved_by'),
                'approved_at' => $request->filled('approved_by') && !$deliveryOrder->approved_at ? now() : $deliveryOrder->approved_at,
            ]);
            
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
            
            DB::commit();
            
            return redirect()->route('delivery-orders.show', $deliveryOrder)
                ->with('success', 'อัพเดทใบส่งสินค้าเลขที่ ' . $deliveryOrder->delivery_number . ' สำเร็จ');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
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
     * Get products from an order for AJAX requests.
     */
    public function getOrderProducts(Request $request)
    {
        $orderId = $request->input('order_id');
        
        if (!$orderId) {
            return response()->json(['error' => 'Order ID is required'], 400);
        }
        
        $order = Order::with(['items.product', 'customer'])->find($orderId);
        
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        
        return response()->json([
            'order' => $order,
            'items' => $order->items,
            'customer' => $order->customer,
        ]);
    }
}
