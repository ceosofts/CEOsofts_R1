<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MigrationCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:cleanup {migration? : ชื่อ migration ที่ต้องการลบ} 
                            {--all-pending : ทำเครื่องหมายทุก migration ที่ pending ว่าเสร็จสิ้นแล้ว}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ลบรายการ migration ที่มีปัญหาจากตาราง migrations หรือทำเครื่องหมายว่าเสร็จสิ้น';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('all-pending')) {
            $this->markAllPendingAsCompleted();
            return;
        }

        $migration = $this->argument('migration');

        if ($migration) {
            // ลบ migration เฉพาะที่ระบุ
            $count = DB::table('migrations')->where('migration', $migration)->delete();
            
            if ($count > 0) {
                $this->info("ลบ migration: {$migration} สำเร็จ");
            } else {
                $this->error("ไม่พบ migration: {$migration}");
            }
        } else {
            // แสดงรายการ migration ที่มีอยู่
            $migrations = DB::table('migrations')->orderBy('batch')->get();
            
            $this->info("รายการ migration ทั้งหมด:");
            $this->table(['Migration', 'Batch'], $migrations->map(function($item) {
                return [
                    'migration' => $item->migration,
                    'batch' => $item->batch
                ];
            }));
            
            // ถามว่าต้องการลบ migration ใด
            $migrationToDelete = $this->ask('ระบุชื่อ migration ที่ต้องการลบ (หรือกด Enter เพื่อยกเลิก)');
            
            if ($migrationToDelete) {
                $count = DB::table('migrations')->where('migration', $migrationToDelete)->delete();
                
                if ($count > 0) {
                    $this->info("ลบ migration: {$migrationToDelete} สำเร็จ");
                } else {
                    $this->error("ไม่พบ migration: {$migrationToDelete}");
                }
            }
        }
    }
    
    /**
     * ทำเครื่องหมายทุก migration ที่ pending ว่าเสร็จสิ้นแล้ว
     */
    private function markAllPendingAsCompleted()
    {
        // ดึงรายการ migration ที่ทำแล้ว
        $completedMigrations = DB::table('migrations')->pluck('migration')->all();
        
        // ดึงรายการไฟล์ migrations ทั้งหมด
        $migrationFiles = collect(File::glob(database_path('migrations/*.php')))
            ->map(function ($file) {
                return pathinfo($file, PATHINFO_FILENAME);
            })
            ->toArray();
        
        // หา migration ที่ยังไม่ได้ทำ
        $pendingMigrations = array_diff($migrationFiles, $completedMigrations);
        
        if (count($pendingMigrations) === 0) {
            $this->info('ไม่พบ migration ที่รอดำเนินการ');
            return;
        }
        
        $this->info('พบ migration ที่รอดำเนินการจำนวน ' . count($pendingMigrations) . ' รายการ');
        
        // แสดงรายชื่อ migration ที่รอดำเนินการ
        $this->table(['Migration'], collect($pendingMigrations)->map(function ($migration) {
            return [$migration];
        }));
        
        if ($this->confirm('ต้องการทำเครื่องหมายว่า migration เหล่านี้เสร็จสิ้นแล้วหรือไม่?')) {
            $batch = DB::table('migrations')->max('batch') + 1;
            
            foreach ($pendingMigrations as $migration) {
                DB::table('migrations')->insert([
                    'migration' => $migration,
                    'batch' => $batch
                ]);
                
                $this->info("ทำเครื่องหมายว่า {$migration} เสร็จสิ้นแล้ว");
            }
        }
    }
}
