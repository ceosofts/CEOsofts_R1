<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Customer;
use App\Models\Company;
use App\Models\Employee; // เพิ่มการนำเข้าโมเดล Employee
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class QuotationController extends Controller
{
    /**
     * แสดงรายการใบเสนอราคาทั้งหมด
     */
    public function index(Request $request)
    {
        // ตรวจสอบและตั้งค่า company_id หากไม่มีใน session
        $companyId = session('company_id');
        
        // กรณีไม่มี company_id ใน session ให้หา company แรกที่ผู้ใช้มีสิทธิ์เข้าถึง
        if (empty($companyId)) {
            // หา company แรกที่ผู้ใช้มีสิทธิ์เข้าถึง
            $user = auth()->user();
            
            if ($user && $user->companies->isNotEmpty()) {
                $firstCompany = $user->companies->first();
                $companyId = $firstCompany->id;
                
                // บันทึกลง session
                session(['company_id' => $companyId]);
                
                // Log การตั้งค่า company_id
                \Illuminate\Support\Facades\Log::info('ตั้งค่า company_id เริ่มต้นเป็น ' . $companyId . ' สำหรับการแสดงรายการใบเสนอราคา');
            } else {
                // กรณีไม่พบ company ที่ผู้ใช้มีสิทธิ์เข้าถึง ใช้ค่าเริ่มต้นเป็น 1
                $companyId = 1;
                session(['company_id' => $companyId]);
                
                \Illuminate\Support\Facades\Log::warning('ไม่พบ company ที่ผู้ใช้มีสิทธิ์เข้าถึง กำหนดค่าเริ่มต้นเป็น 1');
            }
        }

        $query = Quotation::where('company_id', $companyId);

        // Search by text (quotation number, reference, customer name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('salesPerson', function ($q2) use ($search) {
                      $q2->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by customer
        if ($request->filled('customer')) {
            $query->where('customer_id', $request->customer);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('issue_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('issue_date', '<=', $request->date_to);
        }

        // Apply sorting
        $sortField = $request->input('sort', 'quotation_number');
        $sortDirection = $request->input('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $quotations = $query->paginate(15)->withQueryString();

        return view('quotations.index', compact('quotations'));
    }

    /**
     * แสดงหน้าฟอร์มสำหรับสร้างใบเสนอราคาใหม่
     */
    public function create()
    {
        $companyId = session('company_id', 1);
        $customers = Customer::where('company_id', $companyId)->orderBy('name')->get();
        $products = Product::where('company_id', $companyId)->orderBy('name')->get();
        $units = Unit::where('company_id', $companyId)->where('is_active', true)->orderBy('name')->get();
        $salesPersons = Employee::where('company_id', $companyId)->orderBy('first_name')->get();

        // สร้างเลขที่เอกสารอัตโนมัติโดยเรียกใช้เมธอดใหม่จากโมเดล Quotation
        $nextNumber = Quotation::generateQuotationNumber();

        return view('quotations.create', compact('customers', 'products', 'units', 'nextNumber', 'salesPersons'));
    }

    /**
     * เก็บข้อมูลใบเสนอราคาที่สร้างใหม่ลงในฐานข้อมูล
     */
    public function store(Request $request)
    {
        // ...existing code...
        
        $data = $request->validate([
            'customer_id' => 'required',
            'quotation_number' => 'required|unique:quotations',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after_or_equal:issue_date',
            'sales_person_id' => 'nullable|exists:employees,id', // เพิ่ม validation
            // ...existing validation rules...
        ]);

        try {
            // บันทึก raw request เพื่อดีบั๊ก
            Log::info('ข้อมูลที่รับจากฟอร์ม', [
                'all' => $request->all(),
                'products_json' => $request->products_json,
                'has_products_json' => $request->has('products_json')
            ]);
            
            // เพิ่มบันทึกล็อกเพื่อตรวจสอบการเรียกฟังก์ชัน
            Log::info('เริ่มสร้างใบเสนอราคา', $request->all());
            
            // ตรวจสอบความถูกต้องของข้อมูล
            $validated = $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'quotation_number' => 'required|string|unique:quotations,quotation_number',
                'issue_date' => 'required|date',
                'expiry_date' => 'required|date|after_or_equal:issue_date',
                'products_json' => 'required|json',
            ]);
            
            Log::info('ข้อมูลผ่านการตรวจสอบ', $validated);
            
            // แปลงข้อมูลจาก JSON เป็น array
            $productsData = json_decode($request->products_json, true);
            Log::info('ข้อมูลสินค้า', ['products_count' => count($productsData)]);
            
            if (empty($productsData)) {
                return redirect()->back()->with('error', 'กรุณาเพิ่มอย่างน้อย 1 รายการสินค้า')->withInput();
            }
            
            // ถ้าไม่พบ products_json ให้แจ้งเตือน
            if (!$request->has('products_json')) {
                Log::error('ไม่พบข้อมูล products_json ในคำขอ');
                return redirect()->back()
                    ->with('error', 'ไม่พบข้อมูลรายการสินค้า กรุณาเพิ่มอย่างน้อย 1 รายการ')
                    ->withInput();
            }
            
            // คำนวณยอดรวม
            $subtotal = 0;
            foreach ($productsData as $product) {
                $subtotal += $product['quantity'] * $product['unit_price'];
            }
            
            // คำนวณส่วนลด
            $discountAmount = 0;
            if ($request->discount_type === 'percentage' && $request->discount_amount > 0) {
                $discountAmount = $subtotal * ($request->discount_amount / 100);
            } elseif ($request->discount_type === 'fixed' && $request->discount_amount > 0) {
                $discountAmount = $request->discount_amount;
            }
            
            // คำนวณภาษี
            $afterDiscount = $subtotal - $discountAmount;
            $taxAmount = $afterDiscount * ($request->tax_rate / 100);
            $totalAmount = $afterDiscount + $taxAmount;
            
            $companyId = session('company_id', 1);
            
            // บันทึกข้อมูลที่จะใช้สร้างใบเสนอราคา
            $quotationData = [
                'company_id' => $companyId,
                'customer_id' => $request->customer_id,
                'quotation_number' => $request->quotation_number,
                'issue_date' => $request->issue_date,
                'expiry_date' => $request->expiry_date,
                'sales_person_id' => $request->sales_person_id, // เพิ่มบรรทัดนี้
                'subtotal' => $subtotal,
                'discount_type' => $request->discount_type ?? 'fixed',
                'discount_amount' => $discountAmount,
                'tax_rate' => $request->tax_rate ?? 0,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
                'reference_number' => $request->reference_number ?? ('REF-' . date('YmdHis') . '-' . rand(100, 999)),
                'shipping_method' => $request->shipping_method,
                'status' => 'draft',
                'created_by' => Auth::id(),
            ];
            
            Log::info('ข้อมูลที่จะบันทึก', $quotationData);
            
            // สร้างใบเสนอราคา
            $quotation = Quotation::create($quotationData);
            Log::info('สร้างใบเสนอราคาสำเร็จ', ['id' => $quotation->id]);
            
            // สร้างรายการสินค้า
            foreach ($productsData as $idx => $product) {
                $productModel = Product::find($product['product_id']);
                if (!$productModel) {
                    Log::warning('ไม่พบสินค้า', ['product_id' => $product['product_id']]);
                    continue; // ข้ามหากไม่พบสินค้า
                }
                
                $quantity = floatval($product['quantity']);
                $unitPrice = floatval($product['unit_price']);
                $discountPercentage = floatval($product['discount_percentage'] ?? 0);
                
                $itemSubtotal = $quantity * $unitPrice * (1 - ($discountPercentage / 100));
                
                $item = QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $product['product_id'],
                    'description' => $productModel->name,
                    'quantity' => $quantity,
                    'unit_id' => $product['unit_id'] ?? null,
                    'unit_price' => $unitPrice,
                    'discount_percentage' => $discountPercentage,
                    'discount_amount' => ($discountPercentage / 100) * ($quantity * $unitPrice),
                    'tax_percentage' => $request->tax_rate ?? 0,
                    'tax_amount' => ($request->tax_rate / 100) * $itemSubtotal,
                    'subtotal' => $itemSubtotal,
                    'total' => $itemSubtotal * (1 + (($request->tax_rate ?? 0) / 100)),
                    'metadata' => json_encode(['product_code' => $productModel->code ?? null])
                ]);
                
                Log::info('สร้างรายการสินค้า', ['item_id' => $item->id, 'product_name' => $productModel->name]);
            }
            
            // บันทึก log
            Log::info("สร้างใบเสนอราคาเลขที่ {$quotation->quotation_number} สำเร็จแล้ว");
            
            return redirect()->route('quotations.show', $quotation)
                ->with('success', 'สร้างใบเสนอราคาเลขที่ ' . $quotation->quotation_number . ' สำเร็จแล้ว');
        } catch (\Exception $e) {
            Log::error("เกิดข้อผิดพลาดในการสร้างใบเสนอราคา: " . $e->getMessage(), [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // ถ้าเป็นข้อผิดพลาดจากการตรวจสอบข้อมูล ส่งข้อมูลกลับไป
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return redirect()->back()->withErrors($e->validator)->withInput();
            }
            
            return redirect()->back()
                ->with('error', 'เกิดข้อผิดพลาดในการสร้างใบเสนอราคา: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * แสดงข้อมูลของใบเสนอราคาตาม ID
     */
    public function show($id)
    {
        // แก้ไขจาก placeholder เป็นการดึงข้อมูลจริง
        $quotation = Quotation::with(['customer', 'items.product', 'items.unit'])->findOrFail($id);
        
        $company = Company::find($quotation->company_id);
        
        return view('quotations.show', compact('quotation', 'company'));
    }

    /**
     * แสดงหน้าฟอร์มสำหรับแก้ไขข้อมูลใบเสนอราคา
     */
    public function edit($id)
    {
        $companyId = session('company_id');
        
        // โหลดพร้อมความสัมพันธ์ทั้ง product และ unit แบบเจาะจง
        $quotation = Quotation::with([
            'items.product', 
            'items.unit:id,name,code', // เพิ่ม code และระบุฟิลด์ที่ต้องการอย่างชัดเจน
            'customer', 
            'salesPerson'
        ])->findOrFail($id);
        
        // เพิ่มการล็อกข้อมูลเพื่อดีบั๊ก
        Log::debug('Quotation items with relationship: ', [
            'items_count' => $quotation->items->count(),
            'sample_item' => $quotation->items->first() ? [
                'product' => $quotation->items->first()->product ? $quotation->items->first()->product->name : 'No product',
                'unit' => $quotation->items->first()->unit ? [
                    'id' => $quotation->items->first()->unit->id,
                    'name' => $quotation->items->first()->unit->name,
                    'code' => $quotation->items->first()->unit->code ?? null
                ] : 'No unit'
            ] : 'No items'
        ]);
        
        $customers = Customer::where('company_id', $companyId)->orderBy('name')->get();
        $products = Product::where('company_id', $companyId)->orderBy('name')->get();
        $units = Unit::where('company_id', $companyId)->where('is_active', true)->orderBy('name')->get();
        $salesPersons = Employee::where('company_id', $companyId)->orderBy('first_name')->get();

        // เพิ่มการล็อกข้อมูลเพื่อดีบั๊ก
        Log::debug('Quotation items with relationship: ', [
            'items_count' => $quotation->items->count(),
            'sample_item' => $quotation->items->first() ? [
                'product' => $quotation->items->first()->product ? $quotation->items->first()->product->name : 'No product',
                'unit' => $quotation->items->first()->unit ? [
                    'id' => $quotation->items->first()->unit->id,
                    'name' => $quotation->items->first()->unit->name,
                    'code' => $quotation->items->first()->unit->code ?? null
                ] : 'No unit'
            ] : 'No items'
        ]);

        return view('quotations.edit', compact('quotation', 'customers', 'products', 'units', 'salesPersons'));
    }

    /**
     * อัปเดตข้อมูลใบเสนอราคา
     */
    public function update(Request $request, Quotation $quotation)
    {
        // ตรวจสอบว่าสถานะยังเป็น draft อยู่หรือไม่
        if ($quotation->status !== 'draft') {
            return redirect()->route('quotations.show', $quotation)
                ->with('error', 'ไม่สามารถแก้ไขใบเสนอราคาที่อนุมัติหรือปฏิเสธแล้วได้');
        }
        
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after_or_equal:issue_date',
            'products_json' => 'required|json',  // เปลี่ยนการตรวจสอบเป็น JSON string
            'sales_person_id' => 'nullable|exists:employees,id', // เพิ่ม validation
        ]);
        
        // แปลงข้อมูลจาก JSON เป็น array
        $productsData = json_decode($request->products_json, true);
        
        if (empty($productsData)) {
            return redirect()->back()->with('error', 'กรุณาเพิ่มอย่างน้อย 1 รายการสินค้า')->withInput();
        }
        
        // คำนวณยอดรวมและภาษีใหม่
        $subtotal = 0;
        foreach ($productsData as $product) {
            $subtotal += $product['quantity'] * $product['unit_price'];
        }
        
        $discountAmount = 0;
        if ($request->discount_type === 'percentage' && $request->discount_amount > 0) {
            $discountAmount = $subtotal * ($request->discount_amount / 100);
        } elseif ($request->discount_type === 'fixed') {
            $discountAmount = $request->discount_amount;
        }
        
        $afterDiscount = $subtotal - $discountAmount;
        $taxAmount = $afterDiscount * ($request->tax_rate / 100);
        $totalAmount = $afterDiscount + $taxAmount;
        
        // อัปเดตใบเสนอราคา
        $quotation->update([
            'customer_id' => $request->customer_id,
            'issue_date' => $request->issue_date,
            'expiry_date' => $request->expiry_date,
            'sales_person_id' => $request->sales_person_id, // เพิ่มบรรทัดนี้
            'subtotal' => $subtotal,
            'discount_type' => $request->discount_type,
            'discount_amount' => $discountAmount,
            'tax_rate' => $request->tax_rate,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'notes' => $request->notes,
            'reference_number' => $request->reference_number,
            'shipping_method' => $request->shipping_method,
        ]);
        
        // ลบรายการสินค้าเดิมและสร้างใหม่
        $quotation->items()->delete();
        
        foreach ($productsData as $product) {
            $productModel = Product::find($product['product_id']);
            if (!$productModel) continue; // ข้ามหากไม่พบสินค้า
            
            $quantity = floatval($product['quantity']);
            $unitPrice = floatval($product['unit_price']);
            $discountPercentage = floatval($product['discount_percentage'] ?? 0);
            
            $itemSubtotal = $quantity * $unitPrice * (1 - ($discountPercentage / 100));
            
            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'product_id' => $product['product_id'],
                'description' => $productModel->name,
                'quantity' => $quantity,
                'unit_id' => $product['unit_id'],
                'unit_price' => $unitPrice,
                'discount_percentage' => $discountPercentage,
                'discount_amount' => ($discountPercentage / 100) * ($quantity * $unitPrice),
                'tax_percentage' => $request->tax_rate,
                'tax_amount' => ($request->tax_rate / 100) * $itemSubtotal,
                'subtotal' => $itemSubtotal,
                'total' => $itemSubtotal * (1 + ($request->tax_rate / 100)),
                'metadata' => json_encode(['product_code' => $productModel->code ?? null])
            ]);
        }
        
        // เพิ่มการ logging เพื่อตรวจสอบ
        Log::info("อัปเดตใบเสนอราคาเลขที่ {$quotation->quotation_number} สำเร็จ");
        
        return redirect()->route('quotations.show', $quotation)
            ->with('success', 'อัปเดตใบเสนอราคาเลขที่ ' . $quotation->quotation_number . ' สำเร็จแล้ว');
    }

    /**
     * ลบใบเสนอราคาออกจากฐานข้อมูล
     */
    public function destroy($id)
    {
        try {
            // ค้นหาใบเสนอราคา
            $quotation = Quotation::findOrFail($id);
            
            // ตรวจสอบว่ามีการสร้างคำสั่งซื้อจากใบเสนอราคานี้หรือไม่
            if ($quotation->orders()->exists()) {
                return redirect()->route('quotations.index')
                    ->with('error', 'ไม่สามารถลบใบเสนอราคาที่มีการสร้างคำสั่งซื้อแล้วได้');
            }
            
            // เก็บเลขที่ใบเสนอราคาไว้แสดงข้อความ
            $quotationNumber = $quotation->quotation_number;
            
            // ลบรายการสินค้าในใบเสนอราคาก่อน
            $quotation->items()->delete();
            
            // ลบใบเสนอราคา (soft delete)
            $quotation->delete();
            
            // บันทึกล็อก
            Log::info("ลบใบเสนอราคาเลขที่ {$quotationNumber} สำเร็จแล้ว");
            
            return redirect()->route('quotations.index')
                ->with('success', 'ลบใบเสนอราคาเลขที่ ' . $quotationNumber . ' สำเร็จแล้ว');
        } catch (\Exception $e) {
            Log::error("เกิดข้อผิดพลาดในการลบใบเสนอราคา: " . $e->getMessage());
            
            return redirect()->route('quotations.index')
                ->with('error', 'เกิดข้อผิดพลาดในการลบใบเสนอราคา: ' . $e->getMessage());
        }
    }

    /**
     * อนุมัติใบเสนอราคา
     *
     * @param  \App\Models\Quotation  $quotation
     * @return \Illuminate\Http\Response
     */
    public function approve(Quotation $quotation)
    {
        // ตรวจสอบว่าใบเสนอราคาอยู่ในสถานะ draft หรือไม่
        if ($quotation->status !== 'draft') {
            return redirect()->route('quotations.show', $quotation)->with('error', 'ไม่สามารถอนุมัติใบเสนอราคานี้ได้ เนื่องจากไม่อยู่ในสถานะ "กำลังเสนอลูกค้า"');
        }
        
        // อัพเดทสถานะเป็น approved
        $quotation->status = 'approved';
        $quotation->approved_at = now();
        $quotation->save();
        
        return redirect()->route('quotations.show', $quotation)->with('success', 'อนุมัติใบเสนอราคาเรียบร้อยแล้ว');
    }

    /**
     * ปฏิเสธใบเสนอราคา
     *
     * @param  \App\Models\Quotation  $quotation
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function reject(Request $request, Quotation $quotation)
    {
        // ตรวจสอบว่าใบเสนอราคาอยู่ในสถานะ draft หรือไม่
        if ($quotation->status !== 'draft') {
            return redirect()->route('quotations.show', $quotation)->with('error', 'ไม่สามารถปฏิเสธใบเสนอราคานี้ได้ เนื่องจากไม่อยู่ในสถานะ "กำลังเสนอลูกค้า"');
        }
        
        // บันทึกเหตุผลการปฏิเสธ (ถ้ามี)
        $quotation->status = 'rejected';
        $quotation->rejection_reason = $request->input('rejection_reason');
        $quotation->rejected_at = now();
        $quotation->save();
        
        return redirect()->route('quotations.show', $quotation)->with('success', 'ปฏิเสธใบเสนอราคาเรียบร้อยแล้ว');
    }

    /**
     * จัดเตรียมข้อมูลใบเสนอราคาสำหรับการนำไปสร้างใบสั่งขาย
     * ใช้สำหรับ AJAX request เพื่อดึงข้อมูลแบบ JSON
     */
    public function getData(Quotation $quotation)
    {
        try {
            // โหลดความสัมพันธ์ที่จำเป็น
            $quotation->load([
                'customer', 
                'items.product.unit'
            ]);
            
            // เพิ่ม log สำหรับตรวจสอบ
            Log::info('Quotation data requested', [
                'quotation_id' => $quotation->id,
                'quotation_number' => $quotation->quotation_number,
                'items_count' => $quotation->items->count()
            ]);
            
            // ส่งข้อมูลกลับเป็น JSON
            return response()->json($quotation);
        
        } catch (\Exception $e) {
            Log::error('Error fetching quotation data', [
                'quotation_id' => $quotation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'ไม่สามารถดึงข้อมูลใบเสนอราคาได้: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * แสดงใบเสนอราคาในรูปแบบ PDF View (รีไดเร็กต์ไปยังมุมมองพิมพ์)
     *
     * @param  \App\Models\Quotation  $quotation
     * @return \Illuminate\Http\Response
     */
    public function viewAsPdf(Quotation $quotation)
    {
        // เปลี่ยนการเรียกใช้จาก pdf-view เป็น print view แทน
        return $this->printView($quotation);
    }

    /**
     * แสดงใบเสนอราคาในรูปแบบพร้อมพิมพ์
     *
     * @param  \App\Models\Quotation  $quotation
     * @return \Illuminate\Http\Response
     */
    public function printView(Quotation $quotation)
    {
        // โหลดความสัมพันธ์ที่จำเป็น
        $quotation->load(['customer', 'items.product', 'items.unit']);
        
        // หาข้อมูลบริษัท
        $company = \App\Models\Company::find($quotation->company_id);
        
        // ส่ง view print ที่สร้างใหม่
        return view('quotations.print', [
            'quotation' => $quotation,
            'company' => $company,
        ]);
    }
}
