<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Inventory\Models\StockMovement;
use App\Domain\Inventory\Models\Product;
use App\Domain\Organization\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $this->createStockMovementsForCompany($company->id);
        }
    }

    private function createStockMovementsForCompany($companyId)
    {
        // ดึงสินค้าทุกรายการของบริษัทนี้
        $products = Product::where('company_id', $companyId)->get();

        foreach ($products as $product) {
            // ข้ามการสร้าง stock movement สำหรับสินค้าที่เป็นบริการ
            if (isset($product->is_service) && $product->is_service) {
                continue;
            }

            // สร้าง Stock Movement สำหรับสินค้าเริ่มต้น
            StockMovement::create([
                'company_id' => $companyId,
                'product_id' => $product->id,
                'type' => 'receive',
                'reference_type' => 'initial_stock',
                'reference_id' => 1,
                'quantity' => $product->stock_quantity ?? 10,
                'before_quantity' => 0, // กำหนดค่านี้อย่างชัดเจน
                'after_quantity' => $product->stock_quantity ?? 10,
                'unit_cost' => $product->cost ?? 20000.00,
                'total_cost' => ($product->cost ?? 20000.00) * ($product->stock_quantity ?? 10),
                'location' => $product->location ?? 'WH-A-01-01',
                'notes' => 'สินค้าคงเหลือเริ่มต้น',
                'processed_by' => 1, // admin user
                'processed_at' => now(),
                'status' => 'completed', // กำหนดสถานะเป็น completed
                'metadata' => json_encode([
                    'reason' => 'initial_stock',
                    'batch_no' => 'INIT-' . date('Ymd'),
                ])
            ]);
        }

        // ถ้าไม่มีสินค้า ให้สร้างตัวอย่าง stock movement
        if ($products->isEmpty()) {
            // สร้าง dummy stock movement เพื่อเป็นตัวอย่าง
            StockMovement::create([
                'company_id' => $companyId,
                'product_id' => 1, // ใช้ ID 1 (อาจต้องสร้างสินค้าในตอนเริ่มต้น)
                'type' => 'receive',
                'reference_type' => 'initial_stock',
                'reference_id' => 1,
                'quantity' => 10,
                'before_quantity' => 0, // กำหนดค่า before_quantity เป็น 0
                'after_quantity' => 10,
                'unit_cost' => 20000.00,
                'total_cost' => 200000,
                'location' => 'WH-A-01-01',
                'notes' => 'สินค้าคงเหลือเริ่มต้น',
                'processed_by' => 1, // admin user
                'processed_at' => now(),
                'status' => 'completed', // กำหนดสถานะเป็น completed
                'metadata' => json_encode([
                    'reason' => 'initial_stock',
                    'batch_no' => 'INIT-' . date('Ymd'),
                ])
            ]);
        }
    }
}
