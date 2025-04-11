<?php

namespace Database\Seeders;

use App\Domain\Settings\Models\Setting;
use App\Domain\Organization\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        
        foreach ($companies as $company) {
            $this->createSettingsForCompany($company->id);
        }
    }

    private function createSettingsForCompany($companyId)
    {
        // ตรวจสอบว่า settings มี deleted_at และ options หรือไม่
        $hasSoftDeletes = Schema::hasColumn('settings', 'deleted_at');
        $hasOptions = Schema::hasColumn('settings', 'options');
        
        $settings = [
            [
                'key' => 'company_working_days',
                'value' => json_encode(['monday', 'tuesday', 'wednesday', 'thursday', 'friday']),
                'type' => 'json',
                'group' => 'working_hours',
                'description' => 'วันทำงานของบริษัท',
                'options' => $hasOptions ? json_encode([
                    'monday' => 'วันจันทร์', 
                    'tuesday' => 'วันอังคาร', 
                    'wednesday' => 'วันพุธ', 
                    'thursday' => 'วันพฤหัสบดี', 
                    'friday' => 'วันศุกร์', 
                    'saturday' => 'วันเสาร์', 
                    'sunday' => 'วันอาทิตย์'
                ]) : null
            ],
            [
                'key' => 'company_working_hours',
                'value' => json_encode(['start' => '09:00', 'end' => '18:00']),
                'type' => 'json',
                'group' => 'working_hours',
                'description' => 'เวลาทำงานของบริษัท',
            ],
            [
                'key' => 'company_tax_id',
                'value' => '0123456789012',
                'type' => 'string',
                'group' => 'company',
                'description' => 'เลขประจำตัวผู้เสียภาษี',
            ],
            [
                'key' => 'invoice_prefix',
                'value' => 'INV',
                'type' => 'string',
                'group' => 'invoicing',
                'description' => 'คำนำหน้าเลขที่ใบแจ้งหนี้',
            ],
        ];

        foreach ($settings as $setting) {
            // ลบ key options ถ้าไม่มีคอลัมน์นี้
            if (!$hasOptions && isset($setting['options'])) {
                unset($setting['options']);
            }
            
            if ($hasSoftDeletes) {
                // ใช้ Model ถ้ามี SoftDeletes
                try {
                    Setting::firstOrCreate(
                        [
                            'company_id' => $companyId,
                            'key' => $setting['key']
                        ],
                        array_merge(['company_id' => $companyId], $setting)
                    );
                } catch (\Exception $e) {
                    $this->command->error("Error creating setting {$setting['key']}: " . $e->getMessage());
                }
            } else {
                // ใช้ DB Query Builder โดยตรงถ้าไม่มี SoftDeletes
                $exists = DB::table('settings')
                    ->where('company_id', $companyId)
                    ->where('key', $setting['key'])
                    ->exists();
                
                if (!$exists) {
                    try {
                        DB::table('settings')->insert(
                            array_merge(
                                [
                                    'company_id' => $companyId,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ],
                                $setting
                            )
                        );
                    } catch (\Exception $e) {
                        $this->command->error("Error inserting setting {$setting['key']}: " . $e->getMessage());
                    }
                }
            }
        }
    }
}
