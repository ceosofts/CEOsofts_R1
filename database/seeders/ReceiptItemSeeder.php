<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Sales\Models\Receipt;
use App\Domain\Sales\Models\ReceiptItem;
use App\Domain\Organization\Models\Company;
use App\Domain\Sales\Models\Invoice;

class ReceiptItemSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        foreach ($companies as $company) {
            $this->createReceiptItemsForCompany($company);
        }
    }

    private function createReceiptItemsForCompany($company)
    {
        $receipts = Receipt::where('company_id', $company->id)->get();
        $invoices = Invoice::where('company_id', $company->id)->get();

        if ($receipts->isEmpty() || $invoices->isEmpty()) {
            return;
        }

        foreach ($receipts as $receipt) {
            // Link this receipt to between 1-3 invoices
            $itemCount = rand(1, min(3, $invoices->count()));
            $totalAmount = 0;
            $usedInvoices = $invoices->random($itemCount);
            $sortOrder = 1;

            foreach ($usedInvoices as $invoice) {
                $amount = $invoice->amount * (rand(50, 100) / 100); // Random partial payment between 50-100% of invoice
                $totalAmount += $amount;
                
                ReceiptItem::create([
                    'receipt_id' => $receipt->id,
                    'invoice_id' => $invoice->id,
                    'amount' => $amount,
                    'description' => "Payment for invoice {$invoice->invoice_number}",
                    'sort_order' => $sortOrder++,
                    'metadata' => json_encode([
                        'invoice_number' => $invoice->invoice_number,
                        'payment_method' => ['bank_transfer', 'cash', 'credit_card'][rand(0, 2)],
                        'payment_date' => now()->subDays(rand(0, 30))->format('Y-m-d')
                    ])
                ]);
            }

            // Update the total amount in the receipt
            $receipt->update([
                'amount' => $totalAmount
            ]);
        }
    }
}
