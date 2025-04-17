<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     */
    public function index(Request $request)
    {
        $query = Customer::query();
        
        // Apply search filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('tax_id', 'like', "%{$search}%");
            });
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        
        // Apply type filter
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        
        // Apply industry filter
        if ($request->filled('industry')) {
            $industry = $request->input('industry');
            $query->whereRaw("JSON_EXTRACT(metadata, '$.industry') LIKE ?", ["%{$industry}%"]);
        }
        
        // เรียงลำดับ (เปลี่ยนค่าเริ่มต้นเป็น 'id')
        $sortField = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'asc');
        $query->orderBy($sortField, $sortDirection);

        $customers = $query->paginate(15)->withQueryString();
        
        return view('customers.index', compact('customers'));
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
            'code' => 'nullable|string|max:50|unique:customers',
            'credit_limit' => 'nullable|numeric|min:0',
            'industry' => 'nullable|string|max:100',
            'credit_term' => 'nullable|integer|min:0',
            'sales_region' => 'nullable|string|max:100',
            // ฟิลด์ใหม่
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
        
        // Set company_id from session
        $validated['company_id'] = session('current_company_id', 1); // Default to 1 if not set
        
        // Convert is_supplier checkbox to boolean
        $validated['is_supplier'] = isset($validated['is_supplier']) ? true : false;
        
        // Generate customer code if not provided
        if (empty($validated['code'])) {
            $validated['code'] = 'CUST-' . str_pad(Customer::max('id') + 1, 5, '0', STR_PAD_LEFT);
        }
        
        // Prepare metadata
        $metadata = [
            'industry' => $validated['industry'] ?? null,
            'credit_term' => $validated['credit_term'] ?? null,
            'sales_region' => $validated['sales_region'] ?? null,
        ];
        
        // Prepare social media data as json
        $socialMedia = [];
        if (!empty($request->facebook)) $socialMedia['facebook'] = $request->facebook;
        if (!empty($request->line_official)) $socialMedia['line_official'] = $request->line_official;
        if (!empty($request->instagram)) $socialMedia['instagram'] = $request->instagram;
        if (!empty($request->twitter)) $socialMedia['twitter'] = $request->twitter;
        
        $validated['social_media'] = !empty($socialMedia) ? json_encode($socialMedia) : null;
        
        // Remove keys from validated data that go into metadata
        unset($validated['industry']);
        unset($validated['credit_term']);
        unset($validated['sales_region']);
        
        // Add metadata to validated data
        $validated['metadata'] = json_encode($metadata);
        
        // Create customer
        $customer = Customer::create($validated);
        
        return redirect()->route('customers.show', $customer)
            ->with('success', 'ลูกค้าถูกสร้างเรียบร้อยแล้ว');
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
