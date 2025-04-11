<?php

namespace Database\Seeders;

use App\Domain\Settings\Models\Translation;
use App\Domain\Organization\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TranslationSeeder extends Seeder
{
    public function run(): void
    {
        // ตรวจสอบว่ามีตาราง translations หรือไม่
        if (!Schema::hasTable('translations')) {
            $this->command->error('ไม่พบตาราง translations กรุณารัน migration ก่อน');
            return;
        }
        
        // ดึงข้อมูล companies ทั้งหมด
        $companies = Company::all();
        
        if ($companies->isEmpty()) {
            $this->command->warn('ไม่พบข้อมูล Company กรุณารัน CompanySeeder ก่อน');
            return;
        }
        
        foreach ($companies as $company) {
            $this->createTranslationsForCompany($company->id);
        }
    }
    
    private function createTranslationsForCompany($companyId)
    {
        // ข้อมูลการแปลภาษา
        $translations = [
            // ข้อความทั่วไป
            [
                'locale' => 'en',
                'group' => 'messages',
                'key' => 'welcome',
                'field' => 'general',
                'value' => 'Welcome to CEOsofts',
                'translatable_type' => 'general',
                'translatable_id' => 0,
            ],
            [
                'locale' => 'th',
                'group' => 'messages',
                'key' => 'welcome',
                'field' => 'general',
                'value' => 'ยินดีต้อนรับสู่ CEOsofts',
                'translatable_type' => 'general',
                'translatable_id' => 0,
            ],
            // ปุ่ม
            [
                'locale' => 'en',
                'group' => 'buttons',
                'key' => 'save',
                'field' => 'general',
                'value' => 'Save',
                'translatable_type' => 'general',
                'translatable_id' => 0,
            ],
            [
                'locale' => 'th',
                'group' => 'buttons',
                'key' => 'save',
                'field' => 'general',
                'value' => 'บันทึก',
                'translatable_type' => 'general',
                'translatable_id' => 0,
            ],
            [
                'locale' => 'en',
                'group' => 'buttons',
                'key' => 'cancel',
                'field' => 'general',
                'value' => 'Cancel',
                'translatable_type' => 'general',
                'translatable_id' => 0,
            ],
            [
                'locale' => 'th',
                'group' => 'buttons',
                'key' => 'cancel',
                'field' => 'general',
                'value' => 'ยกเลิก',
                'translatable_type' => 'general',
                'translatable_id' => 0,
            ],
        ];
        
        // เพิ่มข้อมูล
        foreach ($translations as $translation) {
            try {
                // ตรวจสอบว่ามีข้อมูลนี้อยู่แล้วหรือไม่โดยใช้เงื่อนไขที่ถูกต้อง
                $exists = DB::table('translations')
                    ->where('company_id', $companyId)
                    ->where('locale', $translation['locale'])
                    ->where('group', $translation['group'])
                    ->where('key', $translation['key'])
                    ->exists();
                
                if (!$exists) {
                    // สร้างข้อมูลใหม่
                    $data = array_merge(
                        ['company_id' => $companyId],
                        $translation,
                        ['created_at' => now(), 'updated_at' => now()]
                    );
                    
                    // ใช้ raw insert แทนการใช้ model เพื่อข้าม unique constraint
                    DB::table('translations')->insert($data);
                    $this->command->info("เพิ่มคำแปล: {$translation['locale']}.{$translation['group']}.{$translation['key']}");
                } else {
                    // อัปเดตข้อมูลที่มีอยู่แล้ว
                    DB::table('translations')
                        ->where('company_id', $companyId)
                        ->where('locale', $translation['locale'])
                        ->where('group', $translation['group'])
                        ->where('key', $translation['key'])
                        ->update([
                            'value' => $translation['value'],
                            'field' => $translation['field'],
                            'translatable_type' => $translation['translatable_type'],
                            'translatable_id' => $translation['translatable_id'],
                            'updated_at' => now()
                        ]);
                    $this->command->info("อัปเดตคำแปล: {$translation['locale']}.{$translation['group']}.{$translation['key']}");
                }
            } catch (\Exception $e) {
                // เก็บข้อความที่เป็นประโยชน์
                $errorMessage = $e->getMessage();
                
                // แสดงรายละเอียดข้อผิดพลาด
                $this->command->error("เกิดข้อผิดพลาดในการเพิ่ม/อัปเดตคำแปล {$translation['key']}: {$errorMessage}");
                
                // ลองใช้อีกวิธีหนึ่งในการแทรกข้อมูล: ลบข้อมูลเดิมก่อนแล้วค่อยเพิ่มใหม่
                try {
                    DB::table('translations')
                        ->where('company_id', $companyId)
                        ->where('locale', $translation['locale'])
                        ->where('group', $translation['group'])
                        ->where('key', $translation['key'])
                        ->delete();
                    
                    $data = array_merge(
                        ['company_id' => $companyId],
                        $translation,
                        ['created_at' => now(), 'updated_at' => now()]
                    );
                    
                    DB::table('translations')->insert($data);
                    $this->command->info("เพิ่มคำแปลสำเร็จหลังจากลบข้อมูลเดิม: {$translation['locale']}.{$translation['group']}.{$translation['key']}");
                } catch (\Exception $e2) {
                    $this->command->error("ล้มเหลวในการเพิ่มคำแปลแม้หลังจากลบข้อมูลเดิม: {$e2->getMessage()}");
                }
            }
        }
    }
}
