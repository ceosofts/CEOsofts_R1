<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Sales\Models\Receipt;
use App\Domain\Sales\Models\Invoice;
use App\Domain\Organization\Models\Company;

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
            $receipt = [
                'company_id' => $company->id,
                'customer_id' => $invoice->customer_id,
                'invoice_id' => $invoice->id,
                'receipt_no' => 'REC' . date('Ym') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'receipt_date' => now(),
                'payment_method' => 'bank_transfer',
                'payment_reference' => 'TR' . rand(100000, 999999),
                'payment_date' => now(),
                'total_amount' => $invoice->grand_total,
                'status' => 'completed',
                'notes' => 'ชำระเงินครบถ้วน',
                'prepared_by' => 1,
                'metadata' => json_encode([
                    'bank_name' => 'ธนาคารกสิกรไทย',
                    'bank_branch' => 'สาขาเซ็นทรัลเวิลด์',
                    'payment_channel' => 'mobile_banking'
                ])
            ];

            Receipt::create($receipt);
        }
    }
}
