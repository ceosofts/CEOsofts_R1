<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SkipMigrationCommand extends Command
{
    /**
     * ชื่อคำสั่งและคำอธิบาย
     *
     * @var string
     */
    protected $signature = 'migrate:skip {migration : The migration file to skip}';

    /**
     * คำอธิบายคำสั่ง
     *
     * @var string
     */
    protected $description = 'Skip a migration by marking it as completed without running it';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $migration = $this->argument('migration');
        
        // ตรวจสอบว่ามีตาราง migrations หรือไม่
        if (!$this->hasTable('migrations')) {
            $this->error('Table "migrations" does not exist. Please run "php artisan migrate:install" first.');
            return 1;
        }
        
        // ตรวจสอบว่ามี migration ที่ระบุหรือไม่
        $existingMigration = DB::table('migrations')
            ->where('migration', $migration)
            ->first();
        
        if ($existingMigration) {
            $this->info("Migration '{$migration}' already exists in the migrations table.");
            return 0;
        }
        
        // เพิ่ม migration เข้าไปในตาราง migrations
        $batch = DB::table('migrations')->max('batch') + 1;
        
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => $batch,
        ]);
        
        $this->info("Successfully marked migration '{$migration}' as completed (batch {$batch}).");
        
        return 0;
    }
    
    /**
     * ตรวจสอบว่ามีตารางหรือไม่
     *
     * @param string $table
     * @return bool
     */
    protected function hasTable($table)
    {
        try {
            return DB::getSchemaBuilder()->hasTable($table);
        } catch (\Exception $e) {
            return false;
        }
    }
}
