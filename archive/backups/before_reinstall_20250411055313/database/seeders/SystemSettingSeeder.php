<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Settings\Models\Setting;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // การตั้งค่าทั่วไป (company_id = null คือเป็นการตั้งค่าระดับระบบ)
        $systemSettings = [
            [
                'group' => 'general',
                'key' => 'site_name',
                'value' => 'CEOsofts',
                'type' => 'string',
                'is_public' => true,
                'description' => 'ชื่อเว็บไซต์',
                'sort_order' => 1
            ],
            [
                'group' => 'general',
                'key' => 'site_description',
                'value' => 'ระบบบริหารจัดการธุรกิจสำหรับผู้ประกอบการ',
                'type' => 'string',
                'is_public' => true,
                'description' => 'คำอธิบายเว็บไซต์',
                'sort_order' => 2
            ],
            [
                'group' => 'general',
                'key' => 'default_language',
                'value' => 'th',
                'type' => 'string',
                'is_public' => true,
                'description' => 'ภาษาเริ่มต้น',
                'sort_order' => 3
            ],
            [
                'group' => 'general',
                'key' => 'default_timezone',
                'value' => 'Asia/Bangkok',
                'type' => 'string',
                'is_public' => true,
                'description' => 'เขตเวลาเริ่มต้น',
                'sort_order' => 4
            ],
            
            // การตั้งค่าอีเมล
            [
                'group' => 'email',
                'key' => 'from_address',
                'value' => 'info@ceosofts.com',
                'type' => 'string',
                'is_public' => false,
                'description' => 'อีเมลสำหรับส่ง',
                'sort_order' => 1
            ],
            [
                'group' => 'email',
                'key' => 'from_name',
                'value' => 'CEOsofts',
                'type' => 'string',
                'is_public' => false,
                'description' => 'ชื่อผู้ส่ง',
                'sort_order' => 2
            ],
            
            // การตั้งค่าใบแจ้งหนี้
            [
                'group' => 'invoice',
                'key' => 'due_days',
                'value' => '15',
                'type' => 'integer',
                'is_public' => false,
                'description' => 'จำนวนวันครบกำหนดชำระ',
                'sort_order' => 1
            ],
            [
                'group' => 'invoice',
                'key' => 'invoice_prefix',
                'value' => 'INV',
                'type' => 'string',
                'is_public' => false,
                'description' => 'คำนำหน้าเลขที่ใบแจ้งหนี้',
                'sort_order' => 2
            ],
        ];
        
        foreach ($systemSettings as $setting) {
            Setting::firstOrCreate(
                [
                    'company_id' => null,
                    'group' => $setting['group'],
                    'key' => $setting['key']
                ],
                $setting
            );
        }
    }
}
