<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\HumanResources\Models\WorkShift;
use App\Domain\Organization\Models\Company;
use Illuminate\Support\Facades\DB;

class WorkShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // แก้ไข unique constraint ก่อน
        $this->fixUniqueConstraint();
        
        $companies = Company::all();
        foreach ($companies as $company) {
            $this->createWorkShiftsForCompany($company->id);
        }
    }

    /**
     * แก้ไข unique constraint โดยตรงด้วย raw SQL
     */
    private function fixUniqueConstraint()
    {
        try {
            // ลบ index เดิม (ถ้ามี)
            DB::statement('ALTER TABLE work_shifts DROP INDEX work_shifts_code_unique');
        } catch (\Exception $e) {
            // ไม่ต้องทำอะไร หาก index ไม่มีอยู่
        }
        
        try {
            // สร้าง compound index ใหม่ (ถ้ายังไม่มี)
            if (!$this->indexExists('work_shifts', 'work_shifts_company_id_code_unique')) {
                DB::statement('ALTER TABLE work_shifts ADD UNIQUE INDEX work_shifts_company_id_code_unique (company_id, code)');
            }
        } catch (\Exception $e) {
            // ไม่ต้องทำอะไร หาก index มีอยู่แล้ว
        }
    }
    
    /**
     * ตรวจสอบว่า index มีอยู่หรือไม่
     */
    private function indexExists($table, $indexName)
    {
        $indexes = DB::select("SHOW INDEXES FROM {$table} WHERE Key_name = '{$indexName}'");
        return count($indexes) > 0;
    }

    private function createWorkShiftsForCompany($companyId)
    {
        // ตรวจสอบก่อนว่ามี work_shifts อยู่แล้วหรือไม่
        $existingCount = WorkShift::where('company_id', $companyId)->count();
        if ($existingCount > 0) {
            // ถ้ามีอยู่แล้ว ไม่ต้องสร้างใหม่
            return;
        }
        
        $workShifts = [
            [
                'company_id' => $companyId,
                'name' => 'กะปกติ',
                'code' => 'REG',
                'description' => 'เวลาทำงานปกติ 8:30-17:30',
                'start_time' => '08:30:00',
                'end_time' => '17:30:00',
                'break_start' => '12:00:00',
                'break_end' => '13:00:00',
                'working_hours' => 8.0,
                'is_night_shift' => false,
                'is_active' => true,
                'color' => '#4CAF50',
                'metadata' => json_encode([
                    'grace_period' => 15, // ช่วงผ่อนผันสาย 15 นาที
                    'days_of_week' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
                    'overtime_rate' => 1.5
                ])
            ],
            [
                'company_id' => $companyId,
                'name' => 'กะเช้า',
                'code' => 'MORNING',
                'description' => 'กะเช้า 06:00-15:00',
                'start_time' => '06:00:00',
                'end_time' => '15:00:00',
                'break_start' => '10:00:00',
                'break_end' => '11:00:00',
                'working_hours' => 8.0,
                'is_night_shift' => false,
                'is_active' => true,
                'color' => '#2196F3',
                'metadata' => json_encode([
                    'grace_period' => 10,
                    'days_of_week' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    'overtime_rate' => 1.5
                ])
            ],
            [
                'company_id' => $companyId,
                'name' => 'กะดึก',
                'code' => 'NIGHT',
                'description' => 'กะดึก 22:00-07:00',
                'start_time' => '22:00:00',
                'end_time' => '07:00:00',
                'break_start' => '02:00:00',
                'break_end' => '03:00:00',
                'working_hours' => 8.0,
                'is_night_shift' => true,
                'is_active' => true,
                'color' => '#9C27B0',
                'metadata' => json_encode([
                    'grace_period' => 10,
                    'days_of_week' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    'night_shift_allowance' => 100, // เบี้ยเลี้ยงกะดึก
                    'overtime_rate' => 2.0
                ])
            ]
        ];

        foreach ($workShifts as $workShift) {
            WorkShift::firstOrCreate(
                [
                    'company_id' => $companyId,
                    'code' => $workShift['code']
                ],
                $workShift
            );
        }
    }
}
