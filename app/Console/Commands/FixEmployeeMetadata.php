<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;

class FixEmployeeMetadata extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:employee-metadata';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix double-encoded JSON in employee metadata fields';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fix employee metadata...');
        
        $count = 0;
        $employees = Employee::all();
        
        foreach ($employees as $employee) {
            if (!$employee->metadata || !is_string($employee->getRawOriginal('metadata'))) {
                continue;
            }
            
            $rawMetadata = $employee->getRawOriginal('metadata');
            $decoded = json_decode($rawMetadata, true);
            
            // ตรวจสอบว่ามีการ encode ซ้อนกันหรือไม่
            if (is_string($decoded)) {
                $nested = json_decode($decoded, true);
                
                if (is_array($nested)) {
                    // ถ้ามีการ encode ซ้อนกัน ให้บันทึกค่าที่ decode แล้วเพียงครั้งเดียว
                    $employee->metadata = $nested;
                    $employee->save();
                    $count++;
                    
                    $this->info("Fixed metadata for employee {$employee->id}: {$employee->first_name} {$employee->last_name}");
                }
            }
        }
        
        $this->info("Finished! Fixed {$count} employee metadata records.");
        
        return Command::SUCCESS;
    }
}
