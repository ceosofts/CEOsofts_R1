<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixDuplicateEmployeeFields extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:fix-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and fix duplicate employee emails and codes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for duplicate employee emails and codes...');
        
        // Check for duplicate emails
        $duplicateEmails = DB::table('employees')
            ->select('email', DB::raw('COUNT(*) as count'))
            ->whereNotNull('email')
            ->groupBy('email')
            ->having('count', '>', 1)
            ->get();
            
        if ($duplicateEmails->count() > 0) {
            $this->error('Found duplicate emails:');
            foreach ($duplicateEmails as $email) {
                $this->line("Email: {$email->email}, Count: {$email->count}");
                
                // Show the duplicate records
                $duplicates = Employee::where('email', $email->email)->get();
                $this->table(['ID', 'Employee Code', 'Name', 'Email'], 
                    $duplicates->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'code' => $item->employee_code,
                            'name' => $item->first_name . ' ' . $item->last_name,
                            'email' => $item->email
                        ];
                    }));
            }
        } else {
            $this->info('No duplicate emails found.');
        }
        
        // Check for duplicate employee codes
        $duplicateCodes = DB::table('employees')
            ->select('employee_code', DB::raw('COUNT(*) as count'))
            ->whereNotNull('employee_code')
            ->groupBy('employee_code')
            ->having('count', '>', 1)
            ->get();
            
        if ($duplicateCodes->count() > 0) {
            $this->error('Found duplicate employee codes:');
            foreach ($duplicateCodes as $code) {
                $this->line("Code: {$code->employee_code}, Count: {$code->count}");
                
                // Show the duplicate records
                $duplicates = Employee::where('employee_code', $code->employee_code)->get();
                $this->table(['ID', 'Employee Code', 'Name', 'Email'], 
                    $duplicates->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'code' => $item->employee_code,
                            'name' => $item->first_name . ' ' . $item->last_name,
                            'email' => $item->email
                        ];
                    }));
            }
        } else {
            $this->info('No duplicate employee codes found.');
        }
        
        // Add verification for unique constraints in database
        $this->info('Checking database unique constraints...');
        
        $uniqueConstraints = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_NAME = 'employees' 
            AND CONSTRAINT_TYPE = 'UNIQUE'
        ");
        
        if (count($uniqueConstraints) > 0) {
            $this->info('Found unique constraints:');
            foreach ($uniqueConstraints as $constraint) {
                $this->line($constraint->CONSTRAINT_NAME);
            }
        } else {
            $this->warn('No unique constraints found in database. This could be part of the problem.');
        }

        $this->info('Analysis complete.');
    }
}
