<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Organization\Models\Company; // ตรวจสอบ namespace ให้ถูกต้อง

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::create([
            'name' => 'Demo Company',
            'code' => 'DEMO',
            'address' => '123 Demo Street, Bangkok, Thailand',
            'phone' => '02-123-4567',
            'email' => 'demo@ceosofts.com',
            'tax_id' => '1234567890123',
            'is_active' => true,
        ]);
    }
}
