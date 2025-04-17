<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;

class CheckDuplicateUnits extends Command
{
    protected $signature = 'units:check-duplicates';
    protected $description = 'ตรวจสอบหน่วยนับซ้ำซ้อนในระบบ';

    public function handle()
    {
        $this->info("กำลังตรวจสอบหน่วยนับซ้ำซ้อน...");
        
        // ตรวจหาชื่อหน่วยซ้ำในบริษัทเดียวกัน
        $duplicateNames = DB::table('units')
            ->select('company_id', 'name', DB::raw('COUNT(*) as count'))
            ->whereNull('deleted_at')
            ->groupBy('company_id', 'name')
            ->having('count', '>', 1)
            ->get();
            
        if ($duplicateNames->count() > 0) {
            $this->warn("พบชื่อหน่วยซ้ำในบริษัทเดียวกัน:");
            foreach ($duplicateNames as $duplicate) {
                $this->line("บริษัท ID: {$duplicate->company_id}, ชื่อ: {$duplicate->name}, จำนวน: {$duplicate->count}");
                
                // แสดงรายละเอียดข้อมูลซ้ำ
                $units = Unit::where('company_id', $duplicate->company_id)
                    ->where('name', $duplicate->name)
                    ->get();
                
                $this->table(['ID', 'Code', 'Description'], 
                    $units->map(function($unit) {
                        return [$unit->id, $unit->code, $unit->description];
                    }));
            }
        } else {
            $this->info("ไม่พบชื่อหน่วยซ้ำในบริษัทเดียวกัน");
        }
        
        // ตรวจหารหัสหน่วยซ้ำในบริษัทเดียวกัน
        $duplicateCodes = DB::table('units')
            ->select('company_id', 'code', DB::raw('COUNT(*) as count'))
            ->whereNull('deleted_at')
            ->groupBy('company_id', 'code')
            ->having('count', '>', 1)
            ->get();
            
        if ($duplicateCodes->count() > 0) {
            $this->warn("พบรหัสหน่วยซ้ำในบริษัทเดียวกัน:");
            foreach ($duplicateCodes as $duplicate) {
                $this->line("บริษัท ID: {$duplicate->company_id}, รหัส: {$duplicate->code}, จำนวน: {$duplicate->count}");
            }
        } else {
            $this->info("ไม่พบรหัสหน่วยซ้ำในบริษัทเดียวกัน");
        }
        
        return 0;
    }
}
