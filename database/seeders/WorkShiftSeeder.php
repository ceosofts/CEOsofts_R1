<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Organization\Models\Company;
use App\Domain\HumanResources\Models\WorkShift; // แก้ไขเป็น namespace ที่ถูกต้อง
use Carbon\Carbon;

class WorkShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();
        foreach ($companies as $company) {
            $this->createWorkShiftsForCompany($company);
        }
    }

    /**
     * สร้างกะการทำงานสำหรับบริษัท
     */
    private function createWorkShiftsForCompany(Company $company): void
    {
        // สร้างรหัสที่ไม่ซ้ำกันโดยเพิ่มคำนำหน้าจาก company code
        $companyPrefix = substr($company->code, 0, 3); // ใช้ 3 ตัวแรกจาก company code
        $companyName = $company->name; // ใช้ชื่อบริษัทในการทำให้ชื่อกะงานไม่ซ้ำกัน

        // กะปกติ 8:30-17:30
        WorkShift::firstOrCreate(
            [
                'company_id' => $company->id,
                'code' => $companyPrefix . '_REG', // เพิ่ม prefix ที่ไม่ซ้ำกัน
            ],
            [
                'name' => 'กะปกติ - ' . $companyName, // เพิ่มชื่อบริษัท
                'description' => 'เวลาทำงานปกติ 8:30-17:30',
                'start_time' => Carbon::parse('08:30:00'),
                'end_time' => Carbon::parse('17:30:00'),
                'break_start' => Carbon::parse('12:00:00'),
                'break_end' => Carbon::parse('13:00:00'),
                'working_hours' => 8,
                'is_night_shift' => false,
                'is_active' => true,
                'color' => '#4CAF50',
                'metadata' => [
                    'grace_period' => 15, // จำนวนนาทีที่อนุญาตให้สาย
                    'days_of_week' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
                    'overtime_rate' => 1.5
                ]
            ]
        );

        // กะเช้า 06:00-15:00
        WorkShift::firstOrCreate(
            [
                'company_id' => $company->id,
                'code' => $companyPrefix . '_AM', // เพิ่ม prefix ที่ไม่ซ้ำกัน
            ],
            [
                'name' => 'กะเช้า - ' . $companyName, // เพิ่มชื่อบริษัท
                'description' => 'กะเช้า 06:00-15:00',
                'start_time' => Carbon::parse('06:00:00'),
                'end_time' => Carbon::parse('15:00:00'),
                'break_start' => Carbon::parse('10:00:00'),
                'break_end' => Carbon::parse('11:00:00'),
                'working_hours' => 8,
                'is_night_shift' => false,
                'is_active' => true,
                'color' => '#2196F3',
                'metadata' => [
                    'grace_period' => 10,
                    'days_of_week' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    'overtime_rate' => 1.5
                ]
            ]
        );

        // กะบ่าย 15:00-00:00
        WorkShift::firstOrCreate(
            [
                'company_id' => $company->id,
                'code' => $companyPrefix . '_PM', // เพิ่ม prefix ที่ไม่ซ้ำกัน
            ],
            [
                'name' => 'กะบ่าย - ' . $companyName, // เพิ่มชื่อบริษัท
                'description' => 'กะบ่าย 15:00-00:00',
                'start_time' => Carbon::parse('15:00:00'),
                'end_time' => Carbon::parse('00:00:00'),
                'break_start' => Carbon::parse('19:00:00'),
                'break_end' => Carbon::parse('20:00:00'),
                'working_hours' => 8,
                'is_night_shift' => true,
                'is_active' => true,
                'color' => '#FF9800',
                'metadata' => [
                    'grace_period' => 10,
                    'days_of_week' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    'overtime_rate' => 1.7
                ]
            ]
        );

        // กะกลางคืน 00:00-09:00
        WorkShift::firstOrCreate(
            [
                'company_id' => $company->id,
                'code' => $companyPrefix . '_NIGHT', // เพิ่ม prefix ที่ไม่ซ้ำกัน
            ],
            [
                'name' => 'กะกลางคืน - ' . $companyName, // เพิ่มชื่อบริษัท
                'description' => 'กะกลางคืน 00:00-09:00',
                'start_time' => Carbon::parse('00:00:00'),
                'end_time' => Carbon::parse('09:00:00'),
                'break_start' => Carbon::parse('04:00:00'),
                'break_end' => Carbon::parse('05:00:00'),
                'working_hours' => 8,
                'is_night_shift' => true,
                'is_active' => true,
                'color' => '#9C27B0',
                'metadata' => [
                    'grace_period' => 10,
                    'days_of_week' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sun'],
                    'overtime_rate' => 2.0
                ]
            ]
        );
    }
}
