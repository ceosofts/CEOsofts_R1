<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SimpleCompanySeeder extends Seeder
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
                'name' => 'บริษัท ซีอีโอซอฟท์ จำกัด',
                'tax_id' => '1234567890123',
                'address' => 'เลขที่ 87/1 อาคารแคปปิตอลทาวเวอร์ ชั้น 10 ถนนวิทยุ แขวงลุมพินี เขตปทุมวัน กรุงเทพฯ 10330',
                'phone' => '02-123-4567',
                'email' => 'info@ceosofts.com',
                'website' => 'https://www.ceosofts.com',
            ],
            [
                'name' => 'บริษัท ไทยเทค โซลูชันส์ จำกัด',
                'tax_id' => '9876543210123',
                'address' => '55/6 อาคารเอ็มไพร์ทาวเวอร์ ชั้น 15 ถนนสาทรใต้ แขวงยานนาวา เขตสาทร กรุงเทพฯ 10120',
                'phone' => '02-987-6543',
                'email' => 'contact@thaitech.co.th',
                'website' => 'https://www.thaitech.co.th',
            ],
            [
                'name' => 'บริษัท บางกอก ซิสเต็มส์ จำกัด',
                'tax_id' => '1111222233334',
                'address' => '33 อาคารสาธรสแควร์ ชั้น 20 ถนนสาทร แขวงสีลม เขตบางรัก กรุงเทพฯ 10500',
                'phone' => '02-111-2222',
                'email' => 'info@bangkoksystems.com',
                'website' => 'https://www.bangkoksystems.com',
            ]
        ];

        try {
            // เคลียร์ข้อมูลเก่าออกก่อน
            DB::table('companies')->truncate();
            $this->command->info("Cleared existing companies data");
        } catch (\Exception $e) {
            $this->command->warn("Could not clear existing data: " . $e->getMessage());
        }

        $successCount = 0;

        foreach ($companies as $companyData) {
            // สร้างข้อมูลเฉพาะคอลัมน์ที่มีอยู่จริง
            $data = [];
            foreach ($companyData as $key => $value) {
                if (in_array($key, $columns)) {
                    $data[$key] = $value;
                }
            }

            // เพิ่ม is_active หากมีคอลัมน์นี้
            if (in_array('is_active', $columns)) {
                $data['is_active'] = 1; // active
            }

            // เพิ่ม ulid หากมีคอลัมน์นี้
            if (in_array('ulid', $columns)) {
                $data['ulid'] = (string) Str::ulid();
            }

            // เพิ่ม uuid หากมีคอลัมน์นี้
            if (in_array('uuid', $columns)) {
                $data['uuid'] = (string) Str::uuid();
            }

            // เพิ่ม settings หากมีคอลัมน์นี้
            if (in_array('settings', $columns)) {
                $data['settings'] = json_encode([
                    'fiscal_year_start' => '01-01',
                    'uses_fiscal_year' => false,
                ]);
            }

            try {
                $company = Company::create($data);
                $this->command->info("Created company: {$companyData['name']}");
                $successCount++;
            } catch (\Exception $e) {
                $this->command->error("Could not create company {$companyData['name']}: " . $e->getMessage());
                Log::error("Error creating company: " . $e->getMessage());
            }
        }

        $this->command->info("Successfully created {$successCount} companies");
    }
}
