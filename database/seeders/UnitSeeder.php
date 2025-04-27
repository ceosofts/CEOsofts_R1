<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();
        foreach ($companies as $company) {
            $this->createUnitsForCompany($company);
        }
    }

    private function createUnitsForCompany(Company $company): void
    {
        // ตรวจสอบสล็อตของคอลัมน์ทั้งหมดใน units
        $hasCode = Schema::hasColumn('units', 'code');
        $hasSymbol = Schema::hasColumn('units', 'symbol');
        $hasAbbreviation = Schema::hasColumn('units', 'abbreviation');
        $hasCategory = Schema::hasColumn('units', 'category');
        $hasType = Schema::hasColumn('units', 'type');
        $hasIsDefault = Schema::hasColumn('units', 'is_default');
        $hasBaseUnitId = Schema::hasColumn('units', 'base_unit_id');
        $hasConversionFactor = Schema::hasColumn('units', 'conversion_factor');
        
        // สร้าง units พื้นฐาน
        $basicUnits = [
            [
                'name' => 'ชิ้น',
                'code' => 'UNI-01-001',
                'symbol' => 'ชิ้น',
                'conversion_factor' => 1,
                'is_default' => true,
                'type' => 'standard',
                'category' => 'quantity',
                'description' => 'หน่วยนับพื้นฐานสำหรับสินค้าทั่วไป',
                'is_active' => true,
            ],
            [
                'name' => 'กิโลกรัม',
                'code' => 'UNI-02-001',
                'symbol' => 'กก.',
                'conversion_factor' => 1,
                'is_default' => true,
                'type' => 'standard',
                'category' => 'weight',
                'description' => 'หน่วยนับน้ำหนักพื้นฐาน',
                'is_active' => true,
            ],
            [
                'name' => 'ลิตร',
                'code' => 'UNI-03-001',
                'symbol' => 'ลิตร',
                'conversion_factor' => 1,
                'is_default' => true,
                'type' => 'standard',
                'category' => 'volume',
                'description' => 'หน่วยนับปริมาตรพื้นฐาน',
                'is_active' => true,
            ],
        ];
        
        foreach ($basicUnits as $unitData) {
            // สร้างข้อมูลพื้นฐานที่ต้องมี
            $data = [
                'company_id' => $company->id,
                'name' => $unitData['name'],
                'description' => $unitData['description'],
                'is_active' => $unitData['is_active'],
            ];
            
            // เพิ่มข้อมูลตามคอลัมน์ที่มีในฐานข้อมูล
            if ($hasCode && isset($unitData['code'])) {
                $data['code'] = $unitData['code'];
            }
            
            if ($hasSymbol && isset($unitData['symbol'])) {
                $data['symbol'] = $unitData['symbol'];
            } elseif ($hasAbbreviation && isset($unitData['symbol'])) {
                $data['abbreviation'] = $unitData['symbol'];
            }
            
            if ($hasCategory && isset($unitData['category'])) {
                $data['category'] = $unitData['category'];
            }
            
            if ($hasType && isset($unitData['type'])) {
                $data['type'] = $unitData['type'];
            }
            
            if ($hasIsDefault && isset($unitData['is_default'])) {
                $data['is_default'] = $unitData['is_default'];
            }
            
            if ($hasConversionFactor && isset($unitData['conversion_factor'])) {
                $data['conversion_factor'] = $unitData['conversion_factor'];
            }
            
            // ถ้าหน่วยเป็นหน่วยพื้นฐาน จะไม่มี base_unit_id
            if ($hasBaseUnitId) {
                $data['base_unit_id'] = null; // หรืออาจจะไม่ต้องใส่ค่านี้เลยก็ได้
            }
            
            // สร้างหรืออัพเดทข้อมูล
            Unit::updateOrCreate(
                ['company_id' => $company->id, 'name' => $unitData['name']],
                $data
            );
        }

        // เพิ่มหน่วยที่มีการแปลงค่า (ถ้าต้องการ)
        if ($hasBaseUnitId && $hasConversionFactor) {
            $derivedUnits = [
                // หน่วยน้ำหนัก
                [
                    'name' => 'กรัม',
                    'code' => 'UNI-02-002',
                    'symbol' => 'ก.',
                    'base_unit_name' => 'กิโลกรัม', // จะถูกแปลงเป็น base_unit_id
                    'conversion_factor' => 0.001,
                    'type' => 'derived',
                    'category' => 'weight',
                    'description' => 'หน่วยน้ำหนัก 1 กรัม = 0.001 กิโลกรัม',
                ],
                // หน่วยปริมาตร
                [
                    'name' => 'มิลลิลิตร',
                    'code' => 'UNI-03-002',
                    'symbol' => 'มล.',
                    'base_unit_name' => 'ลิตร',
                    'conversion_factor' => 0.001,
                    'type' => 'derived',
                    'category' => 'volume',
                    'description' => 'หน่วยปริมาตร 1 มิลลิลิตร = 0.001 ลิตร',
                ],
                // เพิ่มหน่วยที่มีความสัมพันธ์อื่น ๆ ตามต้องการ
            ];
            
            foreach ($derivedUnits as $unitData) {
                // หา base_unit_id จากชื่อ base_unit
                $baseUnit = Unit::where('company_id', $company->id)
                                ->where('name', $unitData['base_unit_name'])
                                ->first();
                                
                if ($baseUnit) {
                    // สร้างข้อมูลพื้นฐานที่ต้องมี
                    $data = [
                        'company_id' => $company->id,
                        'name' => $unitData['name'],
                        'description' => $unitData['description'],
                        'is_active' => true,
                        'base_unit_id' => $baseUnit->id,
                        'conversion_factor' => $unitData['conversion_factor'],
                    ];
                    
                    // เพิ่มข้อมูลตามคอลัมน์ที่มีในฐานข้อมูล
                    if ($hasCode && isset($unitData['code'])) {
                        $data['code'] = $unitData['code'];
                    }
                    
                    if ($hasSymbol && isset($unitData['symbol'])) {
                        $data['symbol'] = $unitData['symbol'];
                    } elseif ($hasAbbreviation && isset($unitData['symbol'])) {
                        $data['abbreviation'] = $unitData['symbol'];
                    }
                    
                    if ($hasCategory && isset($unitData['category'])) {
                        $data['category'] = $unitData['category'];
                    }
                    
                    if ($hasType && isset($unitData['type'])) {
                        $data['type'] = $unitData['type'];
                    }
                    
                    if ($hasIsDefault) {
                        $data['is_default'] = false; // หน่วยที่มีการแปลงค่าจะไม่เป็นหน่วยเริ่มต้น
                    }
                    
                    // สร้างหรืออัพเดทข้อมูล
                    Unit::updateOrCreate(
                        ['company_id' => $company->id, 'name' => $unitData['name']],
                        $data
                    );
                }
            }
        }
    }
}
