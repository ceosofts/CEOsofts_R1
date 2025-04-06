<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Sales\Models\Invoice;
use App\Domain\Sales\Models\Order;
use App\Domain\Sales\Models\Customer;
use App\Domain\Organization\Models\Company;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        
        foreach ($companies as $company) {
            $this->createInvoicesForCompany($company);
        }
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
                        \Log::error("Failed to create invoice after retry: " . $innerEx->getMessage());
                    }
                } else {
                    \Log::error("Error creating invoice: " . $e->getMessage());
                }
            }
        }
    }
}
