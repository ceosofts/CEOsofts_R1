<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixEmployeeFields extends Command
{
    protected $signature = 'fix:employee-fields';
    protected $description = 'Fix inconsistent field names in employees table';

    public function handle()
    {
        // ตรวจสอบว่ามีฟิลด์ birth_date แต่ไม่มี birthdate หรือไม่
        if (Schema::hasColumn('employees', 'birth_date') && !Schema::hasColumn('employees', 'birthdate')) {
            $this->info('Converting birth_date to birthdate...');
            
            // สร้าง migration ที่เรียกใช้งานทันที
            Schema::table('employees', function($table) {
                $table->date('birthdate')->nullable();
            });
            
            // คัดลอกข้อมูลจาก birth_date ไปยัง birthdate
            DB::statement('UPDATE employees SET birthdate = birth_date');
            
            $this->info('Field converted successfully');
        } else {
            $this->info('No field conversion needed');
        }

        // ตรวจสอบเงื่อนไขอื่นๆ ตามต้องการ...
        
        $this->info('Employee fields fixed successfully');
        
        return Command::SUCCESS;
    }
}
