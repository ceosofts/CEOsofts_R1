<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\HumanResources\Models\EmployeeWorkShift;
use App\Domain\HumanResources\Models\Employee;
use App\Domain\HumanResources\Models\WorkShift;
use Carbon\Carbon;

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
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->addMonth()->endOfMonth();

        // สุ่มเลือกกะงานหลักสำหรับพนักงาน
        $primaryShift = $workShifts->where('code', 'REG')->first()
            ?? $workShifts->first();

        // ดึงรายการกะงานที่มีอยู่แล้วของพนักงานในช่วงเวลาที่ต้องการ
        // - เก็บทั้ง employee_id, work_shift_id, และ effective_date เพื่อเช็คว่ามีอยู่แล้วหรือไม่
        $existingShifts = EmployeeWorkShift::where('employee_id', $employee->id)
            ->whereBetween('effective_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get(['employee_id', 'work_shift_id', 'effective_date'])
            ->map(function($shift) {
                return $shift->employee_id . '-' . $shift->work_shift_id . '-' . Carbon::parse($shift->effective_date)->format('Y-m-d');
            })
            ->toArray();
        
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            // ข้ามวันเสาร์-อาทิตย์ถ้าเป็นกะปกติ
            if ($primaryShift->code === 'REG' && ($currentDate->isWeekend())) {
                $currentDate->addDay();
                continue;
            }

            // ตรวจสอบว่ามีกะงานในวันนี้อยู่แล้วหรือไม่
            $dateString = $currentDate->format('Y-m-d');
            $shiftKey = $employee->id . '-' . $primaryShift->id . '-' . $dateString;
            
            if (!in_array($shiftKey, $existingShifts)) {
                try {
                    EmployeeWorkShift::create([
                        'employee_id' => $employee->id,
                        'work_shift_id' => $primaryShift->id,
                        'effective_date' => $dateString,
                        'status' => 'scheduled',
                        'notes' => null,
                        'metadata' => json_encode([
                            'assigned_by' => 'system',
                            'assigned_at' => now()->toDateTimeString(),
                            'shift_type' => 'regular'
                        ])
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    // ถ้าเกิด unique constraint violation ให้ข้ามไป
                    if (strpos($e->getMessage(), 'UNIQUE constraint failed') === false) {
                        throw $e;
                    }
                }
            }

            $currentDate->addDay();
        }
    }
}
