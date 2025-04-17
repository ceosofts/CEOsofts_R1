<?php

namespace App\Console\Commands;

use App\Models\Unit;
use Illuminate\Console\Command;

class CheckUnitCodes extends Command
{
    protected $signature = 'units:check-codes';
    protected $description = 'ตรวจสอบรหัสหน่วยทั้งหมดในระบบ';

    public function handle()
    {
        $units = Unit::all();
        
        $this->info("พบรหัสหน่วยทั้งหมด {$units->count()} รายการ:");
        
        $grouped = $units->groupBy('company_id');
        
        foreach ($grouped as $companyId => $companyUnits) {
            $this->info("\nบริษัท ID: {$companyId} (มี {$companyUnits->count()} หน่วย)");
            $this->table(
                ['ID', 'ชื่อ', 'รหัส', 'หน่วยฐาน', 'สถานะ'],
                $companyUnits->map(function ($unit) {
                    return [
                        'id' => $unit->id,
                        'name' => $unit->name,
                        'code' => $unit->code,
                        'base_unit_id' => $unit->base_unit_id ?? 'NULL',
                        'active' => $unit->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน',
                    ];
                })
            );
        }
        
        // ตรวจสอบรูปแบบรหัสที่ไม่ตรงมาตรฐาน
        $nonStandardCodes = $units->filter(function ($unit) {
            return !preg_match('/^UNI-\d{2}-\d{3}$/', $unit->code);
        });
        
        if ($nonStandardCodes->count() > 0) {
            $this->error("\nพบรหัสที่ไม่เป็นไปตามมาตรฐาน UNI-XX-XXX จำนวน {$nonStandardCodes->count()} รายการ:");
            $this->table(
                ['ID', 'บริษัท', 'ชื่อ', 'รหัสปัจจุบัน', 'แนะนำรหัสใหม่'],
                $nonStandardCodes->map(function ($unit) {
                    return [
                        'id' => $unit->id,
                        'company_id' => $unit->company_id,
                        'name' => $unit->name,
                        'code' => $unit->code,
                        'suggested_code' => Unit::generateUnitCode($unit->company_id),
                    ];
                })
            );
        } else {
            $this->info("\nทุกรหัสเป็นไปตามมาตรฐาน UNI-XX-XXX");
        }
        
        return 0;
    }
}
