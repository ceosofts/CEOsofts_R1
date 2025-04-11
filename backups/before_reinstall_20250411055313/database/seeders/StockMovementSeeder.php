<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Inventory\Models\StockMovement;
use App\Domain\Inventory\Models\Product;
use App\Domain\Organization\Models\Company;

class StockMovementSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        foreach ($companies as $company) {
            $this->createStockMovementsForCompany($company->id);
        }
    }

    private function createStockMovementsForCompany($companyId)
    {
        $products = Product::where('company_id', $companyId)
                         ->where('is_service', false)
                         ->get();

        foreach ($products as $product) {
            // สร้างการรับสินค้าเริ่มต้น
            StockMovement::create([
                'company_id' => $companyId,
                'product_id' => $product->id,
                'type' => 'receive',
                'reference_type' => 'initial_stock',
                'reference_id' => $product->id, // ใช้ product id เป็น reference_id
                'quantity' => $product->stock_quantity,
                'before_quantity' => 0,
                'after_quantity' => $product->stock_quantity,
                'unit_cost' => $product->cost,
                'total_cost' => $product->cost * $product->stock_quantity,
                'location' => $product->location ?? 'main-warehouse',
                'notes' => 'สินค้าคงเหลือเริ่มต้น',
                'processed_by' => 1,
                'processed_at' => now(),
                'metadata' => json_encode([
                    'reason' => 'initial_stock',
                    'batch_no' => 'INIT-' . date('Ymd'),
                ])
            ]);

            // สร้างการเคลื่อนไหวสินค้าตัวอย่าง
            $movements = [
                [
                    'type' => 'receive',
                    'quantity' => rand(5, 20),
                    'reference_type' => 'purchase_order',
                    'notes' => 'รับสินค้าจากการสั่งซื้อ'
                ],
                [
                    'type' => 'issue',
                    'quantity' => rand(1, 5),
                    'reference_type' => 'sales_order',
                    'notes' => 'เบิกจ่ายสินค้าตามออเดอร์'
                ],
                [
                    'type' => 'adjust',
                    'quantity' => -1,
                    'reference_type' => 'stock_count',
                    'notes' => 'ปรับปรุงยอดตามการตรวจนับ'
                ]
            ];

            $currentStock = $product->stock_quantity;
            foreach ($movements as $movement) {
                $beforeQty = $currentStock;
                $afterQty = $currentStock + $movement['quantity'];
                $currentStock = $afterQty;

                StockMovement::create([
                    'company_id' => $companyId,
                    'product_id' => $product->id,
                    'type' => $movement['type'],
                    'reference_type' => $movement['reference_type'],
                    'reference_id' => rand(1000, 9999),
                    'quantity' => $movement['quantity'],
                    'before_quantity' => $beforeQty,
                    'after_quantity' => $afterQty,
                    'unit_cost' => $product->cost,
                    'total_cost' => $product->cost * abs($movement['quantity']),
                    'location' => $product->location ?? 'main-warehouse',
                    'notes' => $movement['notes'],
                    'processed_by' => 1,
                    'processed_at' => now()->subHours(rand(1, 72)),
                    'metadata' => json_encode([
                        'reason' => $movement['reference_type'],
                        'batch_no' => strtoupper($movement['type']) . '-' . date('Ymd') . '-' . rand(100, 999),
                    ])
                ]);
            }
        }
    }
}
