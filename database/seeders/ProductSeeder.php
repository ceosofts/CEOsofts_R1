<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('th_TH');
        $companies = Company::all();

        foreach ($companies as $company) {
            $categories = ProductCategory::where('company_id', $company->id)->get();
            $units = Unit::where('company_id', $company->id)->get();
            
            if ($categories->count() > 0 && $units->count() > 0) {
                $this->createProductsForCompany($company->id);
            }
        }
    }

    /**
     * สร้างตัวอย่างสินค้าและบริการสำหรับแต่ละบริษัท
     */
    private function createProductsForCompany($companyId)
    {
        // ถ้าไม่มีหมวดหมู่สินค้าในบริษัทนี้ ให้สร้างหมวดหมู่ตัวอย่าง
        $this->ensureProductCategoriesExist($companyId);
        
        // ถ้าไม่มีหน่วยนับในบริษัทนี้ ให้สร้างหน่วยนับตัวอย่าง
        $this->ensureUnitsExist($companyId);
        
        // ดึงข้อมูลหมวดหมู่และหน่วยนับของบริษัทนี้
        $categories = ProductCategory::where('company_id', $companyId)->get();
        $units = Unit::where('company_id', $companyId)->get();
        
        // ถ้ามีหมวดหมู่และหน่วยนับแล้ว ให้สร้างสินค้าตัวอย่าง
        if ($categories->isNotEmpty() && $units->isNotEmpty()) {
            // สินค้าตัวอย่าง
            $products = [
                // สินค้าประเภทอิเล็กทรอนิกส์
                [
                    'company_id' => $companyId,
                    'name' => 'สมาร์ทโฟน รุ่น X Pro',
                    'code' => 'PRD-' . $companyId . '-0001',
                    'category_id' => $this->getCategoryId($categories, 'อิเล็กทรอนิกส์'),
                    'unit_id' => $this->getUnitId($units, 'เครื่อง'),
                    'price' => 15900.00,
                    'cost' => 12000.00,
                    'stock_quantity' => 20,
                    'min_stock' => 5,
                    'description' => 'สมาร์ทโฟนรุ่นล่าสุด หน้าจอ 6.5 นิ้ว กล้องหลัง 48MP แบตเตอรี่ 4500mAh',
                    'barcode' => '8850123456789',
                    'is_active' => true,
                    'is_service' => false,
                    'metadata' => json_encode([
                        'color' => ['Black', 'White', 'Blue'],
                        'warranty' => '1 year',
                        'os' => 'Android 13'
                    ])
                ],
                [
                    'company_id' => $companyId,
                    'name' => 'แล็ปท็อป รุ่น Y Series',
                    'code' => 'PRD-' . $companyId . '-0002',
                    'category_id' => $this->getCategoryId($categories, 'อิเล็กทรอนิกส์'),
                    'unit_id' => $this->getUnitId($units, 'เครื่อง'),
                    'price' => 32000.00,
                    'cost' => 26000.00,
                    'stock_quantity' => 10,
                    'min_stock' => 3,
                    'description' => 'แล็ปท็อปสำหรับทำงานและเล่นเกม CPU i7 RAM 16GB SSD 512GB',
                    'barcode' => '8850123456790',
                    'is_active' => true,
                    'is_service' => false,
                    'metadata' => json_encode([
                        'color' => ['Silver', 'Black'],
                        'warranty' => '2 years',
                        'os' => 'Windows 11'
                    ])
                ],
                
                // สินค้าประเภทเฟอร์นิเจอร์
                [
                    'company_id' => $companyId,
                    'name' => 'โต๊ะทำงานไม้สัก',
                    'code' => 'PRD-' . $companyId . '-0003',
                    'category_id' => $this->getCategoryId($categories, 'เฟอร์นิเจอร์'),
                    'unit_id' => $this->getUnitId($units, 'ตัว'),
                    'price' => 4500.00,
                    'cost' => 3200.00,
                    'stock_quantity' => 8,
                    'min_stock' => 2,
                    'description' => 'โต๊ะทำงานไม้สักแท้ ขนาด 120x60x75 ซม. แข็งแรงทนทาน',
                    'barcode' => '8850123456791',
                    'is_active' => true,
                    'is_service' => false,
                    'metadata' => json_encode([
                        'material' => 'ไม้สัก',
                        'size' => '120x60x75 ซม.',
                        'warranty' => '5 years'
                    ])
                ],
                [
                    'company_id' => $companyId,
                    'name' => 'เก้าอี้สำนักงาน รุ่น Ergonomic',
                    'code' => 'PRD-' . $companyId . '-0004',
                    'category_id' => $this->getCategoryId($categories, 'เฟอร์นิเจอร์'),
                    'unit_id' => $this->getUnitId($units, 'ตัว'),
                    'price' => 2900.00,
                    'cost' => 1800.00,
                    'stock_quantity' => 15,
                    'min_stock' => 5,
                    'description' => 'เก้าอี้สำนักงานแบบ Ergonomic รองรับสรีระ ปรับระดับได้ มีที่วางแขน',
                    'barcode' => '8850123456792',
                    'is_active' => true,
                    'is_service' => false,
                    'metadata' => json_encode([
                        'color' => ['Black', 'Gray'],
                        'material' => 'ผ้าตาข่ายและโครงเหล็ก',
                        'max_weight' => '120 kg'
                    ])
                ],
                
                // สินค้าประเภทเครื่องใช้ไฟฟ้า
                [
                    'company_id' => $companyId,
                    'name' => 'เครื่องปรับอากาศ 12000 BTU',
                    'code' => 'PRD-' . $companyId . '-0005',
                    'category_id' => $this->getCategoryId($categories, 'เครื่องใช้ไฟฟ้า'),
                    'unit_id' => $this->getUnitId($units, 'เครื่อง'),
                    'price' => 18500.00,
                    'cost' => 14000.00,
                    'stock_quantity' => 6,
                    'min_stock' => 2,
                    'description' => 'เครื่องปรับอากาศ Inverter ประหยัดไฟเบอร์ 5 ขนาด 12000 BTU',
                    'barcode' => '8850123456793',
                    'is_active' => true,
                    'is_service' => false,
                    'metadata' => json_encode([
                        'energy_rating' => '5 ดาว',
                        'warranty' => '5 years (compressor), 1 year (parts)',
                        'features' => ['Inverter', 'Air Purifier', 'WiFi Control']
                    ])
                ],
                
                // บริการ
                [
                    'company_id' => $companyId,
                    'name' => 'บริการติดตั้งเครื่องปรับอากาศ',
                    'code' => 'PRD-' . $companyId . '-0006',
                    'category_id' => $this->getCategoryId($categories, 'บริการ'),
                    'unit_id' => $this->getUnitId($units, 'ครั้ง'),
                    'price' => 1500.00,
                    'cost' => 1000.00,
                    'stock_quantity' => 0,
                    'min_stock' => 0,
                    'description' => 'บริการติดตั้งเครื่องปรับอากาศโดยช่างผู้เชี่ยวชาญ รวมค่าอุปกรณ์พื้นฐาน',
                    'barcode' => '',
                    'is_active' => true,
                    'is_service' => true,
                    'metadata' => json_encode([
                        'duration' => '3-4 hours',
                        'includes' => ['ท่อน้ำยา', 'สายไฟ', 'อุปกรณ์ติดตั้งพื้นฐาน'],
                        'warranty' => '6 months'
                    ])
                ],
                [
                    'company_id' => $companyId,
                    'name' => 'บริการซ่อมคอมพิวเตอร์',
                    'code' => 'PRD-' . $companyId . '-0007',
                    'category_id' => $this->getCategoryId($categories, 'บริการ'),
                    'unit_id' => $this->getUnitId($units, 'ครั้ง'),
                    'price' => 850.00,
                    'cost' => 500.00,
                    'stock_quantity' => 0,
                    'min_stock' => 0,
                    'description' => 'บริการตรวจเช็คและซ่อมคอมพิวเตอร์ แก้ไขปัญหาทั้งฮาร์ดแวร์และซอฟต์แวร์',
                    'barcode' => '',
                    'is_active' => true,
                    'is_service' => true,
                    'metadata' => json_encode([
                        'service_time' => '24-48 hours',
                        'warranty' => '30 days',
                        'pricing' => 'ราคาเริ่มต้น อาจมีค่าใช้จ่ายเพิ่มเติมหากมีการเปลี่ยนอะไหล่'
                    ])
                ],
                
                // สินค้าประเภทวัสดุสำนักงาน
                [
                    'company_id' => $companyId,
                    'name' => 'กระดาษถ่ายเอกสาร A4 80 แกรม',
                    'code' => 'PRD-' . $companyId . '-0008',
                    'category_id' => $this->getCategoryId($categories, 'วัสดุสำนักงาน'),
                    'unit_id' => $this->getUnitId($units, 'รีม'),
                    'price' => 135.00,
                    'cost' => 110.00,
                    'stock_quantity' => 50,
                    'min_stock' => 20,
                    'description' => 'กระดาษถ่ายเอกสารคุณภาพดี ขนาด A4 น้ำหนัก 80 แกรม 500 แผ่น/รีม',
                    'barcode' => '8850123456794',
                    'is_active' => true,
                    'is_service' => false,
                    'metadata' => json_encode([
                        'sheets' => 500,
                        'weight' => '80 gsm',
                        'size' => 'A4'
                    ])
                ],
                [
                    'company_id' => $companyId,
                    'name' => 'หมึกเครื่องพิมพ์ HP LaserJet',
                    'code' => 'PRD-' . $companyId . '-0009',
                    'category_id' => $this->getCategoryId($categories, 'วัสดุสำนักงาน'),
                    'unit_id' => $this->getUnitId($units, 'ตลับ'),
                    'price' => 1200.00,
                    'cost' => 950.00,
                    'stock_quantity' => 12,
                    'min_stock' => 5,
                    'description' => 'ตลับหมึกสำหรับเครื่องพิมพ์ HP LaserJet รุ่น 85A พิมพ์ได้ 1,600 แผ่น',
                    'barcode' => '8850123456795',
                    'is_active' => true,
                    'is_service' => false,
                    'metadata' => json_encode([
                        'compatible_models' => ['HP LaserJet Pro P1102', 'HP LaserJet Pro M1132'],
                        'yield' => '1,600 pages',
                        'color' => 'Black'
                    ])
                ],
                
                // สินค้าหมดสต็อก/ใกล้หมดสต็อก
                [
                    'company_id' => $companyId,
                    'name' => 'จอมอนิเตอร์ LED 24 นิ้ว',
                    'code' => 'PRD-' . $companyId . '-0010',
                    'category_id' => $this->getCategoryId($categories, 'อิเล็กทรอนิกส์'),
                    'unit_id' => $this->getUnitId($units, 'เครื่อง'),
                    'price' => 4900.00,
                    'cost' => 3800.00,
                    'stock_quantity' => 2,
                    'min_stock' => 3,
                    'description' => 'จอมอนิเตอร์ LED ขนาด 24 นิ้ว ความละเอียด Full HD 1920x1080',
                    'barcode' => '8850123456796',
                    'is_active' => true,
                    'is_service' => false,
                    'metadata' => json_encode([
                        'resolution' => '1920x1080',
                        'refresh_rate' => '75Hz',
                        'inputs' => ['HDMI', 'DisplayPort', 'VGA']
                    ])
                ],
                
                // สินค้าไม่ใช้งาน (inactive)
                [
                    'company_id' => $companyId,
                    'name' => 'แท็บเล็ต รุ่นเก่า',
                    'code' => 'PRD-' . $companyId . '-0011',
                    'category_id' => $this->getCategoryId($categories, 'อิเล็กทรอนิกส์'),
                    'unit_id' => $this->getUnitId($units, 'เครื่อง'),
                    'price' => 5500.00,
                    'cost' => 4200.00,
                    'stock_quantity' => 0,
                    'min_stock' => 2,
                    'description' => 'แท็บเล็ตรุ่นเก่า สต๊อกหมด ไม่ผลิตเพิ่มแล้ว',
                    'barcode' => '8850123456797',
                    'is_active' => false,
                    'is_service' => false,
                    'metadata' => json_encode([
                        'reason' => 'discontinued',
                        'replaced_by' => 'PRD-' . $companyId . '-0001'
                    ])
                ],
                
                // บริการไม่ใช้งาน
                [
                    'company_id' => $companyId,
                    'name' => 'บริการส่งสินค้าด่วนพิเศษ',
                    'code' => 'PRD-' . $companyId . '-0012',
                    'category_id' => $this->getCategoryId($categories, 'บริการ'),
                    'unit_id' => $this->getUnitId($units, 'ครั้ง'),
                    'price' => 350.00,
                    'cost' => 250.00,
                    'stock_quantity' => 0,
                    'min_stock' => 0,
                    'description' => 'บริการจัดส่งสินค้าด่วนพิเศษ ถึงภายใน 3 ชั่วโมง (เฉพาะในกรุงเทพฯ)',
                    'barcode' => '',
                    'is_active' => false,
                    'is_service' => true,
                    'metadata' => json_encode([
                        'reason' => 'temporarily unavailable',
                        'available_from' => '2025-05-01'
                    ])
                ]
            ];
            
            foreach ($products as $productData) {
                // เพิ่มการตรวจสอบและลบค่า status และ type ที่อาจหลงเหลืออยู่
                if (isset($productData['status'])) {
                    unset($productData['status']);
                }
                if (isset($productData['type'])) {
                    unset($productData['type']);
                }
                
                Product::firstOrCreate(
                    [
                        'company_id' => $companyId,
                        'code' => $productData['code']
                    ],
                    $productData
                );
            }
        }
    }

    /**
     * สร้างหมวดหมู่สินค้าตัวอย่างถ้ายังไม่มี
     */
    private function ensureProductCategoriesExist($companyId)
    {
        $categories = [
            'อิเล็กทรอนิกส์' => 'สินค้าประเภทอิเล็กทรอนิกส์ และอุปกรณ์ไอที',
            'เฟอร์นิเจอร์' => 'เฟอร์นิเจอร์สำนักงานและบ้าน',
            'เครื่องใช้ไฟฟ้า' => 'เครื่องใช้ไฟฟ้าทุกประเภท',
            'วัสดุสำนักงาน' => 'อุปกรณ์และวัสดุสำหรับใช้ในสำนักงาน',
            'บริการ' => 'บริการทุกประเภท'
        ];
        
        foreach ($categories as $name => $description) {
            ProductCategory::firstOrCreate(
                [
                    'company_id' => $companyId,
                    'name' => $name
                ],
                [
                    'company_id' => $companyId,
                    'name' => $name,
                    'description' => $description
                ]
            );
        }
    }

    /**
     * สร้างหน่วยนับตัวอย่างถ้ายังไม่มี
     */
    private function ensureUnitsExist($companyId)
    {
        $units = [
            'ชิ้น' => ['description' => 'หน่วยนับพื้นฐานสำหรับสินค้าทั่วไป'],
            'เครื่อง' => ['description' => 'สำหรับอุปกรณ์ไฟฟ้า/อิเล็กทรอนิกส์'],
            'ตัว' => ['description' => 'สำหรับเฟอร์นิเจอร์และของใหญ่'],
            'อัน' => ['description' => 'หน่วยนับทั่วไป'],
            'กล่อง' => ['description' => 'บรรจุภัณฑ์กล่อง'],
            'แพ็ค' => ['description' => 'บรรจุภัณฑ์แพ็ค'],
            'รีม' => ['description' => 'สำหรับกระดาษ'],
            'ตลับ' => ['description' => 'สำหรับหมึกพิมพ์'],
            'ชั่วโมง' => ['description' => 'หน่วยเวลา'],
            'ครั้ง' => ['description' => 'สำหรับบริการ'],
        ];
        
        foreach ($units as $name => $data) {
            // ตรวจสอบก่อนว่ามีหน่วยนี้อยู่แล้วหรือไม่
            $existingUnit = Unit::where('company_id', $companyId)
                ->where('name', $name)
                ->first();
                
            if ($existingUnit) {
                // ถ้ามีอยู่แล้ว ให้อัพเดทข้อมูลอื่นๆ แต่คงรหัสเดิมไว้
                $existingUnit->update([
                    'description' => $data['description'],
                    'symbol' => $name,
                    'is_active' => true,
                    'type' => 'standard'
                ]);
            } else {
                // ถ้ายังไม่มี ให้สร้างใหม่พร้อมสร้างรหัสใหม่
                Unit::create([
                    'company_id' => $companyId,
                    'name' => $name,
                    'description' => $data['description'],
                    'code' => Unit::generateUnitCode($companyId),
                    'symbol' => $name,
                    'is_active' => true,
                    'type' => 'standard'
                ]);
            }
        }
    }

    /**
     * ดึง ID ของหมวดหมู่จากชื่อ
     */
    private function getCategoryId($categories, $name)
    {
        $category = $categories->firstWhere('name', $name);
        return $category ? $category->id : $categories->first()->id;
    }

    /**
     * ดึง ID ของหน่วยนับจากชื่อ
     */
    private function getUnitId($units, $name)
    {
        $unit = $units->firstWhere('name', $name);
        return $unit ? $unit->id : $units->first()->id;
    }
}
