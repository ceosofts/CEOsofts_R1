<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SkipMigration extends Command
{
    protected $signature = 'migrate:skip {migration : Migration filename to skip} {--batch=null : Specify batch number (default is latest)}';

    protected $description = 'Mark a migration as completed without actually running it';

    public function handle()
    {
        $migration = $this->argument('migration');
        $batchOption = $this->option('batch');
        
        // ตรวจสอบว่า migration มีอยู่หรือไม่
        $migrationPath = database_path('migrations/' . $migration . '.php');
        
        if (!file_exists($migrationPath)) {
            $this->error("Migration file not found: {$migrationPath}");
            return 1;
        }
        
        // ตรวจสอบว่า migration นี้ถูกรันไปแล้วหรือยัง
        $exists = DB::table('migrations')->where('migration', $migration)->exists();
        
        if ($exists) {
            $this->info("Migration {$migration} is already marked as completed");
            return 0;
        }
        
        // กำหนดเลข batch
        $batch = $batchOption !== 'null' ? (int)$batchOption : (DB::table('migrations')->max('batch') + 1);
        
        // เพิ่ม migration ลงในตาราง
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => $batch
        ]);
        
        $this->info("Migration {$migration} has been marked as completed (batch {$batch})");
        
        return 0;
    }
}
