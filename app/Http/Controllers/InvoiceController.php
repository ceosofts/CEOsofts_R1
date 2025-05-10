<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $companyId = session('company_id') ?? session('current_company_id');
        
        $query = \App\Models\Invoice::query();
        
        if ($companyId) {
            $query->where('company_id', $companyId);
        }
        
        // ความสัมพันธ์ที่เกี่ยวข้อง
        $query->with(['customer', 'order']);

        // Search parameter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Customer filter
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('from_date')) {
            $query->whereDate('invoice_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('invoice_date', '<=', $request->to_date);
        }

        // Apply sorting
        $sortField = $request->input('sort', 'invoice_number');
        $sortDirection = $request->input('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);
        
        try {
            $invoices = $query->paginate(15)->withQueryString();
            
            if ($invoices->total() === 0 && $request->anyFilled(['search', 'customer_id', 'status', 'from_date', 'to_date'])) {
                session()->flash('info', 'ไม่พบข้อมูลใบแจ้งหนี้จากเงื่อนไขที่ระบุ กรุณาลองใหม่อีกครั้ง');
            }
        } catch (\Exception $e) {
            Log::error('Error getting invoices', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $invoices = collect();
            session()->flash('error', 'เกิดข้อผิดพลาดในการดึงข้อมูล: ' . $e->getMessage());
        }

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $orderId = $request->order_id;
        $order = null;
        
        // ถ้ามีการส่ง order_id มาให้สร้างใบแจ้งหนี้จากใบสั่งขาย
        if ($orderId) {
            $order = Order::with('items.product', 'customer')->find($orderId);
        }
        
        // ดึงใบสั่งขายที่พร้อมออกใบแจ้งหนี้ (สถานะ processing, shipped, delivered)
        $eligibleOrders = Order::whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // ใช้เมธอด generateInvoiceNumber จากโมเดล Invoice
        $invoiceNumber = Invoice::generateInvoiceNumber();
        
        return view('invoices.create', compact('customers', 'products', 'invoiceNumber', 'order', 'eligibleOrders'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('Invoice form submission received', [
            'customer_id' => $request->customer_id,
            'invoice_number' => $request->invoice_number
        ]);
        
        try {
            // ตรวจสอบเลขที่ใบแจ้งหนี้ซ้ำ (ตรวจสอบทั้งรายการปกติและที่ถูกลบแล้ว)
            $invoiceNumber = $request->invoice_number;
            $existingInvoice = \App\Models\Invoice::withTrashed()
                ->where('invoice_number', $invoiceNumber)
                ->exists();
            
            if ($existingInvoice) {
                // กรณีมีเลขซ้ำให้สร้างเลขใหม่จากโมเดล
                $invoiceNumber = Invoice::generateInvoiceNumber();
                Log::info('พบเลขใบแจ้งหนี้ซ้ำ สร้างเลขใหม่: ' . $invoiceNumber);
            }
            
            // Validate
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'invoice_number' => [
                    'required',
                    'string',
                    Rule::unique('invoices')->whereNull('deleted_at'),
                ],
                'invoice_date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:invoice_date',
                'status' => 'required|in:draft,pending,approved,paid,partially_paid,overdue,cancelled',
                'notes' => 'nullable|string',
                'payment_terms' => 'nullable|string',
                'discount_type' => 'nullable|in:fixed,percentage',
                'discount_amount' => 'nullable|numeric|min:0',
                'tax_rate' => 'nullable|numeric|min:0',
                'tax_inclusive' => 'boolean',
                'products' => 'required|array|min:1',
                'products.*.id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|numeric|min:0.01',
                'products.*.unit_price' => 'required|numeric|min:0',
                'order_id' => 'nullable|exists:orders,id',
                'sales_person_id' => 'nullable|exists:employees,id', // เพิ่มการ validate sales_person_id
                'shipping_address' => 'nullable|string',
                'shipping_method' => 'nullable|string',
                'shipping_cost' => 'nullable|numeric|min:0',
            ]);
            
            // เริ่ม transaction
            DB::beginTransaction();
            
            // คำนวณยอดรวม
            $subtotal = 0;
            foreach ($validated['products'] as $product) {
                $subtotal += $product['quantity'] * $product['unit_price'];
            }
            
            $discountType = $validated['discount_type'] ?? 'fixed';
            $discountAmount = $validated['discount_amount'] ?? 0;
            $taxRate = $validated['tax_rate'] ?? 7; // ใช้ค่าเริ่มต้นเป็น 7% หากไม่ได้ระบุ
            $taxInclusive = $validated['tax_inclusive'] ?? false;
            $shippingCost = $validated['shipping_cost'] ?? 0;
            
            // คำนวณส่วนลด
            $discountValue = 0;
            if ($discountType === 'percentage' && $discountAmount > 0) {
                $discountValue = $subtotal * ($discountAmount / 100);
            } else {
                $discountValue = $discountAmount;
            }
            
            // คำนวณภาษี
            $afterDiscount = $subtotal - $discountValue;
            $taxAmount = 0;
            
            if ($taxRate > 0) {
                if ($taxInclusive) {
                    // ภาษีรวมในราคาแล้ว
                    $taxAmount = $afterDiscount - ($afterDiscount / (1 + ($taxRate / 100)));
                } else {
                    // ภาษีเพิ่มจากราคา
                    $taxAmount = $afterDiscount * ($taxRate / 100);
                }
            }
            
            // คำนวณยอดรวม
            $totalAmount = $afterDiscount + $taxAmount;
            
            // เพิ่มค่าขนส่งในยอดรวม
            $totalAmount += $shippingCost;
            
            // เตรียมข้อมูลสำหรับสร้าง Invoice
            $invoiceData = [
                'company_id' => session('current_company_id') ?? session('company_id') ?? Auth::user()->company_id ?? 1,
                'customer_id' => $validated['customer_id'],
                'order_id' => $validated['order_id'] ?? null,
                'invoice_number' => $invoiceNumber,
                'reference_number' => $request->reference_number,
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'status' => $validated['status'] ?? 'draft',
                'discount_type' => $discountType,
                'discount_amount' => $discountAmount,
                'discount_value' => $discountValue,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'subtotal' => $subtotal,
                'shipping_address' => $validated['shipping_address'] ?? null,
                'shipping_method' => $validated['shipping_method'] ?? null,
                'shipping_cost' => $shippingCost,
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
                'payment_terms' => $validated['payment_terms'] ?? null,
                'sales_person_id' => $validated['sales_person_id'] ?? null, // บันทึกพนักงานขาย
                'created_by' => Auth::id(),
            ];
            
            // สร้าง Invoice
            $invoice = \App\Models\Invoice::create($invoiceData);
            
            Log::info('Invoice created successfully', ['invoice_id' => $invoice->id, 'total_amount' => $totalAmount]);
            
            // สร้างรายการสินค้า
            foreach ($validated['products'] as $productData) {
                $product = Product::find($productData['id']);
                
                if (!$product) {
                    throw new \Exception('ไม่พบข้อมูลสินค้า ID: ' . $productData['id']);
                }
                
                // คำนวณภาษีและยอดรวมสำหรับแต่ละรายการ
                $itemSubtotal = $productData['quantity'] * $productData['unit_price'];
                $itemTaxAmount = $taxRate > 0 ? $itemSubtotal * ($taxRate / 100) : 0;
                $itemTotal = $taxInclusive ? $itemSubtotal : $itemSubtotal + $itemTaxAmount;
                
                $invoiceItemData = [
                    'invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'description' => $product->name,
                    'quantity' => $productData['quantity'],
                    'unit' => $product->unit ? $product->unit->name : null,
                    'unit_price' => $productData['unit_price'],
                    'discount_type' => 'fixed',
                    'discount_amount' => 0,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $itemTaxAmount,
                    'subtotal' => $itemSubtotal,
                    'total' => $itemTotal,
                ];
                
                \App\Models\InvoiceItem::create($invoiceItemData);
            }
            
            // อัพเดทใบสั่งขาย (ถ้ามี)
            if ($validated['order_id']) {
                $order = Order::find($validated['order_id']);
                if ($order) {
                    $order->has_invoice = true;
                    $order->save();
                }
            }
                
            // ยืนยันการทำรายการ
            DB::commit();
            
            Log::info('Invoice process completed successfully', ['invoice_id' => $invoice->id]);
            
            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'สร้างใบแจ้งหนี้สำเร็จ');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Invoice validation failed', [
                'errors' => $e->errors()
            ]);
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error_message', 'กรุณาตรวจสอบข้อมูลให้ถูกต้อง');
            
        } catch (\Exception $e) {
            // กรณี transaction ยังไม่ถูก rollback
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            
            Log::error('Invoice creation failed with exception', [
                'exception_class' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error_message', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(\App\Models\Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product', 'creator', 'order']);
        
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(\App\Models\Invoice $invoice)
    {
        // ตรวจสอบว่าใบแจ้งหนี้สามารถแก้ไขได้หรือไม่ (สถานะ draft หรือ pending เท่านั้น)
        if (!in_array($invoice->status, ['draft', 'pending'])) {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'ใบแจ้งหนี้นี้ไม่สามารถแก้ไขได้เนื่องจากสถานะปัจจุบัน');
        }
        
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        
        // ดึงใบสั่งขายที่พร้อมออกใบแจ้งหนี้ (สถานะ processing, shipped, delivered)
        $eligibleOrders = Order::whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('invoices.edit', compact('invoice', 'customers', 'products', 'eligibleOrders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, \App\Models\Invoice $invoice)
    {
        Log::info('Invoice Update Request', [
            'request' => $request->all(),
            'invoice_id' => $invoice->id
        ]);
        
        // ตรวจสอบว่าใบแจ้งหนี้สามารถแก้ไขได้หรือไม่
        if (!in_array($invoice->status, ['draft', 'pending'])) {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'ใบแจ้งหนี้นี้ไม่สามารถแก้ไขได้เนื่องจากสถานะปัจจุบัน');
        }
        
        try {
            // Validate
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'invoice_number' => [
                    'required',
                    'string',
                    Rule::unique('invoices', 'invoice_number')->ignore($invoice->id),
                ],
                'invoice_date' => 'required|date',
                'due_date' => 'required|date|after_or_equal:invoice_date',
                'status' => 'required|in:draft,pending,approved,paid,partially_paid,overdue,cancelled',
                'notes' => 'nullable|string',
                'terms' => 'nullable|string',
                'discount_type' => 'nullable|in:fixed,percentage',
                'discount_amount' => 'nullable|numeric|min:0',
                'tax_rate' => 'nullable|numeric|min:0',
                'tax_inclusive' => 'boolean',
                'products' => 'required|array|min:1',
                'products.*.id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|numeric|min:0.01',
                'products.*.unit_price' => 'required|numeric|min:0',
                'order_id' => 'nullable|exists:orders,id',
            ]);
            
            // เริ่ม transaction
            DB::beginTransaction();
            
            // คำนวณยอดรวม
            $subtotal = 0;
            foreach ($validated['products'] as $product) {
                $subtotal += $product['quantity'] * $product['unit_price'];
            }
            
            $discountType = $validated['discount_type'] ?? 'fixed';
            $discountAmount = $validated['discount_amount'] ?? 0;
            $taxRate = $validated['tax_rate'] ?? 0;
            $taxInclusive = $validated['tax_inclusive'] ?? false;
            
            // คำนวณส่วนลด
            $discountValue = 0;
            if ($discountType === 'percentage' && $discountAmount > 0) {
                $discountValue = $subtotal * ($discountAmount / 100);
            } else {
                $discountValue = $discountAmount;
            }
            
            // คำนวณภาษี
            $afterDiscount = $subtotal - $discountValue;
            $taxAmount = 0;
            
            if ($taxRate > 0) {
                if ($taxInclusive) {
                    // ภาษีรวมในราคาแล้ว
                    $taxAmount = $afterDiscount - ($afterDiscount / (1 + ($taxRate / 100)));
                } else {
                    // ภาษีเพิ่มจากราคา
                    $taxAmount = $afterDiscount * ($taxRate / 100);
                }
            }
            
            // คำนวณยอดรวม
            $totalAmount = $taxInclusive ? $afterDiscount : $afterDiscount + $taxAmount;
            
            // ปรับข้อมูล order_id ในกรณีที่มีการเปลี่ยนแปลง
            $newOrderId = $validated['order_id'] ?? null;
            if ($invoice->order_id != $newOrderId) {
                // ถ้ามีการเปลี่ยน order เดิมให้อัพเดทสถานะ
                if ($invoice->order_id) {
                    $oldOrder = Order::find($invoice->order_id);
                    if ($oldOrder) {
                        $oldOrder->has_invoice = false;
                        $oldOrder->save();
                    }
                }
                
                // ถ้ามี order ใหม่ ให้อัพเดทสถานะ
                if ($newOrderId) {
                    $newOrder = Order::find($newOrderId);
                    if ($newOrder) {
                        $newOrder->has_invoice = true;
                        $newOrder->save();
                    }
                }
            }
            
            // อัพเดทใบแจ้งหนี้
            $invoiceData = [
                'customer_id' => $validated['customer_id'],
                'order_id' => $newOrderId,
                'invoice_number' => $validated['invoice_number'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'status' => $validated['status'],
                'discount_type' => $discountType,
                'discount_amount' => $discountAmount,
                'discount_value' => $discountValue, // แก้ไขจาก total_discount เป็น discount_value
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'subtotal' => $subtotal,
                'shipping_address' => $request->shipping_address ?? $invoice->shipping_address,
                'shipping_method' => $request->shipping_method ?? $invoice->shipping_method,
                'shipping_cost' => $request->shipping_cost ?? $invoice->shipping_cost,
                'total_amount' => $totalAmount + ($request->shipping_cost ?? $invoice->shipping_cost ?? 0), // รวมค่าขนส่งในยอดรวม
                'notes' => $validated['notes'] ?? null,
                'payment_terms' => $validated['payment_terms'] ?? $invoice->payment_terms, // แก้ไขจาก terms เป็น payment_terms
                'sales_person_id' => $request->sales_person_id ?? $invoice->sales_person_id,
            ];
            
            $invoice->update($invoiceData);
            
            // ลบรายการสินค้าเก่า
            $invoice->items()->delete();
            
            // สร้างรายการสินค้าใหม่
            foreach ($validated['products'] as $productData) {
                $product = Product::find($productData['id']);
                
                \App\Models\InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'description' => $product->name,
                    'quantity' => $productData['quantity'],
                    'unit' => $product->unit ? $product->unit->name : null,
                    'unit_price' => $productData['unit_price'],
                    'discount_type' => 'fixed',
                    'discount_amount' => 0,
                    'tax_rate' => $taxRate,
                    'tax_amount' => ($productData['quantity'] * $productData['unit_price']) * ($taxRate / 100),
                    'subtotal' => $productData['quantity'] * $productData['unit_price'],
                    'total' => $productData['quantity'] * $productData['unit_price'] * (1 + ($taxRate / 100)),
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'อัพเดทใบแจ้งหนี้สำเร็จ');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Invoice Update Validation Error', [
                'errors' => $e->errors(),
                'invoice_id' => $invoice->id
            ]);
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            
            Log::error('Invoice update failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(\App\Models\Invoice $invoice)
    {
        // ตรวจสอบว่าใบแจ้งหนี้สามารถลบได้หรือไม่ (เฉพาะสถานะ draft, cancelled)
        if (!in_array($invoice->status, ['draft', 'cancelled'])) {
            return redirect()->route('invoices.index')
                ->with('error', 'ไม่สามารถลบใบแจ้งหนี้ที่ดำเนินการแล้ว');
        }
        
        try {
            // ลบรายการสินค้าก่อน
            $invoice->items()->delete();
            
            // ถ้ามีการเชื่อมโยงกับใบสั่งขาย ให้อัพเดทสถานะใบสั่งขาย
            if ($invoice->order_id) {
                $order = Order::find($invoice->order_id);
                if ($order) {
                    $order->has_invoice = false;
                    $order->save();
                }
            }
            
            // ลบใบแจ้งหนี้
            $invoice->delete();
            
            return redirect()->route('invoices.index')
                ->with('success', 'ลบใบแจ้งหนี้สำเร็จ');
                
        } catch (\Exception $e) {
            return redirect()->route('invoices.index')
                ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * อัพเดทสถานะใบแจ้งหนี้เป็น approved/issued.
     */
    public function issue(\App\Models\Invoice $invoice)
    {
        if (!in_array($invoice->status, ['draft', 'pending'])) {
            return redirect()->back()->with('error', 'สถานะปัจจุบันไม่อนุญาตให้อนุมัติได้');
        }
        
        $invoice->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        
        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'อนุมัติใบแจ้งหนี้สำเร็จ');
    }

    /**
     * อัพเดทสถานะใบแจ้งหนี้เป็น paid.
     */
    public function markAsPaid(\App\Models\Invoice $invoice)
    {
        if (!in_array($invoice->status, ['approved', 'pending', 'partially_paid', 'overdue'])) {
            return redirect()->back()->with('error', 'สถานะปัจจุบันไม่อนุญาตให้ทำเครื่องหมายว่าชำระแล้วได้');
        }
        
        $invoice->update([
            'status' => 'paid',
            'amount_paid' => $invoice->total,
            'amount_due' => 0,
            'paid_at' => now(),
            'paid_by' => Auth::id(),
        ]);
        
        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'อัพเดทสถานะเป็นชำระแล้วสำเร็จ');
    }

    /**
     * อัพเดทสถานะใบแจ้งหนี้เป็น cancelled/void.
     */
    public function void(Request $request, \App\Models\Invoice $invoice)
    {
        if (in_array($invoice->status, ['paid', 'cancelled'])) {
            return redirect()->back()->with('error', 'ไม่สามารถยกเลิกใบแจ้งหนี้ที่ชำระแล้วหรือยกเลิกไปแล้ว');
        }
        
        $validated = $request->validate([
            'void_reason' => 'required|string',
        ]);
        
        $invoice->update([
            'status' => 'cancelled',
            'void_by' => Auth::id(),
            'void_at' => now(),
            'void_reason' => $validated['void_reason'],
        ]);
        
        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'ยกเลิกใบแจ้งหนี้สำเร็จ');
    }
    
    /**
     * Generate a unique invoice number.
     * 
     * @param bool $forceNew บังคับสร้างเลขใหม่แม้จะมีเลขในฐานข้อมูลแล้ว
     * @return string
     */
    public function generateInvoiceNumber($forceNew = false)
    {
        $prefix = 'INV' . date('Ym');
        
        // ค้นหาใบแจ้งหนี้ล่าสุดที่ใช้เลขขึ้นต้นเหมือนกัน (รวมที่ถูกลบแล้ว)
        $lastInvoice = \App\Models\Invoice::withTrashed()
            ->where('invoice_number', 'like', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();
            
        $number = '001';
        
        if ($lastInvoice) {
            // ดึงตัวเลขจากเลขที่ใบแจ้งหนี้ล่าสุด (3 ตัวท้าย)
            $lastNumber = substr($lastInvoice->invoice_number, -3);
            
            // ถ้าบังคับให้สร้างเลขใหม่ ให้เพิ่มไปอีก 1
            if ($forceNew) {
                $number = str_pad((int)$lastNumber + 1, 3, '0', STR_PAD_LEFT);
            } else {
                // ตรวจสอบว่ามีใบแจ้งหนี้ที่ใช้งานอยู่หรือไม่
                $exists = \App\Models\Invoice::where('invoice_number', $lastInvoice->invoice_number)->exists();
                
                if ($exists) {
                    // ถ้ามีแล้ว ให้เพิ่มเลขไปอีก 1
                    $number = str_pad((int)$lastNumber + 1, 3, '0', STR_PAD_LEFT);
                } else {
                    // ถ้าไม่มี (อาจถูกลบไปแล้ว แต่ไม่ได้บังคับให้สร้างใหม่) ให้ใช้เลขเดิม
                    $number = $lastNumber;
                }
            }
        }
        
        $invoiceNumber = $prefix . $number;
        
        // ตรวจสอบอีกครั้งว่าเลขที่สร้างใหม่ซ้ำหรือไม่
        while (\App\Models\Invoice::withTrashed()->where('invoice_number', $invoiceNumber)->exists()) {
            // ถ้าซ้ำให้เพิ่มไปอีก 1
            $number = str_pad((int)$number + 1, 3, '0', STR_PAD_LEFT);
            $invoiceNumber = $prefix . $number;
        }
        
        Log::info('Generated invoice number', ['invoice_number' => $invoiceNumber]);
        
        return $invoiceNumber;
    }

    /**
     * ดึงข้อมูลจากใบสั่งขายเพื่อสร้างใบแจ้งหนี้
     */
    public function getOrderData($orderId)
    {
        Log::info('getOrderData called', ['order_id' => $orderId]);
        
        try {
            // ใช้ with เพื่อโหลดความสัมพันธ์ที่จำเป็นทั้งหมด
            $order = Order::with([
                'items.product.unit', 
                'customer',
                'salesPerson'
            ])->findOrFail($orderId);
            
            Log::info('Found order', [
                'order_number' => $order->order_number,
                'items_count' => $order->items->count()
            ]);
            
            // สร้างข้อมูลในรูปแบบที่เหมาะสำหรับการใช้ในฟอร์มสร้างใบแจ้งหนี้
            // รูปแบบตรงกับที่ JavaScript ในหน้า create.blade.php คาดหวัง
            $response = [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'customer_id' => $order->customer_id,
                'shipping_address' => $order->shipping_address ?? '',
                'shipping_method' => $order->shipping_method ?? '',
                'shipping_cost' => $order->shipping_cost ?? 0,
                'payment_terms' => $order->payment_terms ?? 'ชำระภายใน 30 วัน',
                'notes' => $order->notes ?? '',
                'total_amount' => $order->total_amount,
                'subtotal' => $order->subtotal,
                'tax_rate' => $order->tax_rate ?? 7,
                'tax_amount' => $order->tax_amount ?? 0,
                'discount_type' => $order->discount_type ?? 'fixed',
                'discount_amount' => $order->discount_amount ?? 0,
                'sales_person_id' => $order->sales_person_id,
                'customer' => [
                    'id' => $order->customer->id,
                    'name' => $order->customer->name,
                    'email' => $order->customer->email ?? '',
                    'phone' => $order->customer->phone ?? '',
                    'address' => $order->customer->address ?? '',
                ],
                'items' => $order->items->map(function ($item) {
                    // ดึงข้อมูลหน่วยจากสินค้า
                    $unitName = '';
                    $unitId = null;
                    
                    if ($item->unit_id) {
                        $unitId = $item->unit_id;
                        $unitName = $item->unit ?? '';
                    } elseif ($item->product && $item->product->unit_id) {
                        $unitId = $item->product->unit_id;
                        $unitName = $item->product->unit ? $item->product->unit->name : '';
                    }
                    
                    return [
                        'id' => $item->product_id,
                        'product_id' => $item->product_id,
                        'name' => $item->product ? $item->product->name : $item->description,
                        'description' => $item->description,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'unit_id' => $unitId,
                        'unit_name' => $unitName,
                        'unit' => $unitName,
                        'total' => $item->quantity * $item->unit_price,
                        'product' => $item->product ? [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'code' => $item->product->code ?? $item->product->sku ?? '-',
                            'sku' => $item->product->sku ?? '',
                            'unit_id' => $item->product->unit_id ?? null,
                            'unit_name' => $item->product->unit ? $item->product->unit->name : '',
                        ] : null,
                    ];
                }),
            ];
            
            Log::info('Response prepared', [
                'items_count' => count($response['items']),
                'customer' => $response['customer']['name']
            ]);
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            Log::error('Error getting order data', [
                'message' => $e->getMessage(),
                'order_id' => $orderId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['error' => 'เกิดข้อผิดพลาดในการโหลดข้อมูล: ' . $e->getMessage()], 500);
        }
    }

    /**
     * แสดงใบแจ้งหนี้ในรูปแบบสำหรับพิมพ์
     */
    public function print(Invoice $invoice)
    {
        // โหลดความสัมพันธ์ที่จำเป็น
        $invoice->load(['customer', 'items.product', 'order', 'creator', 'salesPerson']);
        
        // ดึงข้อมูลบริษัทจากฐานข้อมูล
        $companyId = session('company_id') ?? session('current_company_id') ?? $invoice->company_id ?? 1;
        $company = \App\Models\Company::find($companyId);
        
        if (!$company) {
            // ถ้าไม่พบบริษัท ให้ดึงบริษัทแรกในระบบเป็นค่าเริ่มต้น
            $company = \App\Models\Company::first();
        }
        
        // ส่งข้อมูลไปยัง view สำหรับการพิมพ์
        return view('invoices.print', compact('invoice', 'company'));
    }
}
