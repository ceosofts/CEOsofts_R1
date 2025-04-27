<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\ProductCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // ตรวจสอบว่ามีตาราง product_categories หรือไม่
            if (!Schema::hasTable('product_categories')) {
                echo "ไม่พบตาราง product_categories กรุณารัน migration ก่อน\n";
                return;
            }
            
            // ตรวจสอบว่ามีคอลัมน์ที่จำเป็นหรือไม่
            $hasLevel = Schema::hasColumn('product_categories', 'level');
            $hasPath = Schema::hasColumn('product_categories', 'path');
            
            if (!$hasLevel || !$hasPath) {
                echo "ตาราง product_categories ไม่มีคอลัมน์ level และ/หรือ path ที่จำเป็น\n";
                echo "กรุณารันคำสั่ง php artisan migrate เพื่อสร้างคอลัมน์เหล่านี้ก่อน\n";
                
                // แต่เราจะพยายามรันต่อโดยไม่ใช้ level และ path
                echo "กำลังพยายามสร้างข้อมูลโดยไม่ใช้คอลัมน์ที่ขาดหาย...\n";
            }
            
            $companies = Company::all();
            
            foreach ($companies as $company) {
                // สร้างหมวดหมู่สินค้าหลัก
                $this->createCategory($company->id, 'สินค้าทั่วไป', null, $hasLevel, $hasPath);
                $this->createCategory($company->id, 'วัตถุดิบ', null, $hasLevel, $hasPath);
                $this->createCategory($company->id, 'สินค้าสำเร็จรูป', null, $hasLevel, $hasPath);
                
                // หาหมวดหมู่ที่เพิ่งสร้าง
                $generalCategory = $this->findCategory($company->id, 'สินค้าทั่วไป');
                $rawMaterialCategory = $this->findCategory($company->id, 'วัตถุดิบ');
                $finishedGoodsCategory = $this->findCategory($company->id, 'สินค้าสำเร็จรูป');
                
                // สร้างหมวดหมู่ย่อย
                if ($generalCategory) {
                    $this->createCategory($company->id, 'เครื่องใช้สำนักงาน', $generalCategory->id, $hasLevel, $hasPath, $generalCategory);
                    $this->createCategory($company->id, 'อุปกรณ์อิเล็กทรอนิกส์', $generalCategory->id, $hasLevel, $hasPath, $generalCategory);
                }
                
                if ($rawMaterialCategory) {
                    $this->createCategory($company->id, 'โลหะ', $rawMaterialCategory->id, $hasLevel, $hasPath, $rawMaterialCategory);
                    $this->createCategory($company->id, 'พลาสติก', $rawMaterialCategory->id, $hasLevel, $hasPath, $rawMaterialCategory);
                }
                
                if ($finishedGoodsCategory) {
                    $this->createCategory($company->id, 'เฟอร์นิเจอร์', $finishedGoodsCategory->id, $hasLevel, $hasPath, $finishedGoodsCategory);
                    $this->createCategory($company->id, 'เครื่องใช้ไฟฟ้า', $finishedGoodsCategory->id, $hasLevel, $hasPath, $finishedGoodsCategory);
                }
            }
            
            echo "เพิ่มข้อมูลหมวดหมู่สินค้าเรียบร้อยแล้ว\n";
        } catch (\Exception $e) {
            echo "เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * สร้างหมวดหมู่ใหม่
     */
    private function createCategory($companyId, $name, $parentId = null, $hasLevel = true, $hasPath = true, $parentCategory = null)
    {
        try {
            // คำนวณค่า level และ path ถ้า column นั้นมีอยู่
            $level = 1;
            $path = null;
            
            if ($parentCategory && $hasLevel) {
                $level = $parentCategory->level + 1;
            }
            
            if ($parentCategory && $hasPath) {
                $path = $parentCategory->path ? $parentCategory->path . '/' . $parentCategory->id : $parentCategory->id;
            }
            
            // เตรียมข้อมูลที่ต้องมี
            $data = [
                'company_id' => $companyId,
                'name' => $name,
                'parent_id' => $parentId,
                'is_active' => true,
            ];
            
            // เพิ่มข้อมูล level และ path ถ้าคอลัมน์เหล่านั้นมีอยู่
            if ($hasLevel) {
                $data['level'] = $level;
            }
            
            if ($hasPath) {
                $data['path'] = $path;
            }
            
            return ProductCategory::updateOrCreate(
                ['company_id' => $companyId, 'name' => $name],
                $data
            );
        } catch (\Exception $e) {
            echo "เกิดข้อผิดพลาดในการสร้างหมวดหมู่ {$name}: " . $e->getMessage() . "\n";
            return null;
        }
    }
    
    /**
     * ค้นหาหมวดหมู่ตามชื่อ
     */
    private function findCategory($companyId, $name)
    {
        return ProductCategory::where('company_id', $companyId)
                              ->where('name', $name)
                              ->first();
    }
}
