<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Unit;
use Illuminate\Database\QueryException;

class NormalizeUnitCodes extends Command
{
    protected $signature = 'units:normalize-codes {--force : Run without confirmation}';
    protected $description = 'ปรับเปลี่ยนรหัสหน่วยนับทั้งหมดให้เป็นรูปแบบ UNI-XX-XXX';

    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('การทำงานนี้จะอัปเดตรหัสหน่วยนับทั้งหมด คุณต้องการทำต่อหรือไม่?')) {
            $this->info('ยกเลิกการทำงาน');
            return 0;
        }

        try {
            $this->info('กำลังเริ่มการทำงาน...');
            $count = Unit::normalizeAllUnitCodes();
            $this->info("ปรับปรุงรหัสหน่วยนับสำเร็จ {$count} รายการ");
            return 0;
        } catch (QueryException $e) {
            $this->error("เกิดข้อผิดพลาด: {$e->getMessage()}");
            return 1;
        }
    }
}
