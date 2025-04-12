<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Domain\Inventory\Models\Unit; // แก้ไขเป็น namespace ที่ถูกต้อง

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        foreach ($companies as $company) {
            $this->createUnitsForCompany($company);
        }
    }

    private function createUnitsForCompany($company): void
    {
        // หน่วยชิ้น (เป็นหน่วยพื้นฐาน)
        $pcsUnit = Unit::firstOrCreate(
            [
                'company_id' => $company->id,
                'code' => 'PCS'
            ],
            [
                'name' => 'ชิ้น',
                'symbol' => 'ชิ้น',
                'base_unit_id' => null, // ไม่มี base unit เพราะเป็นหน่วยพื้นฐานเอง
                'conversion_factor' => 1,
                // 'is_base_unit' => true, // ลบออกหรือแทนที่ด้วย field ที่มีอยู่จริง
                'is_default' => true, // ใช้ is_default แทน
                'is_active' => true,
                'type' => 'standard', // เพิ่ม type ที่มีในตาราง
                'category' => 'quantity' // เพิ่ม category ที่มีในตาราง
            ]
        );

        // หน่วยโหล (12 ชิ้น)
        Unit::firstOrCreate(
            [
                'company_id' => $company->id,
                'code' => 'DOZ'
            ],
            [
                'name' => 'โหล',
                'symbol' => 'โหล',
                'base_unit_id' => $pcsUnit->id, // อ้างอิงถึงหน่วยชิ้น
                'conversion_factor' => 12, // 1 โหล = 12 ชิ้น
                // 'is_base_unit' => false, // ลบออก
                'is_default' => false,
                'is_active' => true,
                'type' => 'derived',
                'category' => 'quantity'
            ]
        );

        // หน่วยแพ็ค (6 ชิ้น)
        Unit::firstOrCreate(
            [
                'company_id' => $company->id,
                'code' => 'PAC'
            ],
            [
                'name' => 'แพ็ค',
                'symbol' => 'แพ็ค',
                'base_unit_id' => $pcsUnit->id,
                'conversion_factor' => 6, // 1 แพ็ค = 6 ชิ้น
                // 'is_base_unit' => false, // ลบออก
                'is_default' => false,
                'is_active' => true,
                'type' => 'derived',
                'category' => 'quantity'
            ]
        );

        // หน่วยกิโลกรัม
        $kgUnit = Unit::firstOrCreate(
            [
                'company_id' => $company->id,
                'code' => 'KG'
            ],
            [
                'name' => 'กิโลกรัม',
                'symbol' => 'กก.',
                'base_unit_id' => null,
                'conversion_factor' => 1,
                // 'is_base_unit' => true, // ลบออก
                'is_default' => false,
                'is_active' => true,
                'type' => 'standard',
                'category' => 'weight'
            ]
        );

        // หน่วยกรัม
        Unit::firstOrCreate(
            [
                'company_id' => $company->id,
                'code' => 'G'
            ],
            [
                'name' => 'กรัม',
                'symbol' => 'ก.',
                'base_unit_id' => $kgUnit->id,
                'conversion_factor' => 0.001, // 1 กรัม = 0.001 กิโลกรัม
                // 'is_base_unit' => false, // ลบออก
                'is_default' => false,
                'is_active' => true,
                'type' => 'derived',
                'category' => 'weight'
            ]
        );

        // เพิ่มหน่วยเพิ่มเติมตามต้องการ
    }
}
