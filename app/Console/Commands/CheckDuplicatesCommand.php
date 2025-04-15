<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckDuplicatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for duplicate entries in various tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for duplicate email entries in employees table...');
        
        $duplicateEmails = DB::table('employees')
            ->select('email', DB::raw('COUNT(*) as count'))
            ->whereNotNull('email')
            ->groupBy('email')
            ->having('count', '>', 1)
            ->get();
            
        if ($duplicateEmails->count() > 0) {
            $this->error('Found duplicate emails:');
            foreach ($duplicateEmails as $email) {
                $this->info("Email: {$email->email}, Count: {$email->count}");
                
                // แสดงข้อมูลที่ซ้ำ
                $duplicates = DB::table('employees')
                    ->where('email', $email->email)
                    ->get();
                
                $this->table(['ID', 'Email', 'Employee Code', 'Name', 'Company ID'], 
                    $duplicates->map(function($item) {
                        return [
                            'id' => $item->id,
                            'email' => $item->email,
                            'code' => $item->employee_code,
                            'name' => $item->first_name . ' ' . $item->last_name,
                            'company_id' => $item->company_id
                        ];
                    }));
            }
        } else {
            $this->info('No duplicate emails found.');
        }
        
        // ตรวจสอบ employee_code ซ้ำ
        $this->info('Checking for duplicate employee code entries...');
        
        $duplicateCodes = DB::table('employees')
            ->select('employee_code', DB::raw('COUNT(*) as count'))
            ->whereNotNull('employee_code')
            ->groupBy('employee_code')
            ->having('count', '>', 1)
            ->get();
            
        if ($duplicateCodes->count() > 0) {
            $this->error('Found duplicate employee codes:');
            foreach ($duplicateCodes as $code) {
                $this->info("Code: {$code->employee_code}, Count: {$code->count}");
                
                // แสดงข้อมูลที่ซ้ำ
                $duplicates = DB::table('employees')
                    ->where('employee_code', $code->employee_code)
                    ->get();
                
                $this->table(['ID', 'Email', 'Employee Code', 'Name', 'Company ID'], 
                    $duplicates->map(function($item) {
                        return [
                            'id' => $item->id,
                            'email' => $item->email,
                            'code' => $item->employee_code,
                            'name' => $item->first_name . ' ' . $item->last_name,
                            'company_id' => $item->company_id
                        ];
                    }));
            }
        } else {
            $this->info('No duplicate employee codes found.');
        }
        
        // แสดงข้อมูลคอลัมน์ในตาราง employees (SQLite compatibility)
        $this->info('Current columns in employees table:');
        $columns = Schema::getColumnListing('employees');
        $this->info(implode(', ', $columns));
        
        // แสดงข้อมูล indexes (SQLite compatible)
        $this->info('Current indexes in employees table:');
        try {
            // สำหรับ SQLite
            if (DB::connection()->getDriverName() === 'sqlite') {
                $indexes = DB::select("SELECT name, sql FROM sqlite_master WHERE type = 'index' AND tbl_name = 'employees'");
                foreach ($indexes as $index) {
                    $this->line("{$index->name}: {$index->sql}");
                }
            } 
            // สำหรับ MySQL
            else {
                $constraints = DB::select("
                    SELECT CONSTRAINT_NAME
                    FROM information_schema.TABLE_CONSTRAINTS
                    WHERE TABLE_NAME = 'employees' 
                    AND CONSTRAINT_TYPE = 'UNIQUE'
                ");
                foreach ($constraints as $constraint) {
                    $this->line($constraint->CONSTRAINT_NAME);
                }
            }
        } catch (\Exception $e) {
            $this->warn("Could not retrieve index information: " . $e->getMessage());
        }
    }
}
