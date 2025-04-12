<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Inventory\Models\Product;
use App\Domain\Organization\Models\Company;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $this->createProductsForCompany($company->id);
        }
    }

    private function createProductsForCompany($companyId)
    {
        $products = [
            [
                'company_id' => $companyId,
                'code' => 'P001',
                'name' => 'คอมพิวเตอร์โน้ตบุ๊ค รุ่น X1',
                'description' => 'โน้ตบุ๊คสำหรับงานออฟฟิศทั่วไป',
                'category_id' => 1, // IT Equipment
                // 'unit_id' => 1, // ลบหรือคอมเมนต์บรรทัดนี้
                'barcode' => '8850001234567',
                'sku' => 'NB-X1-2024',
                'price' => 25000.00,
                'cost' => 20000.00,
                'stock_quantity' => 10,
                'min_stock' => 2,
                'max_stock' => 20,
                'location' => 'WH-A-01-01',
                'weight' => 2.5,
                'dimension' => json_encode([
                    'length' => 35,
                    'width' => 25,
                    'height' => 2.5
                ]),
                'is_active' => true,
                'metadata' => json_encode([
                    'brand' => 'TechBrand',
                    'warranty' => '1 year',
                    'specifications' => [
                        'processor' => 'Intel Core i5',
                        'ram' => '8GB',
                        'storage' => '512GB SSD'
                    ]
                ])
            ],
            [
                'company_id' => $companyId,
                'code' => 'S001',
                'name' => 'บริการติดตั้งระบบเครือข่าย',
                'description' => 'บริการติดตั้งและตั้งค่าระบบเครือข่ายองค์กร',
                'category_id' => 2, // Services
                // 'unit_id' => 2, // ลบหรือคอมเมนต์บรรทัดนี้
                'barcode' => null,
                'sku' => 'SVC-NET-2024',
                'price' => 15000.00,
                'cost' => 10000.00,
                'stock_quantity' => 0,
                'min_stock' => 0,
                'max_stock' => 0,
                'is_service' => true,
                'is_active' => true,
                'metadata' => json_encode([
                    'service_type' => 'installation',
                    'duration' => '1-3 days',
                    'includes' => [
                        'Network planning',
                        'Installation',
                        'Configuration',
                        'Testing'
                    ]
                ])
            ]
        ];

        foreach ($products as $product) {
            Product::firstOrCreate(
                [
                    'company_id' => $companyId,
                    'code' => $product['code']
                ],
                array_merge($product, [
                    'uuid' => Str::uuid()
                ])
            );
        }
    }
}
