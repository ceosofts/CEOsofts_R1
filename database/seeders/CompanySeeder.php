<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Organization\Models\Company;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ข้อมูลบริษัท
        $companies = [
            [
                'name' => 'บริษัท ซีอีโอซอฟต์ จำกัด',
                'code' => 'CEOSOFT',
                'address' => '55/99 ถนนรัชดาภิเษก แขวงดินแดง เขตดินแดง กรุงเทพมหานคร 10400',
                'phone' => '02-123-4567',
                'email' => 'info@ceosofts.com',
                'tax_id' => '0105564123456',
                'website' => 'https://www.ceosofts.com',
                'logo' => null, // ต้องเป็น null จริงๆ
                'is_active' => true,
                'status' => 'active',
                'settings' => json_encode([
                    'invoice_prefix' => 'INV-CEOSOFT',
                    'receipt_prefix' => 'REC-CEOSOFT',
                ]),
                'metadata' => json_encode([
                    'founded_year' => 2015,
                    'industry' => 'Software Development',
                ]),
                'uuid' => (string) Str::uuid(),
                'ulid' => (string) Str::ulid(), // เพิ่ม ulid ที่จำเป็น
            ],
            [
                'name' => 'บริษัท ไทยซอฟต์ เทคโนโลยี จำกัด',
                'code' => 'THAISOFT',
                'address' => '99/88 ถนนสีลม แขวงสีลม เขตบางรัก กรุงเทพมหานคร 10500',
                'phone' => '02-987-6543',
                'email' => 'contact@thaisoft.co.th',
                'tax_id' => '0105562789012',
                'website' => 'https://www.thaisoft.co.th',
                'logo' => null,
                'is_active' => true,
                'status' => 'active',
                'settings' => json_encode([
                    'invoice_prefix' => 'INV-THAISOFT',
                    'receipt_prefix' => 'REC-THAISOFT',
                ]),
                'metadata' => json_encode([
                    'founded_year' => 2010,
                    'industry' => 'IT Solutions',
                ]),
                'uuid' => (string) Str::uuid(),
                'ulid' => (string) Str::ulid(),
            ],
            [
                'name' => 'บริษัท ดิจิทัล โซลูชันส์ จำกัด',
                'code' => 'DIGISOLVE',
                'address' => '77/33 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพมหานคร 10110',
                'phone' => '02-345-6789',
                'email' => 'hello@digitalsolution.co.th',
                'tax_id' => '0105563456789',
                'website' => 'https://www.digitalsolution.co.th',
                'logo' => null,
                'is_active' => true,
                'status' => 'active',
                'settings' => json_encode([
                    'invoice_prefix' => 'INV-DIGISOLVE',
                    'receipt_prefix' => 'REC-DIGISOLVE',
                ]),
                'metadata' => json_encode([
                    'founded_year' => 2018,
                    'industry' => 'Digital Transformation',
                ]),
                'uuid' => (string) Str::uuid(),
                'ulid' => (string) Str::ulid(),
            ],
        ];

        // แก้ไขบริษัทอื่นๆ ให้มี ulid ด้วย
        foreach ($companies as $key => $company) {
            if (!isset($company['ulid'])) {
                $companies[$key]['ulid'] = (string) Str::ulid();
            }
        }

        // ใช้ DB facade แทน Model เนื่องจาก PHP PDO มีการจัดการ null ที่ดีกว่า
        foreach ($companies as $company) {
            // ตรวจสอบว่ามีบริษัทนี้อยู่แล้วหรือไม่
            $exists = DB::table('companies')->where('code', $company['code'])->exists();

            if (!$exists) {
                // ถ้ายังไม่มี ให้เพิ่มใหม่
                DB::table('companies')->insert($company);
                $this->command->info("เพิ่มบริษัท: {$company['name']} ({$company['code']})");
            } else {
                // ถ้ามีแล้ว ให้อัปเดต (ยกเว้น code)
                $code = $company['code'];
                unset($company['code']);  // ไม่อัปเดต code

                DB::table('companies')
                    ->where('code', $code)
                    ->update($company);

                $this->command->info("อัปเดตบริษัท: {$company['name']} ($code)");
            }
        }
    }
}
