<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Sales\Models\Invoice;
use App\Domain\Sales\Models\Order;
use App\Domain\Sales\Models\Customer;
use App\Domain\Organization\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
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
            
            // สร้างเลขที่ใบแจ้งหนี้ที่ไม่ซ้ำกัน
            $invoiceNumber = 'INV' . date('Ym') . str_pad(mt_rand(100, 999) . $i, 6, '0', STR_PAD_LEFT);
            
            // สร้างใบแจ้งหนี้
            $invoice = [
                'company_id' => $company->id,
                'customer_id' => $customer->id,
                'order_id' => null, // ไม่มีออร์เดอร์
                'invoice_number' => $invoiceNumber,
                'invoice_date' => Carbon::now()->subDays(rand(0, 60)),
                'due_date' => Carbon::now()->addDays(rand(1, 30)),
                'status' => rand(0, 10) > 7 ? 'paid' : 'pending', // สุ่มสถานะบ้าง
                'currency' => 'THB',
                'discount_type' => 'fixed',
                'discount_amount' => 0,
                'tax_inclusive' => false,
                'tax_rate' => $taxRate,
                'subtotal' => $subtotal,
                'total_discount' => 0,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'amount_paid' => rand(0, 10) > 7 ? $total : 0, // ถ้าสถานะเป็น paid ให้ใส่ยอดจ่ายเต็ม
                'amount_due' => rand(0, 10) > 7 ? 0 : $total, // ถ้าสถานะเป็น paid ให้ใส่ยอดค้างจ่ายเป็น 0
                'notes' => 'ใบแจ้งหนี้ตัวอย่าง - โปรดชำระเงินภายในวันที่กำหนด',
                'terms' => 'ภายใน 30 วัน',
                'created_by' => 1,
                'metadata' => json_encode([
                    'sample_invoice' => true,
                    'products' => $invoiceProducts,
                    'payment_methods' => ['transfer', 'credit_card', 'check'],
                    'notice' => 'กรุณาแจ้งการชำระเงินทุกครั้ง'
                ])
            ];
            
            try {
                Invoice::create($invoice);
            } catch (\Exception $e) {
                // หากเกิดข้อผิดพลาด เช่น duplicated invoice number
                if ($e instanceof \Illuminate\Database\UniqueConstraintViolationException) {
                    // สร้างเลขที่ใหม่ที่มั่นใจว่าไม่ซ้ำ
                    $uniqueID = uniqid('', true);
                    $invoice['invoice_number'] = 'INV' . date('Ym') . substr(md5($uniqueID . $i), 0, 6);
                    
                    try {
                        Invoice::create($invoice);
                    } catch (\Exception $innerEx) {
                        Log::error("Failed to create sample invoice after retry: " . $innerEx->getMessage());
                    }
                } else {
                    Log::error("Error creating sample invoice: " . $e->getMessage());
                }
            }
        }
        
        Log::info("Created {$numInvoices} sample invoices for company ID: {$company->id}");
    }

    private function createInvoicesForCompany($company)
    {
        // หาออเดอร์ที่ออกใบแจ้งหนี้ได้
        $orders = Order::where('company_id', $company->id)
            ->where('status', 'approved')
            ->orWhere('status', 'delivered')
            ->get();

        if ($orders->isEmpty()) {
            return;
        }

        foreach ($orders as $index => $order) {
            // เพิ่ม index เพื่อหลีกเลี่ยงเลขที่ใบแจ้งหนี้ซ้ำกัน
            $invoiceNumber = 'INV' . date('Ym') . str_pad(mt_rand(100, 999) . $index, 6, '0', STR_PAD_LEFT);

            $invoice = [
                'company_id' => $company->id,
                'customer_id' => $order->customer_id,
                'order_id' => $order->id,
                'invoice_number' => $invoiceNumber,
                'invoice_date' => Carbon::now(),
                'due_date' => Carbon::now()->addDays(30),
                'status' => 'pending',
                'currency' => 'THB',
                'discount_type' => 'fixed',
                'discount_amount' => 0,
                'tax_inclusive' => false,
                'tax_rate' => 7,
                'subtotal' => $order->subtotal ?? 5000,
                'total_discount' => 0,
                'tax_amount' => ($order->subtotal ?? 5000) * 0.07,
                'total' => ($order->subtotal ?? 5000) * 1.07,
                'amount_paid' => 0,
                'amount_due' => ($order->subtotal ?? 5000) * 1.07,
                'notes' => 'โปรดชำระเงินภายในวันที่กำหนด',
                'terms' => 'ภายใน 30 วัน',
                'created_by' => 1,
                'metadata' => json_encode([
                    'original_order_number' => $order->order_number,
                    'payment_methods' => ['transfer', 'credit_card', 'check'],
                    'notice' => 'กรุณาแจ้งการชำระเงินทุกครั้ง'
                ])
            ];

            try {
                Invoice::create($invoice);
            } catch (\Exception $e) {
                // หากเกิดข้อผิดพลาด เช่น duplicated invoice number
                if ($e instanceof \Illuminate\Database\UniqueConstraintViolationException) {
                    // สร้างเลขที่ใหม่ที่มั่นใจว่าไม่ซ้ำ
                    $uniqueID = uniqid('', true);
                    $invoice['invoice_number'] = 'INV' . date('Ym') . substr(md5($uniqueID . $index), 0, 6);

                    try {
                        Invoice::create($invoice);
                    } catch (\Exception $innerEx) {
                        Log::error("Failed to create invoice after retry: " . $innerEx->getMessage());
                    }
                } else {
                    Log::error("Error creating invoice: " . $e->getMessage());
                }
            }
        }
    }
}
