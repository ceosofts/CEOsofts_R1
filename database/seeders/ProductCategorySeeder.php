<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductCategory;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ล้างข้อมูลเดิมก่อน
        // DB::table('product_categories')->truncate();

        // ดึงข้อมูลบริษัททั้งหมด
        $companies = Company::all();

        if ($companies->isEmpty()) {
            $this->command->info('ไม่พบข้อมูลบริษัท กรุณาสร้างบริษัทก่อน');
            return;
        }

        // ประเภทสินค้าพื้นฐาน
        $categories = [
            [
                'code' => 'IT-HW',
                'name' => 'อุปกรณ์คอมพิวเตอร์',
                'description' => 'สินค้าประเภทอุปกรณ์คอมพิวเตอร์และฮาร์ดแวร์',
            ],
            [
                'code' => 'IT-SW',
                'name' => 'ซอฟต์แวร์',
                'description' => 'สินค้าประเภทซอฟต์แวร์และลิขสิทธิ์',
            ],
            [
                'code' => 'OFC',
                'name' => 'อุปกรณ์สำนักงาน',
                'description' => 'สินค้าประเภทอุปกรณ์สำนักงานและเครื่องใช้',
            ],
            [
                'code' => 'FURN',
                'name' => 'เฟอร์นิเจอร์',
                'description' => 'สินค้าประเภทเฟอร์นิเจอร์และของตกแต่ง',
            ],
            [
                'code' => 'STAT',
                'name' => 'เครื่องเขียน',
                'description' => 'สินค้าประเภทเครื่องเขียนและอุปกรณ์',
            ],
            [
                'code' => 'SVC-IT',
                'name' => 'บริการไอที',
                'description' => 'บริการด้านเทคโนโลยีสารสนเทศ',
            ],
            [
                'code' => 'SVC-CONSULT',
                'name' => 'บริการให้คำปรึกษา',
                'description' => 'บริการให้คำปรึกษาด้านธุรกิจและการจัดการ',
            ],
            [
                'code' => 'SVC-TRAIN',
                'name' => 'บริการฝึกอบรม',
                'description' => 'บริการฝึกอบรมและพัฒนาบุคลากร',
            ],
        ];

        foreach ($companies as $company) {
            foreach ($categories as $index => $category) {
                // คำนวณ ID รันตัวเลข 3 หลัก
                $nextId = DB::table('product_categories')->max('id') + $index + 1;
                $formattedId = str_pad($nextId, 3, '0', STR_PAD_LEFT);
                $code = $category['code'];
                
                // เพิ่ม formatted_code ลงใน metadata
                $metadata = [
                    'formatted_code' => "PC-{$formattedId}-{$company->id}-{$code}"
                ];
                
                ProductCategory::create([
                    'company_id' => $company->id,
                    'code' => $code,
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'is_active' => true,
                    'slug' => Str::slug($category['name']),
                    'level' => 0,
                    'path' => '',
                    'metadata' => json_encode($metadata)
                ]);
            }
        }

        $this->command->info('เพิ่มข้อมูลหมวดหมู่สินค้าเรียบร้อยแล้ว');
    }
}
