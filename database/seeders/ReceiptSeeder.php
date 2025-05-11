<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Sales\Models\Receipt;
use App\Domain\Sales\Models\Invoice;
use App\Domain\Organization\Models\Company;
use Illuminate\Support\Str;

class ReceiptSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();
        
        foreach ($companies as $company) {
            $this->createReceiptsForCompany($company);
        }
    }

    private function createReceiptsForCompany($company)
    {
        // ดึงใบแจ้งหนี้ที่ชำระเงินแล้ว
        $invoices = Invoice::where('company_id', $company->id)
                         ->where('status', 'paid')
                         ->get();

        if ($invoices->isEmpty()) {
            return;
        }

        foreach ($invoices as $invoice) {
            // Generate a unique receipt number
            $receiptNumber = $this->generateUniqueReceiptNumber($company->id);
            
            // Check if a receipt with this number already exists
            $existingReceipt = Receipt::where('receipt_number', $receiptNumber)->first();
            
            // If it exists, try again with a new number
            if ($existingReceipt) {
                $receiptNumber = $this->generateUniqueReceiptNumber($company->id);
            }
            
            $receipt = [
                'company_id' => $company->id,
                'customer_id' => $invoice->customer_id,
                'invoice_id' => $invoice->id,
                'receipt_number' => $receiptNumber,
                'issue_date' => now(),
                'amount' => $invoice->total,
                'status' => 'completed',
                'created_by' => 1,
                'metadata' => json_encode([
                    'payment_method' => 'bank_transfer',
                    'payment_reference' => 'TR' . rand(100000, 999999),
                    'payment_date' => now()->format('Y-m-d'),
                    'notes' => 'ชำระเงินครบถ้วน',
                    'bank_name' => 'ธนาคารกสิกรไทย',
                    'bank_branch' => 'สาขาเซ็นทรัลเวิลด์',
                    'payment_channel' => 'mobile_banking'
                ])
            ];

            try {
                Receipt::create($receipt);
            } catch (\Exception $e) {
                // If we still get a unique constraint violation, try once more with a completely different approach
                if (str_contains($e->getMessage(), 'UNIQUE constraint failed')) {
                    $receipt['receipt_number'] = 'REC' . $company->id . date('Ymd') . uniqid();
                    Receipt::create($receipt);
                } else {
                    throw $e; // Re-throw if it's a different error
                }
            }
        }
    }
    
    /**
     * Generate a unique receipt number
     * Uses microsecond precision and a random component to ensure uniqueness
     */
    private function generateUniqueReceiptNumber($companyId)
    {
        $dateTime = now();
        $datePart = $dateTime->format('Ym');
        $microPart = substr($dateTime->format('u'), 0, 4); // Get first 4 digits of microseconds
        $randomPart = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        return 'REC' . $companyId . $datePart . $microPart . $randomPart;
    }
}
