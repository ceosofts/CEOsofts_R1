<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Sales\Models\Receipt;
use App\Domain\Sales\Models\ReceiptItem;
use App\Domain\Organization\Models\Company;
use App\Domain\Inventory\Models\Product;

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
        $products = Product::where('company_id', $company->id)->get();

        if ($receipts->isEmpty() || $products->isEmpty()) {
            return;
        }

        foreach ($receipts as $receipt) {
            // สร้างรายการสินค้า 1-3 รายการต่อใบเสร็จ
            $itemCount = rand(1, 3);
            $totalAmount = 0;

            for ($i = 0; $i < $itemCount; $i++) {
                $product = $products->random();
                $quantity = rand(1, 5);
                $unitPrice = $product->price;
                $amount = $quantity * $unitPrice;
                $totalAmount += $amount;

                ReceiptItem::create([
                    'company_id' => $company->id,
                    'receipt_id' => $receipt->id,
                    'product_id' => $product->id,
                    'description' => $product->name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'unit' => $product->unit ?? 'piece',
                    'discount_type' => rand(0, 1) ? 'percentage' : 'amount',
                    'discount_amount' => rand(0, 1) ? rand(50, 200) : 0,
                    'amount' => $amount,
                    'tax_rate' => 7,
                    'tax_amount' => $amount * 0.07,
                    'metadata' => json_encode([
                        'original_price' => $product->price,
                        'sku' => $product->sku,
                        'product_code' => $product->code
                    ])
                ]);
            }

            // อัปเดตยอดรวมในใบเสร็จ
            $receipt->update([
                'total_amount' => $totalAmount,
                'tax_amount' => $totalAmount * 0.07,
                'grand_total' => $totalAmount + ($totalAmount * 0.07)
            ]);
        }
    }
}
