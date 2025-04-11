<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateSkipCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:skip {migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark a migration as completed without running it';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $migrationName = $this->argument('migration');
        
        // Find all migrations that match the provided name (pattern matching)
        $migrations = glob(database_path("migrations/*{$migrationName}*.php"));
        
        if (empty($migrations)) {
            $this->error("ไม่พบไฟล์ migration ที่ตรงกับ '{$migrationName}'");
            return 1;
        }
        
        // If multiple migrations found, let user choose
        if (count($migrations) > 1) {
            $this->info("พบ migration หลายไฟล์ที่ตรงกับเงื่อนไข:");
            
            foreach ($migrations as $key => $path) {
                $filename = basename($path);
                $this->line("[{$key}] {$filename}");
            }
            
            $choice = $this->ask('กรุณาเลือกไฟล์ที่ต้องการข้าม (ตัวเลข)');
            if (!isset($migrations[$choice])) {
                $this->error('ตัวเลือกไม่ถูกต้อง');
                return 1;
            }
            
            $migrationPath = $migrations[$choice];
        } else {
            $migrationPath = $migrations[0];
        }
        
        $filename = basename($migrationPath);
        $migration = str_replace('.php', '', $filename);
        
        // Check if the migration has already been run
        $exists = DB::table('migrations')->where('migration', $migration)->exists();
        
        if ($exists) {
            $this->info("Migration '{$migration}' ถูกประมวลผลแล้ว");
            return 0;
        }
        
        // Add the migration to the migrations table
        DB::table('migrations')->insert([
            'migration' => $migration,
            'batch' => DB::table('migrations')->max('batch') + 1
        ]);
        
        $this->info("Migration '{$migration}' ถูกข้ามและทำเครื่องหมายว่าเสร็จสิ้นแล้ว");
        
        return 0;
    }
}
