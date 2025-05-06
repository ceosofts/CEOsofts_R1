<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Quotation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log; // เพิ่มบรรทัดนี้
use Illuminate\Support\Facades\Schema; // เพิ่มบรรทัดนี้

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // ดำเนินการสร้างข้อมูลตัวอย่างถ้ามีการร้องขอ (เฉพาะโหมด debug)
        if ($request->has('seed_sample')) {
            try {
                // รันคำสั่ง migration เพื่อเพิ่มคอลัมน์ที่จำเป็นก่อน
                if (config('app.debug')) {
                    if (!Schema::hasColumn('orders', 'tax_rate')) {
                        \Illuminate\Support\Facades\Artisan::call('migrate', [
                            '--path' => 'database/migrations/0001_01_01_00070_add_tax_rate_to_orders_table.php',
                            '--force' => true
                        ]);
                        Log::info('เพิ่มคอลัมน์ tax_rate ในตาราง orders เรียบร้อยแล้ว');
                    }
                    
                    if (!Schema::hasColumn('orders', 'shipping_cost')) {
                        \Illuminate\Support\Facades\Artisan::call('migrate', [
                            '--path' => 'database/migrations/0001_01_01_00071_add_shipping_cost_to_orders_table.php',
                            '--force' => true
                        ]);
                        Log::info('เพิ่มคอลัมน์ shipping_cost ในตาราง orders เรียบร้อยแล้ว');
                    }
                }
                
                // เลือกบริษัทหลัก
                $companyId = session('company_id') ?? session('current_company_id') ?? 1;
                $company = \App\Models\Company::find($companyId);
                
                Log::info('เริ่มการทำงานสร้างข้อมูลตัวอย่าง', [
                    'company_id' => $companyId,
                    'company' => $company ? $company->name : 'ไม่พบบริษัท'
                ]);
                
                // สร้างข้อมูลตัวอย่างโดยเรียกใช้ OrderSeeder โดยตรง
                $seeder = new \Database\Seeders\OrderSeeder();
                $seeder->run($companyId);
                
                // รีเซ็ต Cache สำหรับ Order
                if (method_exists(\App\Models\Order::class, 'flushCache')) {
                    \App\Models\Order::flushCache();
                }
                
                return redirect()->route('orders.index')
                    ->with('success', 'สร้างข้อมูลตัวอย่างเรียบร้อยแล้ว กรุณารีเฟรชหน้าเพื่อดูข้อมูล');
            } catch (\Exception $e) {
                Log::error('เกิดข้อผิดพลาดในการสร้างข้อมูลตัวอย่าง', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return redirect()->route('orders.index')
                    ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage() . '. กรุณาตรวจสอบ log ไฟล์');
            }
        }

        // ปรับปรุงการดึงค่า company ID ให้ทำงานได้ดีขึ้น
        $companyId = session('company_id') ?? session('current_company_id');
        
        // กรณีไม่มี company ID ในระบบ แต่เป็น admin ให้ดึงบริษัทแรกในระบบ
        if (empty($companyId) && Auth::check() && Auth::user()->hasRole('Super Admin')) {
            $firstCompany = \App\Models\Company::first();
            if ($firstCompany) {
                $companyId = $firstCompany->id;
                // ทำการตั้งค่า session เพื่อให้การใช้งานต่อไปได้ผลลัพธ์
                session(['company_id' => $companyId]);
                session(['current_company_id' => $companyId]);
                
                Log::info('Auto-setting company_id for Admin', [
                    'company_id' => $companyId,
                    'company_name' => $firstCompany->name,
                    'user_id' => Auth::id(),
                    'user_email' => Auth::user()->email
                ]);
            } else {
                Log::warning('No companies found in the system');
            }
        }

        // เพิ่มการตรวจสอบค่า companyId อีกครั้ง และกำหนดค่าเริ่มต้นเป็น 1 ถ้ายังไม่มี
        $companyId = $companyId ?? 1;
        
        // เริ่ม direct query เพื่อตรวจสอบว่ามีข้อมูลหรือไม่
        $checkOrdersExist = \App\Models\Order::where('company_id', $companyId)->exists();
        
        // บันทึก log เพื่อตรวจสอบค่า company_id
        Log::info('Orders index called', [
            'company_id' => $companyId,
            'session_company_id' => session('company_id'),
            'current_company_id' => session('current_company_id'),
            'user_id' => Auth::id(),
            'orders_exist' => $checkOrdersExist ? 'Yes' : 'No'
        ]);
        
        // เริ่มต้น query
        $query = \App\Models\Order::query();
        
        // ถ้ามี company_id และไม่ใช่ Super Admin ให้กรองตาม company_id
        // ถ้าเป็น Super Admin และไม่มี company_id ให้แสดงทั้งหมด
        if ($companyId && !(Auth::check() && Auth::user()->hasRole('Super Admin') && $request->has('show_all'))) {
            $query->where('company_id', $companyId);
        }
        
        // เพิ่ม eager loading ความสัมพันธ์ที่จำเป็น
        $query->with(['customer', 'salesPerson']);

        // Search parameter handling
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_po_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('salesPerson', function ($q2) use ($search) {
                      $q2->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
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
            $query->whereDate('order_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('order_date', '<=', $request->to_date);
        }

        // Apply sorting
        $sortField = $request->input('sort', 'order_number');
        $sortDirection = $request->input('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // ก่อนเรียก paginate(), ให้บันทึก raw SQL query เพื่อตรวจสอบ
        Log::info('Order query', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);
        
        // ใช้ try/catch เพื่อจับ errors ที่อาจเกิดขึ้น
        try {
            $orders = $query->paginate(15)->withQueryString();
            
            // บันทึกจำนวนใบสั่งขายที่พบ
            Log::info('Orders found', ['count' => $orders->total()]);
            
            // ถ้าไม่พบข้อมูล แต่มีการค้นหา เพิ่มข้อความแจ้งเตือน
            if ($orders->total() === 0 && $request->anyFilled(['search', 'customer_id', 'status', 'from_date', 'to_date'])) {
                session()->flash('info', 'ไม่พบข้อมูลใบสั่งขายจากเงื่อนไขที่ระบุ กรุณาลองใหม่อีกครั้ง');
            }
        } catch (\Exception $e) {
            // กรณีมี error ให้เตรียม collection ว่าง
            Log::error('Error getting orders', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $orders = collect(); // สร้าง collection ว่าง
            session()->flash('error', 'เกิดข้อผิดพลาดในการดึงข้อมูล: ' . $e->getMessage());
        }

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $quotationId = $request->quotation_id;
        $quotation = null;
        
        // ถ้ามีการส่ง quotation_id มาให้สร้างใบสั่งขายจากใบเสนอราคา
        if ($quotationId) {
            $quotation = Quotation::with('items.product', 'customer')->find($quotationId);
        }
        
        // เพิ่มการโหลดพนักงานขาย
        $salesPersons = \App\Models\Employee::where('company_id', session('company_id'))->orderBy('first_name')->get();
        
        // ดึงใบเสนอราคาที่อนุมัติแล้วทั้งหมด
        $approvedQuotations = Quotation::where('status', 'approved')
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $orderNumber = Order::generateOrderNumber(session('company_id'));
        
        return view('orders.create', compact('customers', 'products', 'orderNumber', 'quotation', 'approvedQuotations', 'salesPersons'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // เพิ่มการ log ข้อมูลที่ได้รับจากฟอร์มอย่างละเอียด
        Log::info('Order form submission received', [
            'customer_id' => $request->customer_id,
            'order_number' => $request->order_number
        ]);
        
        try {
            // ตรวจสอบเลขที่ใบสั่งขายซ้ำก่อนเริ่มกระบวนการ รวมถึงรายการที่ถูก soft delete
            $orderNumber = $request->order_number;
            $existingOrder = Order::withTrashed()->where('order_number', $orderNumber)->exists();
            
            if ($existingOrder) {
                // ถ้าเลขซ้ำ ให้สร้างเลขใหม่ทันที
                $orderNumber = Order::generateOrderNumber();
                Log::info('เลขใบสั่งขายซ้ำ สร้างเลขใหม่: ' . $orderNumber);
            }

            // ปรับปรุงการ validate ให้มีความยืดหยุ่นมากขึ้น
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'order_number' => [
                    'required',
                    'string',
                    Rule::unique('orders')->whereNull('deleted_at'),
                ],
                'order_date' => 'required|date',
                'delivery_date' => 'nullable|date',
                'status' => 'required|in:draft,confirmed,processing,shipped,delivered,cancelled',
                'notes' => 'nullable|string',
                'shipping_address' => 'nullable|string',
                'shipping_method' => 'nullable|string',
                'shipping_cost' => 'nullable|numeric|min:0',
                'products' => 'required|array|min:1',
                'products.*.id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|numeric|min:0.01',
                'products.*.unit_price' => 'required|numeric|min:0',
                'payment_terms' => 'nullable|string',
                'tax_rate' => 'nullable|numeric|min:0',
                'discount_type' => 'nullable|in:fixed,percentage',
                'discount_amount' => 'nullable|numeric|min:0',
                'customer_po_number' => 'nullable|string',
                'sales_person_id' => 'nullable|exists:employees,id',
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
            
            // คำนวณส่วนลด
            $discountValue = 0;
            if ($discountType === 'percentage' && $discountAmount > 0) {
                $discountValue = $subtotal * ($discountAmount / 100);
            } else {
                $discountValue = $discountAmount;
            }
            
            // คำนวณภาษี
            $taxAmount = ($subtotal - $discountValue) * ($taxRate / 100);
            
            // คำนวณยอดรวม
            $totalAmount = $subtotal - $discountValue + $taxAmount + floatval($validated['shipping_cost'] ?? 0);
            
            // เตรียมข้อมูลสำหรับสร้าง Order
            $orderData = [
                'company_id' => session('current_company_id') ?? session('company_id') ?? Auth::user()->company_id ?? 1,
                'customer_id' => $validated['customer_id'],
                'order_number' => $orderNumber,
                'order_date' => $validated['order_date'],
                'status' => $validated['status'] ?? 'draft',
                'total_amount' => $totalAmount,
                'subtotal' => $subtotal,
                'discount_amount' => $discountValue,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'shipping_cost' => $validated['shipping_cost'] ?? 0,
                'delivery_date' => $validated['delivery_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'shipping_address' => $validated['shipping_address'] ?? null,
                'shipping_method' => $validated['shipping_method'] ?? null,
                'payment_terms' => $validated['payment_terms'] ?? null,
                'customer_po_number' => $validated['customer_po_number'] ?? null,
                'quotation_id' => $request->quotation_id ?? null,
                'created_by' => Auth::id(),
                'sales_person_id' => $request->sales_person_id,
            ];
            
            // เพิ่มตรวจสอบว่าคอลัมน์ discount_type มีอยู่หรือไม่
            if (Schema::hasColumn('orders', 'discount_type')) {
                $orderData['discount_type'] = $discountType;
            }
            
            // กำหนด created_at และ updated_at เป็นรูปแบบที่ SQLite รองรับ
            $now = now()->format('Y-m-d H:i:s');
            $orderData['created_at'] = $now;
            $orderData['updated_at'] = $now;

            Log::info('Creating order with data', ['order_data' => $orderData]);
            
            try {
                // แทนที่จะใช้ DB::table('orders')->insertGetId เราจะใช้ Order::create เพื่อให้ Eloquent จัดการ
                $order = new Order($orderData);
                $order->save();
                
                Log::info('Order created successfully', ['order_id' => $order->id]);
                
                // สร้างรายการสินค้า
                foreach ($validated['products'] as $productData) {
                    $product = Product::find($productData['id']);
                    
                    if (!$product) {
                        throw new \Exception('ไม่พบข้อมูลสินค้า ID: ' . $productData['id']);
                    }
                    
                    $orderItemData = [
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'description' => $product->name,
                        'quantity' => $productData['quantity'],
                        'unit_price' => $productData['unit_price'],
                        'price' => $productData['unit_price'],
                        'unit_id' => $product->unit_id ?? null,
                        'total' => $productData['quantity'] * $productData['unit_price'],
                    ];
                    
                    OrderItem::create($orderItemData);
                }
                
                // ถ้ามีการสร้างจากใบเสนอราคา ให้อัพเดทสถานะใบเสนอราคา
                if ($request->quotation_id) {
                    $quotation = Quotation::find($request->quotation_id);
                    if ($quotation) {
                        $quotation->update(['status' => 'converted']);
                    }
                }
                    
                // ยืนยันการทำรายการ
                DB::commit();
                
                Log::info('Order process completed successfully', ['order_id' => $order->id]);
                
                return redirect()->route('orders.show', $order)
                    ->with('success', 'สร้างใบสั่งขายสำเร็จ');
                
            } catch (\Exception $innerException) {
                // ถ้าเกิด error ให้ rollback transaction และ throw exception ขึ้นไปให้ catch ข้างนอก
                DB::rollBack();
                Log::error('Error creating order or order items', [
                    'message' => $innerException->getMessage(),
                    'trace' => $innerException->getTraceAsString()
                ]);
                throw $innerException;
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Order validation failed', [
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
            
            Log::error('Order creation failed with exception', [
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
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $order->load(['customer', 'items.product', 'creator']);
        
        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        // ตรวจสอบว่าใบสั่งขายสามารถแก้ไขได้หรือไม่ (เช่น ยังไม่ส่งสินค้า)
        if (in_array($order->status, ['shipped', 'delivered', 'cancelled'])) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'ใบสั่งขายนี้ไม่สามารถแก้ไขได้เนื่องจากสถานะปัจจุบัน');
        }
        
        $customers = Customer::orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $salesPersons = \App\Models\Employee::where('company_id', session('company_id'))->orderBy('first_name')->get();
        
        return view('orders.edit', compact('order', 'customers', 'products', 'salesPersons'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        // เพิ่ม verbose debugging
        Log::info('Order Update Request', [
            'request' => $request->all(),
            'order_id' => $order->id
        ]);
        
        // เพิ่ม debug message ลงใน Debugbar
        if (class_exists('\Debugbar')) {
            \Debugbar::info('OrderController@update called');
            \Debugbar::info($request->all());
            \Debugbar::startMeasure('update_order', 'Time for updating order');
        }
        
        // เพิ่มการตรวจสอบข้อมูลที่ส่งมา (debugging)
        Log::info('Order Update Request', $request->all());
        
        // ตรวจสอบว่าใบสั่งขายสามารถแก้ไขได้หรือไม่
        if (in_array($order->status, ['shipped', 'delivered', 'cancelled'])) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'ใบสั่งขายนี้ไม่สามารถแก้ไขได้เนื่องจากสถานะปัจจุบัน');
        }
        
        try {
            Log::info('Validating order data');
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'order_number' => 'required|unique:orders,order_number,' . $order->id,
                'order_date' => 'required|date',
                'delivery_date' => 'nullable|date',
                'status' => 'required|in:draft,confirmed,processing,shipped,delivered,cancelled',
                'notes' => 'nullable|string',
                'shipping_address' => 'nullable|string',
                'shipping_method' => 'nullable|string',
                'shipping_cost' => 'nullable|numeric',
                'products' => 'required|array',
                'products.*.id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|numeric|min:1',
                'products.*.unit_price' => 'required|numeric|min:0',
                'payment_terms' => 'nullable|string',
                'tax_rate' => 'nullable|numeric|min:0',
                'discount_type' => 'nullable|in:fixed,percentage',
                'discount_amount' => 'nullable|numeric|min:0',
                'customer_po_number' => 'nullable|string', // รองรับ customer_po_number
                'sales_person_id' => 'nullable|exists:employees,id', // เพิ่ม validation rule
            ]);
            
            Log::info('Starting transaction');
            // เริ่ม transaction แบบชัดเจน และเช็คการ rollback เมื่อมีปัญหา
            DB::beginTransaction();
            
            // คำนวณยอดรวมเหมือนกับ store method
            $subtotal = 0;
            foreach ($validated['products'] as $product) {
                $subtotal += $product['quantity'] * $product['unit_price'];
            }
            
            // คำนวณส่วนลด
            $discountAmount = 0;
            if (isset($validated['discount_amount']) && $validated['discount_amount'] > 0) {
                if ($validated['discount_type'] == 'percentage') {
                    $discountAmount = $subtotal * ($validated['discount_amount'] / 100);
                } else {
                    $discountAmount = $validated['discount_amount'];
                }
            }
            
            // คำนวณภาษี
            $taxAmount = 0;
            $taxRate = $validated['tax_rate'] ?? 0;
            if ($taxRate > 0) {
                $taxAmount = ($subtotal - $discountAmount) * ($taxRate / 100);
            }
            
            // คำนวณยอดรวม
            $totalAmount = $subtotal - $discountAmount + $taxAmount + ($validated['shipping_cost'] ?? 0);
            
            // อัพเดทใบสั่งขาย
            $updateData = [
                'customer_id' => $validated['customer_id'],
                'order_number' => $validated['order_number'],
                'order_date' => $validated['order_date'],
                'delivery_date' => $validated['delivery_date'] ?? null,
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? null,
                'shipping_address' => $validated['shipping_address'] ?? null,
                'shipping_method' => $validated['shipping_method'] ?? null,
                'shipping_cost' => $validated['shipping_cost'] ?? 0,
                'total_amount' => $totalAmount,
                'payment_terms' => $validated['payment_terms'] ?? null,
                'customer_po_number' => $validated['customer_po_number'] ?? null,
                'sales_person_id' => $request->sales_person_id,
            ];
            
            // เพิ่มตรวจสอบคอลัมน์ก่อนอัพเดท
            if (Schema::hasColumn('orders', 'discount_type')) {
                $updateData['discount_type'] = $validated['discount_type'] ?? null;
            }
            if (Schema::hasColumn('orders', 'subtotal')) {
                $updateData['subtotal'] = $subtotal;
            }
            if (Schema::hasColumn('orders', 'discount_amount')) {
                $updateData['discount_amount'] = $discountAmount;
            }
            if (Schema::hasColumn('orders', 'tax_rate')) {
                $updateData['tax_rate'] = $taxRate;
            }
            if (Schema::hasColumn('orders', 'tax_amount')) {
                $updateData['tax_amount'] = $taxAmount;
            }
            
            $order->update($updateData);
            
            // ลบรายการสินค้าเก่า (ใช้เทคนิคลบชั่วคราวแทนการลบถาวร)
            foreach ($order->items as $item) {
                $item->delete();  // SoftDelete หากใช้ trait
            }
            
            // สร้างรายการสินค้าใหม่
            foreach ($request->products as $productData) {
                $product = Product::find($productData['id']);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'description' => $product->name,
                    'quantity' => $productData['quantity'],
                    'unit_price' => $productData['unit_price'],
                    'price' => $productData['unit_price'],  // เพิ่ม price เพื่อให้มั่นใจว่าทั้งสองฟิลด์มีค่า
                    'unit_id' => $product->unit_id ?? null,
                    'total' => $productData['quantity'] * $productData['unit_price'],
                ]);
            }
            
            Log::info('Committing transaction');
            DB::commit();
            
            Log::info('Order successfully updated', ['order_id' => $order->id]);
            if (class_exists('\Debugbar')) {
                \Debugbar::stopMeasure('update_order');
            }
            
            return redirect()->route('orders.show', $order)
                ->with('success', 'อัพเดทใบสั่งขายสำเร็จ');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            // บันทึก validation errors ลง log
            Log::error('Order Update Validation Error', [
                'errors' => $e->errors(),
                'order_id' => $order->id
            ]);
            
            // ส่ง error กลับไปที่หน้าฟอร์ม
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error_debug', json_encode($e->errors())); // เพิ่ม debug information
        } catch (\Exception $e) {
            // บันทึกข้อผิดพลาดลง log
            Log::error('Order update failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())
                ->with('error_debug', $e->getTraceAsString()); // เพิ่ม debug information
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        // ตรวจสอบว่าใบสั่งขายสามารถลบได้หรือไม่ (เช่น ยังไม่จัดส่ง)
        if (!in_array($order->status, ['draft', 'cancelled'])) {
            return redirect()->route('orders.index')
                ->with('error', 'ไม่สามารถลบใบสั่งขายที่ดำเนินการแล้ว');
        }
        
        try {
            // ลบรายการสินค้าก่อน
            $order->items()->delete();
            // ลบใบสั่งขาย
            $order->delete();
            
            return redirect()->route('orders.index')
                ->with('success', 'ลบใบสั่งขายสำเร็จ');
                
        } catch (\Exception $e) {
            return redirect()->route('orders.index')
                ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * Update order status to confirmed.
     */
    public function confirm(Order $order)
    {
        if ($order->status !== 'draft') {
            return redirect()->back()->with('error', 'สถานะปัจจุบันไม่อนุญาตให้ยืนยันได้');
        }
        
        $order->update([
            'status' => 'confirmed',
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
        ]);
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'ยืนยันใบสั่งขายสำเร็จ');
    }

    /**
     * Update order status to processing.
     */
    public function process(Order $order)
    {
        if ($order->status !== 'confirmed') {
            return redirect()->back()->with('error', 'สถานะปัจจุบันไม่อนุญาตให้ดำเนินการได้');
        }
        
        $order->update([
            'status' => 'processing',
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'อัพเดทสถานะเป็นกำลังดำเนินการสำเร็จ');
    }

    /**
     * Update order status to shipped.
     */
    public function ship(Request $request, Order $order)
    {
        if (!in_array($order->status, ['confirmed', 'processing'])) {
            return redirect()->back()->with('error', 'สถานะปัจจุบันไม่อนุญาตให้จัดส่งได้');
        }
        
        $validated = $request->validate([
            'tracking_number' => 'nullable|string',
            'shipping_notes' => 'nullable|string',
        ]);
        
        $order->update([
            'status' => 'shipped',
            'shipped_by' => Auth::id(),
            'shipped_at' => now(),
            'tracking_number' => $validated['tracking_number'] ?? null,
            'shipping_notes' => $validated['shipping_notes'] ?? null,
        ]);
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'อัพเดทสถานะเป็นจัดส่งแล้วสำเร็จ');
    }

    /**
     * Update order status to delivered.
     */
    public function deliver(Order $order)
    {
        if ($order->status !== 'shipped') {
            return redirect()->back()->with('error', 'สถานะปัจจุบันไม่อนุญาตให้ทำเครื่องหมายว่าส่งมอบแล้วได้');
        }
        
        $order->update([
            'status' => 'delivered',
            'delivered_by' => Auth::id(),
            'delivered_at' => now(),
        ]);
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'อัพเดทสถานะเป็นส่งมอบแล้วสำเร็จ');
    }

    /**
     * Update order status to cancelled.
     */
    public function cancel(Request $request, Order $order)
    {
        // ตรวจสอบว่าสามารถยกเลิกได้หรือไม่
        if (in_array($order->status, ['shipped', 'delivered', 'cancelled'])) {
            return redirect()->back()->with('error', 'ไม่สามารถยกเลิกใบสั่งขายที่จัดส่งหรือส่งมอบแล้ว');
        }
        
        $validated = $request->validate([
            'cancellation_reason' => 'required|string',
        ]);
        
        $order->update([
            'status' => 'cancelled',
            'cancelled_by' => Auth::id(),
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['cancellation_reason'],
        ]);
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'ยกเลิกใบสั่งขายสำเร็จ');
    }
    
    /**
     * Generate a unique order number.
     */
    protected function generateOrderNumber()
    {
        $prefix = 'SO' . date('Ym');
        $lastOrder = Order::where('order_number', 'like', $prefix . '%')
            ->orderBy('order_number', 'desc')
            ->first();
            
        $number = '001';
        
        if ($lastOrder) {
            $lastNumber = substr($lastOrder->order_number, -3);
            $number = str_pad((int)$lastNumber + 1, 3, '0', STR_PAD_LEFT);
        }
        
        return $prefix . $number;
    }

    /**
     * ดึงข้อมูลสินค้าในใบสั่งขายสำหรับใช้ในการสร้างใบส่งสินค้า
     *
     * @param int $id รหัสใบสั่งขาย
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrderProducts($id)
    {
        Log::info('API getOrderProducts ถูกเรียกใช้', [
            'id' => $id,
            'type' => gettype($id)
        ]);

        try {
            // ตรวจสอบว่า ID มีค่าหรือไม่
            if (!$id) {
                return response()->json(['error' => 'Order ID is required'], 400);
            }

            // ใช้ findOrFail เพื่อให้เกิด 404 ถ้าไม่พบ
            $order = Order::findOrFail($id);
            
            // โหลดข้อมูลที่เกี่ยวข้อง
            $order->load(['items.product.unit', 'customer', 'quotation.items']);
            
            Log::info('พบใบสั่งขาย', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'customer' => $order->customer ? $order->customer->name : 'ไม่มีข้อมูลลูกค้า',
                'items_count' => $order->items->count()
            ]);

            // ตรวจสอบว่ามีข้อมูลลูกค้าหรือไม่
            if (!$order->customer) {
                return response()->json(['error' => 'ไม่พบข้อมูลลูกค้าในใบสั่งขาย'], 400);
            }

            // สร้างข้อมูลสำหรับส่งกลับ
            $response = [
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'customer_id' => $order->customer_id,
                    'shipping_address' => $order->shipping_address ?? '',
                    'shipping_method' => $order->shipping_method ?? '',
                    'delivery_date' => $order->delivery_date ? $order->delivery_date->format('Y-m-d') : null,
                    'notes' => $order->notes ?? '',
                    'sales_person_id' => $order->sales_person_id ?? null,
                ],
                'customer' => $order->customer,
                'items' => $order->items->map(function ($item) {
                    // ตรวจสอบและดึงข้อมูลหน่วยจากใบเสนอราคาหรือจากสินค้า
                    $unit = null;
                    $unitName = '';
                    
                    if ($item->unit_id) {
                        $unit = \App\Models\Unit::find($item->unit_id);
                        $unitName = $unit ? $unit->name : '';
                    } elseif ($item->product && $item->product->unit_id) {
                        $unit = $item->product->unit;
                        $unitName = $unit ? $unit->name : '';
                    }
                    
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_code' => $item->product ? $item->product->code ?? $item->product->sku ?? '' : '',
                        'description' => $item->description ?? '',
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'unit_id' => $item->unit_id ?? ($item->product ? $item->product->unit_id : null),
                        'unit_name' => $unitName,
                        'product' => $item->product ? [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'sku' => $item->product->sku ?? '',
                            'code' => $item->product->code ?? '',
                            'unit_id' => $item->product->unit_id ?? null,
                            'unit_name' => $item->product->unit ? $item->product->unit->name : '',
                        ] : null,
                    ];
                }),
                'sales_person' => $order->sales_person_id ? \App\Models\Employee::find($order->sales_person_id) : null,
                'quotation' => $order->quotation_id ? [
                    'id' => $order->quotation->id,
                    'quotation_number' => $order->quotation->quotation_number
                ] : null,
            ];

            return response()->json($response);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('ไม่พบใบสั่งขาย ID: ' . $id);
            return response()->json(['error' => 'ไม่พบใบสั่งขาย'], 404);
        } catch (\Exception $e) {
            Log::error('เกิดข้อผิดพลาดในการดึงข้อมูลใบสั่งขาย', [
                'id' => $id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'เกิดข้อผิดพลาดในการดึงข้อมูล: ' . $e->getMessage()], 500);
        }
    }
}
