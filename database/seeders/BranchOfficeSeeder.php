<?php

namespace Database\Seeders;

use App\Models\BranchOffice;
use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchOfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ล้างข้อมูลสาขาเดิม (ถ้าต้องการ)
        // DB::table('branch_offices')->truncate();
        
        // ดึงรายการบริษัททั้งหมด
        $companies = Company::all();
        
        foreach ($companies as $company) {
            $companyId = $company->id;
            $companyPrefix = str_pad($companyId, 2, '0', STR_PAD_LEFT); // 01, 02, ...
            
            // สร้างสำนักงานใหญ่
            BranchOffice::create([
                'company_id' => $companyId,
                'name' => 'สำนักงานใหญ่',
                'code' => "HQ-{$companyPrefix}",
                'address' => '123 ถนนรัชดาภิเษก แขวงดินแดง เขตดินแดง กรุงเทพมหานคร 10400',
                'phone' => '02-123-4567',
                'email' => 'hq@ceosofts.com',
                'is_headquarters' => true,
                'is_active' => true,
                'metadata' => json_encode([
                    'region' => 'กรุงเทพและปริมณฑล',
                    'tax_branch_id' => '00000',
                    'opening_date' => '2020-01-01'
                ])
            ]);
            
            // สร้างสาขาเชียงใหม่
            BranchOffice::create([
                'company_id' => $companyId,
                'name' => 'สาขาเชียงใหม่',
                'code' => "BRA-{$companyPrefix}-001",
                'address' => '456 ถ.ห้วยแก้ว ต.สุเทพ อ.เมือง จ.เชียงใหม่ 50200',
                'phone' => '053-123-456',
                'email' => 'cnx@ceosofts.com',
                'is_headquarters' => false,
                'is_active' => true,
                'metadata' => json_encode([
                    'region' => 'ภาคเหนือ',
                    'tax_branch_id' => '00001',
                    'opening_date' => '2021-03-01'
                ])
            ]);
            
            // สร้างสาขาขอนแก่น
            BranchOffice::create([
                'company_id' => $companyId,
                'name' => 'สาขาขอนแก่น',
                'code' => "BRA-{$companyPrefix}-002",
                'address' => '789 ถ.มิตรภาพ ต.ในเมือง อ.เมือง จ.ขอนแก่น 40000',
                'phone' => '043-234-567',
                'email' => 'kkc@ceosofts.com',
                'is_headquarters' => false,
                'is_active' => true,
                'metadata' => json_encode([
                    'region' => 'ภาคตะวันออกเฉียงเหนือ',
                    'tax_branch_id' => '00002',
                    'opening_date' => '2022-06-01'
                ])
            ]);
            
            // เพิ่มสาขาอื่นๆ ตามต้องการ
            BranchOffice::create([
                'company_id' => $companyId,
                'name' => 'สาขาภูเก็ต',
                'code' => "BRA-{$companyPrefix}-003",
                'address' => '123 ถ.เทพกระษัตรี ต.รัษฎา อ.เมือง จ.ภูเก็ต 83000',
                'phone' => '076-345-678',
                'email' => 'phuket@ceosofts.com',
                'is_headquarters' => false,
                'is_active' => true,
                'metadata' => json_encode([
                    'region' => 'ภาคใต้',
                    'tax_branch_id' => '00003',
                    'opening_date' => '2023-01-15'
                ])
            ]);
        }
        
        $this->command->info('Branch offices seeded successfully.');
    }
}
