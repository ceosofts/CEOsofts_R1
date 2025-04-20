<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Company;
use Carbon\Carbon;
use Faker\Factory as Faker;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('th_TH');
        
        // Get all available company IDs or use default ID 1
        $companyIds = Company::pluck('id')->toArray();
        
        // If no companies exist, stop seeding to prevent errors
        if (empty($companyIds)) {
            $this->command->warn('No companies found. Please run CompanySeeder first.');
            return;
        }
        
        // สร้างลูกค้าโดยใช้รหัสอัตโนมัติ
        for ($i = 1; $i <= 20; $i++) {
            $type = $faker->randomElement(['company', 'person']);
            $customerGroup = $faker->randomElement(['A', 'B', 'C', null]);
            $paymentTermType = $faker->randomElement(['credit', 'cash', 'cheque', 'transfer']);
            
            // สำหรับการทดสอบ ให้จำลองวันที่เป็นย้อนหลังเพื่อให้ได้รหัสที่หลากหลาย
            $date = Carbon::now()->subDays(rand(0, 30));
            $prefix = 'CUS';
            $year = $date->format('Y');
            $month = $date->format('m');
            
            // นับจำนวนลูกค้าในเดือนที่กำหนด
            $count = Customer::where('code', 'like', $prefix . $year . $month . '%')->count();
            $nextNumber = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
            $code = $prefix . $year . $month . $nextNumber;
            
            $metadata = [
                'industry' => $faker->randomElement(['ผลิตอาหาร', 'อสังหาริมทรัพย์', 'การเงิน', 'เทคโนโลยี', 'การศึกษา', 'สุขภาพ']),
                'sales_region' => $faker->randomElement(['กรุงเทพฯ', 'ภาคเหนือ', 'ภาคใต้', 'ภาคตะวันออก', 'ภาคตะวันออกเฉียงเหนือ']),
                'credit_term' => $faker->randomElement([7, 15, 30, 45, 60])
            ];
            
            $social_media = [
                'facebook' => $type === 'company' ? $faker->domainName : $faker->userName,
                'line' => $faker->userName,
                'instagram' => $faker->userName
            ];
            
            // ใช้ company_id จากรายการที่มีอยู่จริง
            $company_id = $faker->randomElement($companyIds);
            
            Customer::create([
                'code' => $code,
                'name' => $type === 'company' ? $faker->company : $faker->name,
                'type' => $type,
                'status' => $faker->randomElement(['active', 'inactive']),
                'email' => $faker->email,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'website' => $type === 'company' ? 'https://' . $faker->domainName : null,
                'tax_id' => $type === 'company' ? $faker->numerify('###########') : null,
                'reference_id' => $faker->bothify('REF-####-????'),
                'contact_person' => $faker->name,
                'contact_person_position' => $faker->jobTitle,
                'contact_person_email' => $faker->email,
                'contact_person_phone' => $faker->phoneNumber,
                'contact_person_line_id' => $faker->userName,
                'social_media' => json_encode($social_media),
                'is_supplier' => $faker->boolean(20), // 20% chance of being a supplier
                'customer_group' => $customerGroup,
                'customer_rating' => $faker->numberBetween(1, 5),
                'payment_term_type' => $paymentTermType,
                'credit_limit' => $paymentTermType === 'credit' ? $faker->randomFloat(2, 10000, 1000000) : null,
                'discount_rate' => $faker->randomFloat(2, 0, 20),
                'bank_name' => $faker->randomElement(['ไทยพาณิชย์', 'กสิกรไทย', 'กรุงเทพ', 'กรุงไทย', 'กรุงศรีอยุธยา']),
                'bank_branch' => $faker->city,
                'bank_account_name' => $type === 'company' ? $faker->company : $faker->name,
                'bank_account_number' => $faker->numerify('##########'),
                'last_contacted_date' => $faker->dateTimeBetween('-6 months', 'now'),
                'note' => $faker->paragraph,
                'metadata' => json_encode($metadata),
                'company_id' => $company_id,
                'created_at' => $date,
                'updated_at' => $date
            ]);
        }
    }
}
