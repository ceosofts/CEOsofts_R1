<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /**
     * แสดงรายการลูกค้าทั้งหมด
     */
    public function index(Request $request)
    {
        try {
            $query = Customer::query();

            // ค้นหาและกรอง
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('code', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('phone', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('group')) {
                $query->where('customer_group', $request->group);
            }

            // การเรียงลำดับ (กำหนดค่าเริ่มต้นให้เรียงตามรหัสลูกค้าจากมากไปน้อย)
            $sortField = $request->input('sort', 'code');
            $sortDirection = $request->input('direction', 'desc');
            
            $query->orderBy($sortField, $sortDirection);

            $customers = $query->paginate(10)->withQueryString();
            
            return view('customers.index', compact('customers'));
            
        } catch (\Exception $e) {
            Log::error('Error in customer index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'เกิดข้อผิดพลาดในการแสดงรายการลูกค้า');
        }
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'type' => 'required|in:company,person',
                'status' => 'required|in:active,inactive',
                'address' => 'nullable|string',
                'tax_id' => 'nullable|string|max:20',
                'contact_person' => 'nullable|string|max:255',
                'website' => 'nullable|string|max:255',
                'note' => 'nullable|string',
                'code' => 'nullable|string|max:50|unique:customers',
                'credit_limit' => 'nullable|numeric|min:0',
                'industry' => 'nullable|string|max:100',
                'credit_term' => 'nullable|integer|min:0',
                'sales_region' => 'nullable|string|max:100',
                'contact_person_position' => 'nullable|string|max:100',
                'contact_person_email' => 'nullable|email|max:255',
                'contact_person_phone' => 'nullable|string|max:50',
                'contact_person_line_id' => 'nullable|string|max:100',
                'payment_term_type' => 'nullable|string|in:cash,credit,cheque,transfer',
                'discount_rate' => 'nullable|numeric|min:0|max:100',
                'reference_id' => 'nullable|string|max:100',
                'customer_group' => 'nullable|string|max:10',
                'customer_rating' => 'nullable|integer|min:1|max:5',
                'bank_account_name' => 'nullable|string|max:255',
                'bank_account_number' => 'nullable|string|max:20',
                'bank_name' => 'nullable|string|max:100',
                'bank_branch' => 'nullable|string|max:100',
                'is_supplier' => 'nullable|boolean',
            ]);
            
            DB::beginTransaction();
            
            $data = $request->all();
            
            // แปลง metadata จากฟอร์ม
            $metadata = $this->processMetadata($request);
            if ($metadata) {
                $data['metadata'] = json_encode($metadata);
            }
            
            // แปลง social_media เป็น JSON
            if (isset($data['social_media']) && is_array($data['social_media'])) {
                $data['social_media'] = json_encode($data['social_media']);
            }
            
            // สร้างรหัสอัตโนมัติหากไม่ได้ระบุ
            if (empty($data['code'])) {
                $data['code'] = Customer::generateCustomerCode();
            }
            
            // เพิ่มข้อมูล company_id จาก session หรือค่าเริ่มต้น
            $data['company_id'] = session('current_company_id', 1);
            
            $customer = Customer::create($data);
            
            DB::commit();
            
            return redirect()->route('customers.show', $customer)
                ->with('success', 'สร้างลูกค้าใหม่สำเร็จ');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Customer creation error: ' . $e->getMessage());
            
            return redirect()->back()->withInput()
                ->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * ประมวลผลข้อมูล metadata จากฟอร์ม
     */
    private function processMetadata(Request $request)
    {
        // เริ่มด้วยข้อมูล metadata ที่มีอยู่แล้ว
        $metadata = $request->input('metadata', []);
        
        // เพิ่มข้อมูลจากฟิลด์ custom
        if ($request->has('metadata_keys') && $request->has('metadata_values')) {
            $keys = $request->input('metadata_keys');
            $values = $request->input('metadata_values');
            
            for ($i = 0; $i < count($keys); $i++) {
                if (!empty($keys[$i]) && isset($values[$i])) {
                    $metadata[$keys[$i]] = $values[$i];
                }
            }
        }
        
        return $metadata;
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer)
    {
        // Get customer's recent orders
        $recentOrders = $customer->orders()
            ->with('quotation')
            ->latest('order_date')
            ->limit(5)
            ->get();
        
        // Get customer's recent quotations
        $recentQuotations = $customer->quotations()
            ->latest('quotation_date')
            ->limit(5)
            ->get();
            
        return view('customers.show', compact('customer', 'recentOrders', 'recentQuotations'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer)
    {
        // Parse metadata for easier form handling
        if (is_string($customer->metadata)) {
            $customer->metadata = json_decode($customer->metadata, true);
        }
        
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
            'contact_person' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'note' => 'nullable|string',
            'type' => 'nullable|string|in:individual,company',
            'code' => 'nullable|string|max:50|unique:customers,code,' . $customer->id,
            'credit_limit' => 'nullable|numeric|min:0',
            'industry' => 'nullable|string|max:100',
            'credit_term' => 'nullable|integer|min:0',
            'sales_region' => 'nullable|string|max:100',
        ]);
        
        // Prepare metadata
        $metadata = is_string($customer->metadata) 
            ? json_decode($customer->metadata, true) ?? []
            : ($customer->metadata ?? []);
            
        $metadata['industry'] = $validated['industry'] ?? null;
        $metadata['credit_term'] = $validated['credit_term'] ?? null;
        $metadata['sales_region'] = $validated['sales_region'] ?? null;
        
        // Remove keys from validated data that go into metadata
        unset($validated['industry']);
        unset($validated['credit_term']);
        unset($validated['sales_region']);
        
        // Add metadata to validated data
        $validated['metadata'] = json_encode($metadata);
        
        // Update customer
        $customer->update($validated);
        
        return redirect()->route('customers.show', $customer)
            ->with('success', 'ข้อมูลลูกค้าถูกอัปเดตเรียบร้อยแล้ว');
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer)
    {
        // Check if customer has related orders
        $hasOrders = $customer->orders()->exists();
        $hasQuotations = $customer->quotations()->exists();
        
        if ($hasOrders || $hasQuotations) {
            return redirect()->route('customers.show', $customer)
                ->with('error', 'ไม่สามารถลบลูกค้าที่มีการสั่งซื้อหรือใบเสนอราคาได้');
        }
        
        $customer->delete();
        
        return redirect()->route('customers.index')
            ->with('success', 'ลูกค้าถูกลบเรียบร้อยแล้ว');
    }
    
    /**
     * Export customers to CSV.
     */
    public function export()
    {
        $customers = Customer::all();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="customers.csv"',
        ];
        
        $callback = function() use ($customers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['รหัส', 'ชื่อ', 'อีเมล', 'โทรศัพท์', 'ที่อยู่', 'เลขประจำตัวผู้เสียภาษี', 'สถานะ']);
            
            foreach ($customers as $customer) {
                fputcsv($file, [
                    $customer->code,
                    $customer->name,
                    $customer->email,
                    $customer->phone,
                    $customer->address,
                    $customer->tax_id,
                    $customer->status,
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Display customer purchase history.
     */
    public function purchaseHistory(Customer $customer)
    {
        $orders = $customer->orders()
            ->with('quotation')
            ->orderBy('order_date', 'desc')
            ->paginate(10);
            
        return view('customers.purchase-history', compact('customer', 'orders'));
    }
}
