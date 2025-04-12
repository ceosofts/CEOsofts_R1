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
        $companies = [
            [
                'name' => 'บริษัท ซีอีโอซอฟต์ จำกัด',
                'code' => 'CEOSOFT',
                'address' => '55/99 ถนนรัชดาภิเษก แขวงดินแดง เขตดินแดง กรุงเทพมหานคร 10400',
                'phone' => '02-123-4567',
                'email' => 'info@ceosofts.com',
                'tax_id' => '0105564123456',
                'website' => 'https://www.ceosofts.com',
                'logo' => null,
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
            ],
        ];

        foreach ($companies as $company) {
            try {
                // ใช้ Direct DB Query เพื่อหลีกเลี่ยงการกำหนดค่า ID โดย Eloquent
                $exists = DB::table('companies')->where('code', $company['code'])->exists();

                if ($exists) {
                    // อัปเดตข้อมูลบริษัทที่มีอยู่แล้ว
                    DB::table('companies')
                        ->where('code', $company['code'])
                        ->update(array_merge($company, [
                            'uuid' => Str::uuid()->toString(),
                            'updated_at' => now()
                        ]));

                    $this->command->info("Updated company: {$company['name']}");
                } else {
                    // เพิ่มบริษัทใหม่
                    DB::table('companies')->insert(array_merge($company, [
                        'uuid' => Str::uuid()->toString(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]));

                    $this->command->info("Created company: {$company['name']}");
                }
            } catch (\Exception $e) {
                $this->command->error("Error processing company {$company['code']}: {$e->getMessage()}");
                // แสดงข้อมูลเพิ่มเติมเกี่ยวกับ error
                $this->command->line("Error details: " . $e->getTraceAsString());
            }
        }
    }
}
