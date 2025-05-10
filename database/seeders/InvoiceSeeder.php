<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Sales\Models\Invoice;
use App\Domain\Sales\Models\Order;
use App\Domain\Sales\Models\Customer;
use App\Domain\Organization\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        echo "เริ่มสร้างข้อมูลใบแจ้งหนี้...\n";
        $companies = Company::all();

        if ($companies->isEmpty()) {
            // สร้างบริษัทตัวอย่าง 1 บริษัทหากไม่มีบริษัทในระบบ
            $company = Company::create([
                'name' => 'บริษัทตัวอย่าง จำกัด',
                'tax_id' => '1234567890123',
                'address' => '123 ถนนตัวอย่าง แขวงตัวอย่าง เขตตัวอย่าง กรุงเทพฯ 10000',
                'phone' => '02-123-4567',
                'email' => 'example@company.com',
            ]);
            $companies = collect([$company]);
        }

        // ดึงข้อมูลลูกค้าหรือสร้างลูกค้าตัวอย่าง
        $customers = Customer::all();
        if ($customers->isEmpty()) {
            // สร้างลูกค้าตัวอย่าง 5 คนหากไม่มีลูกค้าในระบบ
            $customerData = [
                [
                    'name' => 'ลูกค้าตัวอย่าง 1',
                    'email' => 'customer1@example.com',
                    'phone' => '081-111-1111',
                ],
                [
                    'name' => 'ลูกค้าตัวอย่าง 2',
                    'email' => 'customer2@example.com',
                    'phone' => '082-222-2222',
                ],
                [
                    'name' => 'ลูกค้าตัวอย่าง 3',
                    'email' => 'customer3@example.com',
                    'phone' => '083-333-3333',
                ],
                [
                    'name' => 'ลูกค้าตัวอย่าง 4',
                    'email' => 'customer4@example.com',
                    'phone' => '084-444-4444',
                ],
                [
                    'name' => 'ลูกค้าตัวอย่าง 5',
                    'email' => 'customer5@example.com',
                    'phone' => '085-555-5555',
                ],
            ];

            foreach ($customerData as $data) {
                // สร้างลูกค้าแต่ละคนสำหรับแต่ละบริษัท
                foreach ($companies as $company) {
                    $data['company_id'] = $company->id;
                    Customer::create($data);
                }
            }
            
            // ดึงลูกค้าที่เพิ่งสร้างทั้งหมด
            $customers = Customer::all();
        }

        foreach ($companies as $company) {
            // สร้างใบแจ้งหนี้ตัวอย่างโดยตรงโดยไม่ต้องพึ่งพาออร์เดอร์
            $this->createSampleInvoicesForCompany($company, $customers->where('company_id', $company->id));
            
            // ยังคงเรียกใช้เมธอดเดิมเผื่อกรณีที่มีออร์เดอร์จริงในระบบ
            $this->createInvoicesForCompany($company);
        }
        
        echo "สร้างข้อมูลใบแจ้งหนี้เสร็จสิ้น\n";
    }

    /**
     * สร้างใบแจ้งหนี้ตัวอย่างโดยตรงโดยไม่ต้องพึ่งพาออร์เดอร์
     */
    private function createSampleInvoicesForCompany($company, $customers)
    {
        // สร้างใบแจ้งหนี้ตัวอย่าง 10 รายการต่อบริษัท
        $numInvoices = 10;
        
        // ตรวจสอบว่ามีลูกค้าหรือไม่
        if ($customers->isEmpty()) {
            Log::warning("No customers found for company ID: {$company->id}");
            return;
        }
        
        // สร้างสินค้าตัวอย่างเพื่อใช้ในใบแจ้งหนี้
        $products = [
            ['name' => 'สินค้าตัวอย่าง A', 'price' => 1000],
            ['name' => 'สินค้าตัวอย่าง B', 'price' => 2500],
            ['name' => 'สินค้าตัวอย่าง C', 'price' => 3200],
            ['name' => 'สินค้าตัวอย่าง D', 'price' => 4500],
            ['name' => 'สินค้าตัวอย่าง E', 'price' => 5800],
        ];
        
        for ($i = 0; $i < $numInvoices; $i++) {
            try {
                // เริ่ม transaction
                DB::beginTransaction();
                
                // สุ่มเลือกลูกค้าสำหรับใบแจ้งหนี้
                $customer = $customers->random();
                
                // สร้างสินค้าสุ่ม 1-3 รายการสำหรับใบแจ้งหนี้นี้
                $invoiceProducts = [];
                $numProducts = rand(1, 3);
                $subtotal = 0;
                
                for ($j = 0; $j < $numProducts; $j++) {
                    $product = $products[array_rand($products)];
                    $quantity = rand(1, 5);
                    $price = $product['price'];
                    $lineTotal = $price * $quantity;
                    $subtotal += $lineTotal;
                    
                    $invoiceProducts[] = [
                        'name' => $product['name'],
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $lineTotal
                    ];
                }
                
                // คำนวณภาษีและยอดรวม
                $taxRate = 7;
                $taxAmount = $subtotal * ($taxRate / 100);
                $total = $subtotal + $taxAmount;
                $totalDiscount = 0;
                
                // สร้างเลขที่ใบแจ้งหนี้ที่ไม่ซ้ำกัน โดยใช้รูปแบบ INVxx + ปี + เดือน + เลขลำดับ
                $companyCode = str_pad($company->id, 2, '0', STR_PAD_LEFT);
                $shortYear = date('y');
                $month = date('m');
                
                // หาเลขลำดับล่าสุดของใบแจ้งหนี้ในเดือนปัจจุบัน (เช็คทั้งหมดไม่เฉพาะบริษัทนี้)
                $latestInvoice = Invoice::where('invoice_number', 'like', "INV%" . $shortYear . $month . "%")
                    ->orderBy('id', 'desc')
                    ->first();
                
                $sequenceNumber = 1;
                if ($latestInvoice) {
                    // ดึงเลขลำดับจากเลขที่ใบแจ้งหนี้ล่าสุด (4 ตัวสุดท้าย)
                    $currentSequence = (int)substr($latestInvoice->invoice_number, -4);
                    $sequenceNumber = $currentSequence + 1;
                }
                
                // สร้างเลขที่ใบแจ้งหนี้ใหม่ - ใช้เลขบริษัท+ปี+เดือน+เลขลำดับ
                $invoiceNumber = "INV{$companyCode}{$shortYear}{$month}" . str_pad($sequenceNumber, 4, '0', STR_PAD_LEFT);
                
                // สร้างใบแจ้งหนี้
                $invoice = Invoice::create([
                    'company_id' => $company->id,
                    'customer_id' => $customer->id,
                    'order_id' => null, // ไม่มีออร์เดอร์
                    'invoice_number' => $invoiceNumber,
                    'invoice_date' => Carbon::now()->subDays(rand(0, 60)),
                    'due_date' => Carbon::now()->addDays(rand(1, 30)),
                    'status' => rand(0, 10) > 7 ? 'paid' : 'draft',
                    'currency' => 'THB',
                    'exchange_rate' => 1.00,
                    'discount_type' => 'fixed',
                    'discount_amount' => 0,
                    'tax_inclusive' => false,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'subtotal' => $subtotal,
                    'total_discount' => 0,
                    'total' => $total,
                    'amount_paid' => rand(0, 10) > 7 ? $total : 0, // ถ้าสถานะเป็น paid ให้จ่ายเต็มจำนวน
                    'amount_due' => rand(0, 10) > 7 ? 0 : $total, // ถ้าสถานะเป็น paid ให้ค้างจ่ายเป็น 0
                    'notes' => 'ใบแจ้งหนี้ตัวอย่าง - โปรดชำระเงินภายในวันที่กำหนด',
                    'terms' => 'เงื่อนไขการชำระเงิน: ชำระภายใน 15 วันนับจากวันที่ออกใบแจ้งหนี้',
                    'created_by' => 1,
                    'metadata' => json_encode([
                        'source' => 'system_seeder',
                        'generated_at' => now()->toDateTimeString()
                    ])
                ]);
                
                // สร้างรายการสินค้าในใบแจ้งหนี้
                foreach ($invoiceProducts as $item) {
                    DB::table('invoice_items')->insert([
                        'invoice_id' => $invoice->id,
                        'description' => $item['name'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'total' => ($item['quantity'] * $item['price']) * (1 + ($taxRate / 100)),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                
                // ยืนยัน transaction
                DB::commit();
                echo "  สร้างใบแจ้งหนี้ {$invoiceNumber} สำเร็จ\n";
            } catch (\Exception $e) {
                // กรณีเกิด error ให้ rollback transaction
                DB::rollBack();
                
                echo "  เกิดข้อผิดพลาดในการสร้างใบแจ้งหนี้: " . $e->getMessage() . "\n";
                Log::error("Error creating sample invoice: " . $e->getMessage());
            }
        }
        
        Log::info("Created {$numInvoices} sample invoices for company ID: {$company->id}");
    }

    private function createInvoicesForCompany($company)
    {
        // หาออเดอร์ที่ออกใบแจ้งหนี้ได้
        $orders = Order::where('company_id', $company->id)
            ->whereIn('status', ['approved', 'delivered', 'shipped'])
            ->get();

        if ($orders->isEmpty()) {
            return;
        }

        echo "พบออร์เดอร์ที่สามารถสร้างใบแจ้งหนี้: " . $orders->count() . " รายการ\n";

        foreach ($orders as $order) {
            try {
                // เริ่ม transaction
                DB::beginTransaction();
                
                // สร้างเลขที่ใบแจ้งหนี้ที่ไม่ซ้ำกัน
                $invoiceNumber = $this->generateUniqueInvoiceNumber($company);

                // สร้างใบแจ้งหนี้
                $subtotal = $order->subtotal ?? 5000;
                $taxRate = 7;
                $taxAmount = $subtotal * ($taxRate / 100);
                $total = $subtotal + $taxAmount;
                $totalDiscount = 0;
                
                $invoice = Invoice::create([
                    'company_id' => $company->id,
                    'customer_id' => $order->customer_id,
                    'order_id' => $order->id,
                    'invoice_number' => $invoiceNumber,
                    'invoice_date' => Carbon::now(),
                    'due_date' => Carbon::now()->addDays(30),
                    'status' => 'draft', // แก้ไขค่า status จาก pending เป็น draft
                    'currency' => 'THB',
                    'exchange_rate' => 1.00,
                    'discount_type' => 'fixed',
                    'discount_amount' => 0,
                    'tax_inclusive' => false,
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'subtotal' => $subtotal,
                    'total_discount' => 0,
                    'total' => $total,
                    'amount_paid' => 0,
                    'amount_due' => $total,
                    'notes' => 'โปรดชำระเงินภายในวันที่กำหนด',
                    'terms' => 'เงื่อนไขการชำระเงิน: ชำระภายใน 30 วันนับจากวันที่ออกใบแจ้งหนี้',
                    'created_by' => 1,
                    'metadata' => json_encode([
                        'source' => 'system_seeder',
                        'generated_at' => now()->toDateTimeString()
                    ])
                ]);
                
                // สร้างรายการสินค้าในใบแจ้งหนี้จากรายการใน order
                foreach ($order->items as $orderItem) {
                    DB::table('invoice_items')->insert([
                        'invoice_id' => $invoice->id,
                        'product_id' => $orderItem->product_id,
                        'description' => $orderItem->description ?? 'สินค้าจากออร์เดอร์',
                        'quantity' => $orderItem->quantity,
                        'unit_price' => $orderItem->unit_price,
                        'total' => ($orderItem->quantity * $orderItem->unit_price) * (1 + ($taxRate / 100)),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
                
                // อัพเดทใบสั่งขายให้แสดงว่ามีใบแจ้งหนี้แล้ว
                if (Schema::hasColumn('orders', 'has_invoice')) {
                    $order->has_invoice = true;
                    $order->save();
                }
                
                // ยืนยัน transaction
                DB::commit();
                
                echo "  สร้างใบแจ้งหนี้ {$invoiceNumber} สำหรับออร์เดอร์ {$order->order_number} สำเร็จ\n";
            } catch (\Exception $e) {
                // กรณีเกิด error ให้ rollback transaction
                DB::rollBack();
                
                echo "  เกิดข้อผิดพลาดในการสร้างใบแจ้งหนี้จากออร์เดอร์: " . $e->getMessage() . "\n";
                Log::error("Error creating invoice from order: " . $e->getMessage());
            }
        }
    }

    /**
     * สร้างเลขที่ใบแจ้งหนี้ที่ไม่ซ้ำกัน
     */
    private function generateUniqueInvoiceNumber($company)
    {
        // Create a truly unique invoice number with company code, date parts, and a unique random suffix
        $companyCode = str_pad($company->id, 2, '0', STR_PAD_LEFT);
        $year = date('y');
        $month = date('m');
        
        // Add microseconds and a random number to ensure uniqueness
        $uniqueSuffix = substr(microtime(true) * 10000, -4) . rand(1000, 9999);
        
        // Format: INV + company_id(2) + year(2) + month(2) + unique random number
        $invoiceNumber = "INV{$companyCode}{$year}{$month}" . substr($uniqueSuffix, -4);
        
        // Double-check that this invoice number is truly unique in the database
        while (Invoice::where('invoice_number', $invoiceNumber)->exists()) {
            $uniqueSuffix = substr(microtime(true) * 10000, -4) . rand(1000, 9999);
            $invoiceNumber = "INV{$companyCode}{$year}{$month}" . substr($uniqueSuffix, -4);
        }
        
        return $invoiceNumber;
    }
}
