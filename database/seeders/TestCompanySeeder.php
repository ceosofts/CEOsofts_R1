<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class TestCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ตรวจสอบคอลัมน์ที่มีอยู่จริงในตาราง
        $columns = Schema::getColumnListing('companies');
        $this->command->info("Columns in companies table: " . implode(", ", $columns));

        $companies = [
            [
                'name' => 'บริษัท ทดสอบ อินเตอร์เนชั่นแนล จำกัด',
                'code' => 'TEST01',
                'address' => '123 ถนนสาทร แขวงสีลม เขตบางรัก กรุงเทพฯ 10500',
                'phone' => '02-123-4567',
                'email' => 'contact@test-inter.co.th',
                'tax_id' => '1234567890123',
                'website' => 'https://www.test-inter.co.th',
                'status' => 'active',
            ],
            [
                'name' => 'บริษัท ดิจิทัล โซลูชั่น จำกัด',
                'code' => 'DGSOL',
                'address' => '88/9 อาคารเอไอเอ ถนนสีลม แขวงสุริยวงศ์ เขตบางรัก กรุงเทพฯ 10500',
                'phone' => '02-987-6543',
                'email' => 'contact@digitalsolution.co.th',
                'tax_id' => '9876543210123',
                'website' => 'https://www.digitalsolution.co.th',
                'status' => 'active',
            ],
            [
                'name' => 'บริษัท อีคอมเมิร์ซ โปร จำกัด',
                'code' => 'ECOMPRO',
                'address' => '99 อาคารไซเบอร์เวิลด์ ชั้น 15 ถนนรัชดาภิเษก แขวงดินแดง เขตดินแดง กรุงเทพฯ 10400',
                'phone' => '02-111-2222',
                'email' => 'info@ecompro.co.th',
                'tax_id' => '5678901234567',
                'website' => 'https://www.ecompro.co.th',
                'status' => 'active',
            ],
            [
                'name' => 'บริษัท ไทย ชิปปิ้ง จำกัด',
                'code' => 'THAISHP',
                'address' => '123/4 ถนนสุขุมวิท 21 แขวงคลองเตยเหนือ เขตวัฒนา กรุงเทพฯ 10110',
                'phone' => '02-333-4444',
                'email' => 'info@thaishipping.co.th',
                'tax_id' => '3456789012345',
                'website' => 'https://www.thaishipping.co.th',
                'status' => 'active',
            ],
            [
                'name' => 'บริษัท ทัวร์ไทย จำกัด',
                'code' => 'THAITOUR',
                'address' => '456 ถนนพระราม 1 แขวงวังใหม่ เขตปทุมวัน กรุงเทพฯ 10330',
                'phone' => '02-555-6666',
                'email' => 'contact@thaitour.co.th',
                'tax_id' => '7890123456789',
                'website' => 'https://www.thaitour.co.th',
                'status' => 'inactive',
            ],
        ];

        foreach ($companies as $companyData) {
            try {
                // สร้าง array ข้อมูลที่มีเฉพาะคอลัมน์ที่มีอยู่จริง
                $validData = [];
                foreach ($companyData as $key => $value) {
                    if (in_array($key, $columns)) {
                        $validData[$key] = $value;
                    }
                }

                // ตรวจสอบว่ามีคอลัมน์ uuid หรือ ulid
                if (in_array('uuid', $columns)) {
                    $validData['uuid'] = (string) Str::uuid();
                }
                if (in_array('ulid', $columns)) {
                    $validData['ulid'] = (string) Str::ulid();
                }

                // ตรวจสอบค่า is_active
                if (in_array('is_active', $columns) && !isset($validData['is_active'])) {
                    $validData['is_active'] = $companyData['status'] === 'active' ? 1 : 0;
                }

                // สร้างข้อมูล settings ถ้ามีคอลัมน์นี้
                if (in_array('settings', $columns)) {
                    $validData['settings'] = json_encode([
                        'created_via' => 'seeder',
                        'fiscal_year_start' => '01-01',
                        'uses_fiscal_year' => rand(0, 1) === 1,
                    ]);
                }

                // สร้างข้อมูล metadata ถ้ามีคอลัมน์นี้
                if (in_array('metadata', $columns)) {
                    $validData['metadata'] = json_encode([
                        'created_via' => 'seeder',
                        'version' => '1.0',
                    ]);
                }

                Company::create($validData);
                $this->command->info("Created company: {$companyData['name']}");
            } catch (\Exception $e) {
                Log::error("Error creating company {$companyData['name']}: " . $e->getMessage());
                $this->command->error("Failed to create {$companyData['name']}: " . $e->getMessage());
            }
        }

        $this->command->info('Test companies data seeded successfully!');
    }
}
