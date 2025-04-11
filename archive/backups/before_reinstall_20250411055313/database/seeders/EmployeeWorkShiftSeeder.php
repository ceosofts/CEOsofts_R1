<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\HumanResources\Models\EmployeeWorkShift;
use App\Domain\HumanResources\Models\Employee;
use App\Domain\HumanResources\Models\WorkShift;

class EmployeeWorkShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = Employee::all();
        foreach ($employees as $employee) {
            $this->createWorkShiftsForEmployee($employee);
        }
    }

    private function createWorkShiftsForEmployee($employee)
    {
        // ดึงกะงานของบริษัทที่พนักงานสังกัด
        $workShifts = WorkShift::where('company_id', $employee->company_id)
                              ->where('is_active', true)
                              ->get();

        if ($workShifts->isEmpty()) {
            return;
        }

        // สร้างตารางกะงานของพนักงานสำหรับเดือนปัจจุบันและเดือนถัดไป
        $startDate = now()->startOfMonth();
        $endDate = now()->addMonth()->endOfMonth();

        // สุ่มเลือกกะงานหลักสำหรับพนักงาน
        $primaryShift = $workShifts->where('code', 'REG')->first() 
            ?? $workShifts->first();

        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            // ข้ามวันเสาร์-อาทิตย์ถ้าเป็นกะปกติ
            if ($primaryShift->code === 'REG' && ($currentDate->isWeekend())) {
                $currentDate->addDay();
                continue;
            }

            EmployeeWorkShift::firstOrCreate([
                'employee_id' => $employee->id,
                'work_shift_id' => $primaryShift->id,
                'work_date' => $currentDate->format('Y-m-d'),
            ], [
                'status' => 'scheduled',
                'notes' => null,
                'metadata' => json_encode([
                    'assigned_by' => 'system',
                    'assigned_at' => now()->toDateTimeString(),
                    'shift_type' => 'regular'
                ])
            ]);

            $currentDate->addDay();
        }
    }
}
