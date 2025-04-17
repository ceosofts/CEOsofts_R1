<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\QuotationItem; // เพิ่มการนำเข้าคลาส QuotationItem
use App\Models\Product; // เพิ่มการนำเข้าคลาส Product
use App\Models\Unit; // เพิ่มการนำเข้าคลาส Unit ถ้าใช้
use App\Models\Customer; // เพิ่มการนำเข้าคลาส Customer
use App\Models\Company; // เพิ่มการนำเข้าคลาส Company
use Illuminate\Support\Facades\Auth; // เพิ่มการนำเข้า Auth Facade
use Illuminate\Support\Facades\Log; // เพิ่มการนำเข้า Log Facade
use Illuminate\Pagination\LengthAwarePaginator;

class QuotationController extends Controller
{
    /**
     * แสดงรายการใบเสนอราคาทั้งหมด
     */
    public function index(Request $request)
    {
        try {
            // แก้ไขการดึงค่า company_id ให้มีค่าเริ่มต้นเป็น 1 ถ้าไม่มีค่า
            $companyId = session('company_id');
            
            // ถ้า session ไม่มีค่า ลองดึงจาก user
            if (empty($companyId) && Auth::check()) {
                $companyId = Auth::user()->company_id;
            }
            
            // ถ้ายังไม่มีค่า ให้ใช้ค่าเริ่มต้นเป็น 1
            if (empty($companyId)) {
                $companyId = 1; // ค่าเริ่มต้นกรณีไม่พบ company_id
                // บันทึก session เพื่อใช้ในครั้งต่อไป
                session(['company_id' => $companyId]);
            }
            
            // สร้าง query builder เริ่มต้น - ต้องแน่ใจว่า $query ถูกกำหนดค่า
            $query = Quotation::with(['customer', 'creator'])
                    ->where('company_id', $companyId);
            
            // ...existing code for filters...
            
            // จัดเรียงข้อมูล - เปลี่ยนค่าเริ่มต้นเป็น quotation_number
            $sort = $request->input('sort', 'quotation_number');
            $direction = $request->input('direction', 'desc');
            
            // ตรวจสอบ column ที่อนุญาตให้เรียงลำดับ
            $allowedSortColumns = ['quotation_number', 'issue_date', 'expiry_date', 'total_amount', 'created_at'];
            
            if (in_array($sort, $allowedSortColumns)) {
                $query->orderBy($sort, $direction);
            } else {
                $query->orderBy('quotation_number', 'desc');
            }
            
            // ดึงข้อมูลพร้อม pagination
            $quotations = $query->paginate(10);
            
            // นับจำนวนตามสถานะ (เพิ่มบรรทัดนี้เพื่อให้แน่ใจว่ามี $statusCounts)
            $statusCounts = [
                'all' => Quotation::where('company_id', $companyId)->count(),
                'draft' => Quotation::where('company_id', $companyId)->where('status', 'draft')->count(),
                'approved' => Quotation::where('company_id', $companyId)->where('status', 'approved')->count(),
                'rejected' => Quotation::where('company_id', $companyId)->where('status', 'rejected')->count(),
            ];
            
            // เตรียมข้อมูล debug (ถ้าจำเป็น)
            $debugInfo = null;
            if (config('app.env') === 'local') {
                $debugInfo = [
                    'total_count' => $quotations->total(),
                    'query_count' => $quotations->count(),
                    // ...additional debug info...
                ];
            }
            
            return view('quotations.index', compact('quotations', 'statusCounts', 'debugInfo'));
        } catch (\Exception $e) {
            // ใช้ LengthAwarePaginator แทน collect()->paginate()
            $perPage = 10;
            $currentPage = $request->input('page', 1);
            $path = route('quotations.index');
            
            $quotations = new LengthAwarePaginator(
                [], // empty array
                0, // total items
                $perPage,
                $currentPage,
                ['path' => $path]
            );
            
            return view('quotations.index', [
                'quotations' => $quotations,
                'statusCounts' => ['all' => 0, 'draft' => 0, 'approved' => 0, 'rejected' => 0],
                'error' => 'เกิดข้อผิดพลาดในการดึงข้อมูลใบเสนอราคา: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * แสดงหน้าฟอร์มสำหรับสร้างใบเสนอราคาใหม่
     */
    public function create()
    {
        $companyId = session('company_id', 1);
        $customers = Customer::where('company_id', $companyId)->orderBy('name')->get();
        $products = Product::where('company_id', $companyId)->orderBy('name')->get();
        $units = Unit::all();

        // สร้างเลขที่เอกสารอัตโนมัติ
        $latestQuotation = Quotation::where('company_id', $companyId)
            ->orderBy('id', 'desc')
            ->first();
        
        // รูปแบบ: QT + ปีเดือน (YYMM) + เลขลำดับ 4 หลัก
        $nextNumber = 'QT' . date('Ym') . '0001';
        if ($latestQuotation) {
            $lastNumber = intval(substr($latestQuotation->quotation_number, -4));
            $nextNumber = 'QT' . date('Ym') . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        }

        return view('quotations.create', compact('customers', 'products', 'units', 'nextNumber'));
    }

    /**
     * เก็บข้อมูลใบเสนอราคาที่สร้างใหม่ลงในฐานข้อมูล
     */
    public function store(Request $request)
    {
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
        // แก้ไขจาก placeholder เป็นการดึงข้อมูลจริง
        $quotation = Quotation::with(['items.product', 'items.unit'])->findOrFail($id);
        
        // ตรวจสอบว่าสถานะยังเป็น draft อยู่หรือไม่
        if ($quotation->status !== 'draft') {
            return redirect()->route('quotations.show', $quotation)
                ->with('error', 'ไม่สามารถแก้ไขใบเสนอราคาที่อนุมัติหรือปฏิเสธแล้วได้');
        }
        
        $companyId = session('company_id', 1);
        
        // เตรียมข้อมูลที่จำเป็นสำหรับฟอร์มแก้ไข
        $customers = Customer::where('company_id', $companyId)->orderBy('name')->get();
        $products = Product::where('company_id', $companyId)->orderBy('name')->get();
        $units = Unit::all();
        
        return view('quotations.edit', compact('quotation', 'customers', 'products', 'units'));
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
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($id)
    {
        try {
            // ค้นหาใบเสนอราคา
            $quotation = Quotation::findOrFail($id);
            
            // ตรวจสอบว่าสามารถอนุมัติได้หรือไม่ (ต้องเป็นสถานะ draft)
            if ($quotation->status !== 'draft') {
                return redirect()->route('quotations.show', $quotation)
                    ->with('error', 'ไม่สามารถอนุมัติใบเสนอราคาที่ไม่ได้อยู่ในสถานะร่างได้');
            }
            
            // อนุมัติใบเสนอราคา
            $quotation->status = 'approved';
            $quotation->approved_by = Auth::id();
            $quotation->approved_at = now();
            $quotation->save();
            
            // บันทึกล็อก
            Log::info("อนุมัติใบเสนอราคาเลขที่ {$quotation->quotation_number} สำเร็จแล้ว", [
                'quotation_id' => $quotation->id,
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);
            
            return redirect()->route('quotations.show', $quotation)
                ->with('success', 'อนุมัติใบเสนอราคาเลขที่ ' . $quotation->quotation_number . ' เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            Log::error("เกิดข้อผิดพลาดในการอนุมัติใบเสนอราคา: " . $e->getMessage(), [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()->back()
                ->with('error', 'เกิดข้อผิดพลาดในการอนุมัติใบเสนอราคา: ' . $e->getMessage());
        }
    }

    /**
     * ปฏิเสธใบเสนอราคา
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, $id)
    {
        try {
            // ค้นหาใบเสนอราคา
            $quotation = Quotation::findOrFail($id);
            
            // ตรวจสอบว่าสามารถปฏิเสธได้หรือไม่ (ต้องเป็นสถานะ draft)
            if ($quotation->status !== 'draft') {
                return redirect()->route('quotations.show', $quotation)
                    ->with('error', 'ไม่สามารถปฏิเสธใบเสนอราคาที่ไม่ได้อยู่ในสถานะร่างได้');
            }
            
            // ปฏิเสธใบเสนอราคา
            $quotation->status = 'rejected';
            $quotation->approved_by = Auth::id(); // ใช้ approved_by เก็บผู้ปฏิเสธ
            $quotation->approved_at = now(); // ใช้ approved_at เก็บเวลาที่ปฏิเสธ
            
            // เก็บเหตุผลในการปฏิเสธ (ถ้ามี)
            if ($request->has('rejection_reason')) {
                $metadata = $quotation->metadata ?: [];
                $metadata['rejection_reason'] = $request->rejection_reason;
                $quotation->metadata = $metadata;
            }
            
            $quotation->save();
            
            // บันทึกล็อก
            Log::info("ปฏิเสธใบเสนอราคาเลขที่ {$quotation->quotation_number} สำเร็จแล้ว", [
                'quotation_id' => $quotation->id,
                'rejected_by' => Auth::id(),
                'rejection_reason' => $request->rejection_reason ?? null
            ]);
            
            return redirect()->route('quotations.show', $quotation)
                ->with('success', 'ปฏิเสธใบเสนอราคาเลขที่ ' . $quotation->quotation_number . ' เรียบร้อยแล้ว');
        } catch (\Exception $e) {
            Log::error("เกิดข้อผิดพลาดในการปฏิเสธใบเสนอราคา: " . $e->getMessage(), [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()->back()
                ->with('error', 'เกิดข้อผิดพลาดในการปฏิเสธใบเสนอราคา: ' . $e->getMessage());
        }
    }
}
