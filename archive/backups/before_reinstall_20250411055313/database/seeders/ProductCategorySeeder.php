<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Inventory\Models\ProductCategory;
use App\Domain\Organization\Models\Company;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        foreach ($companies as $company) {
            $this->createCategoriesForCompany($company->id);
        }
    }

    private function createCategoriesForCompany($companyId)
    {
        $categories = [
            [
                'company_id' => $companyId,
                'name' => 'คอมพิวเตอร์และอุปกรณ์',
                'code' => 'COM',
                'description' => 'คอมพิวเตอร์ โน้ตบุ๊ค และอุปกรณ์ต่อพ่วง',
                'parent_id' => null,
                'is_active' => true,
                'metadata' => json_encode([
                    'icon' => 'computer',
                    'display_order' => 1,
                    'show_in_pos' => true
                ])
            ],
            [
                'company_id' => $companyId,
                'name' => 'อุปกรณ์เน็ตเวิร์ค',
                'code' => 'NET',
                'description' => 'อุปกรณ์เครือข่ายและการสื่อสาร',
                'parent_id' => null,
                'is_active' => true,
                'metadata' => json_encode([
                    'icon' => 'router',
                    'display_order' => 2,
                    'show_in_pos' => true
                ])
            ],
            [
                'company_id' => $companyId,
                'name' => 'ซอฟต์แวร์',
                'code' => 'SW',
                'description' => 'ซอฟต์แวร์และลิขสิทธิ์',
                'parent_id' => null,
                'is_active' => true,
                'metadata' => json_encode([
                    'icon' => 'code',
                    'display_order' => 3,
                    'show_in_pos' => true
                ])
            ],
            [
                'company_id' => $companyId,
                'name' => 'บริการ IT',
                'code' => 'SVC',
                'description' => 'บริการด้านไอที',
                'parent_id' => null,
                'is_active' => true,
                'metadata' => json_encode([
                    'icon' => 'support',
                    'display_order' => 4,
                    'show_in_pos' => false
                ])
            ]
        ];

        foreach ($categories as $category) {
            ProductCategory::firstOrCreate(
                [
                    'company_id' => $companyId,
                    'code' => $category['code']
                ],
                array_merge($category, [
                    'slug' => Str::slug($category['name'])
                ])
            );
        }
    }
}
