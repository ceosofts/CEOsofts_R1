<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Unit; // เปลี่ยนจาก App\Domain\Inventory\Models\Unit เป็น App\Models\Unit
use Illuminate\Support\Facades\Log;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        // ลบข้อมูลเดิมทั้งหมดก่อน seed ใหม่
        \App\Models\Unit::truncate();

        $companies = Company::all();
        foreach ($companies as $company) {
            $this->createUnitsForCompany($company);
        }
    }

    private function createUnitsForCompany($company): void
    {
        Log::info("Starting to create units for company {$company->id}: {$company->name}");
        
        // หน่วยชิ้น (เป็นหน่วยพื้นฐาน)
        $data = [
            'code' => Unit::generateUnitCode($company->id),
            'symbol' => 'ชิ้น',
            'base_unit_id' => null,
            'conversion_factor' => 1,
            'is_default' => true,
            'is_active' => true,
            'type' => 'standard',
            'category' => 'quantity',
            'description' => 'หน่วยนับพื้นฐานสำหรับสินค้าทั่วไป'
        ];
        
        Log::info("Creating unit with data: " . json_encode($data));
        
        $pcsUnit = Unit::updateOrCreate(
            [
                'company_id' => $company->id,
                'name' => 'ชิ้น'
            ],
            $data
        );
        
        Log::info("Created/Updated piece unit: " . json_encode($pcsUnit->toArray()));
        
        // หน่วยโหล (12 ชิ้น)
        Unit::updateOrCreate(
            [
                'company_id' => $company->id,
                'name' => 'โหล'
            ],
            [
                'code' => Unit::generateUnitCode($company->id),
                'symbol' => 'โหล',
                'base_unit_id' => $pcsUnit->id,
                'conversion_factor' => 12,
                'is_default' => false,
                'is_active' => true,
                'type' => 'derived',
                'category' => 'quantity',
                'description' => '1 โหล = 12 ชิ้น สำหรับการสั่งซื้อจำนวนมาก'
            ]
        );

        // หน่วยแพ็ค (6 ชิ้น)
        Unit::updateOrCreate(
            [
                'company_id' => $company->id,
                'name' => 'แพ็ค'
            ],
            [
                'code' => Unit::generateUnitCode($company->id),
                'symbol' => 'แพ็ค',
                'base_unit_id' => $pcsUnit->id,
                'conversion_factor' => 6,
                'is_default' => false,
                'is_active' => true,
                'type' => 'derived',
                'category' => 'quantity',
                'description' => '1 แพ็ค = 6 ชิ้น สำหรับการบรรจุภัณฑ์ขนาดกลาง'
            ]
        );

        // หน่วยกิโลกรัม
        $kgUnit = Unit::updateOrCreate(
            [
                'company_id' => $company->id,
                'name' => 'กิโลกรัม'
            ],
            [
                'code' => Unit::generateUnitCode($company->id),
                'symbol' => 'กก.',
                'base_unit_id' => null,
                'conversion_factor' => 1,
                'is_default' => false,
                'is_active' => true,
                'type' => 'standard',
                'category' => 'weight',
                'description' => 'หน่วยน้ำหนักมาตรฐาน สำหรับสินค้าที่ขายตามน้ำหนัก'
            ]
        );

        // หน่วยกรัม
        Unit::updateOrCreate(
            [
                'company_id' => $company->id,
                'name' => 'กรัม'
            ],
            [
                'code' => Unit::generateUnitCode($company->id),
                'symbol' => 'ก.',
                'base_unit_id' => $kgUnit->id,
                'conversion_factor' => 0.001,
                'is_default' => false,
                'is_active' => true,
                'type' => 'derived',
                'category' => 'weight',
                'description' => '1 กรัม = 0.001 กิโลกรัม สำหรับสินค้าที่มีน้ำหนักเบา'
            ]
        );

        // เพิ่มหน่วยอื่นๆ
        // $unitNames = [
        //     'เครื่อง' => 'standard',
        //     'ตัว' => 'standard',
        //     'อัน' => 'standard',
        //     'กล่อง' => 'standard',
        //     'รีม' => 'standard',
        //     'ตลับ' => 'standard',
        //     'ชั่วโมง' => 'standard',
        //     'ครั้ง' => 'standard',
        // ];

        // foreach ($unitNames as $name => $type) {
        //     Unit::updateOrCreate(
        //         [
        //             'company_id' => $company->id,
        //             'code' => Unit::generateUnitCode($company->id) // ตรวจสอบว่าการเรียกใช้ถูกต้อง
        //         ],
        //         [
        //             'name' => $name,
        //             'symbol' => null,
        //             'base_unit_id' => null,
        //             'conversion_factor' => 1,
        //             'is_default' => false,
        //             'is_active' => true,
        //             'type' => $type,
        //             'category' => null,
        //             'description' => null,
        //         ]
        //     );
        // }
    }
}
