<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FixDuplicateEmployeeRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:fix-duplicates {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix duplicate employee records by making email and employee_code unique per company';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->info('Running in dry-run mode. No changes will be made.');
        }

        $this->info('Checking for duplicate employee records...');

        // 1. จัดกลุ่มพนักงานตามบริษัท
        $this->info('Step 1: Grouping employees by company...');
        $companies = DB::table('employees')->select('company_id')->distinct()->get();

        foreach ($companies as $company) {
            $companyId = $company->company_id;
            $this->info("Processing company ID: {$companyId}");

            // 2. ตรวจสอบและแก้ไขอีเมลซ้ำในแต่ละบริษัท
            $duplicateEmails = $this->getDuplicateEmails($companyId);
            if (count($duplicateEmails) > 0) {
                $this->fixDuplicateEmails($duplicateEmails, $dryRun);
            } else {
                $this->info("No duplicate emails found for company ID: {$companyId}");
            }

            // 3. ตรวจสอบและแก้ไขรหัสพนักงานซ้ำในแต่ละบริษัท
            $duplicateCodes = $this->getDuplicateEmployeeCodes($companyId);
            if (count($duplicateCodes) > 0) {
                $this->fixDuplicateEmployeeCodes($duplicateCodes, $dryRun);
            } else {
                $this->info("No duplicate employee codes found for company ID: {$companyId}");
            }
        }

        // 4. สร้าง migration เพื่อเพิ่ม unique constraints
        if (!$dryRun) {
            $this->info('Creating migration for unique constraints...');
            $this->call('make:migration', [
                'name' => 'add_unique_constraints_to_employees_table',
                '--path' => 'database/migrations'
            ]);
            
            $this->info('Please update the newly created migration file to add these constraints:');
            $this->info('- Unique constraint for [company_id, email]');
            $this->info('- Unique constraint for [company_id, employee_code]');
            $this->info('Then run: php artisan migrate');
        }

        $this->info('Done!');
    }

    /**
     * Get duplicate emails within a company
     */
    private function getDuplicateEmails($companyId)
    {
        return DB::table('employees')
            ->select('email', DB::raw('COUNT(*) as count'))
            ->where('company_id', $companyId)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->groupBy('email')
            ->having('count', '>', 1)
            ->get()
            ->pluck('email')
            ->toArray();
    }

    /**
     * Fix duplicate emails
     */
    private function fixDuplicateEmails($emails, $dryRun)
    {
        $this->info("Found " . count($emails) . " duplicate email(s) to fix");
        
        foreach ($emails as $email) {
            $duplicates = DB::table('employees')
                ->where('email', $email)
                ->orderBy('id')
                ->get();
            
            $this->info("Processing duplicates for email: {$email}");
            
            // Keep the first record unchanged
            $first = true;
            foreach ($duplicates as $employee) {
                if ($first) {
                    $this->info("Keeping original email for ID: {$employee->id}");
                    $first = false;
                    continue;
                }
                
                // Generate a unique email for the duplicate
                $newEmail = $this->generateUniqueEmail($employee->email, $employee->id);
                $this->info("Updating email for ID: {$employee->id} from '{$employee->email}' to '{$newEmail}'");
                
                if (!$dryRun) {
                    DB::table('employees')
                        ->where('id', $employee->id)
                        ->update(['email' => $newEmail]);
                }
            }
        }
    }

    /**
     * Generate a unique email
     */
    private function generateUniqueEmail($email, $id)
    {
        $parts = explode('@', $email);
        $newEmail = $parts[0] . '+' . $id . '@' . $parts[1];
        return $newEmail;
    }

    /**
     * Get duplicate employee codes within a company
     */
    private function getDuplicateEmployeeCodes($companyId)
    {
        return DB::table('employees')
            ->select('employee_code', DB::raw('COUNT(*) as count'))
            ->where('company_id', $companyId)
            ->whereNotNull('employee_code')
            ->where('employee_code', '!=', '')
            ->groupBy('employee_code')
            ->having('count', '>', 1)
            ->get()
            ->pluck('employee_code')
            ->toArray();
    }

    /**
     * Fix duplicate employee codes
     */
    private function fixDuplicateEmployeeCodes($codes, $dryRun)
    {
        $this->info("Found " . count($codes) . " duplicate employee code(s) to fix");
        
        foreach ($codes as $code) {
            $duplicates = DB::table('employees')
                ->where('employee_code', $code)
                ->orderBy('id')
                ->get();
            
            $this->info("Processing duplicates for code: {$code}");
            
            // Keep the first record unchanged
            $first = true;
            foreach ($duplicates as $employee) {
                if ($first) {
                    $this->info("Keeping original code for ID: {$employee->id}");
                    $first = false;
                    continue;
                }
                
                // Generate a unique code for the duplicate
                $newCode = $this->generateUniqueCode($employee->employee_code, $employee->id);
                $this->info("Updating code for ID: {$employee->id} from '{$employee->employee_code}' to '{$newCode}'");
                
                if (!$dryRun) {
                    DB::table('employees')
                        ->where('id', $employee->id)
                        ->update(['employee_code' => $newCode]);
                }
            }
        }
    }

    /**
     * Generate a unique employee code
     */
    private function generateUniqueCode($code, $id)
    {
        return $code . '-' . $id;
    }
}
