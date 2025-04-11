<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetMigrationStatus extends Command
{
    protected $signature = 'migrate:reset-status {--all : รีเซ็ตสถานะทั้งหมด} {--pending : ตั้งค่าให้ migration ที่ pending กลับเป็นรันแล้ว}';

    protected $description = 'รีเซ็ตสถานะ migration ใน migrations table โดยไม่ต้องรันไฟล์ migration จริง';

    public function handle()
    {
        if (!$this->confirm('การดำเนินการนี้จะแก้ไขตาราง migrations โดยตรง ต้องการดำเนินการต่อหรือไม่?')) {
            $this->info('ยกเลิกการดำเนินการ');
            return;
        }

        if ($this->option('all')) {
            $this->truncateMigrationsTable();
        } elseif ($this->option('pending')) {
            $this->markPendingMigrationsAsRun();
        } else {
            $this->showMigrationStatus();
            $this->fixSpecificMigrations();
        }
    }

    /**
     * ล้างข้อมูลในตาราง migrations ทั้งหมด
     */
    private function truncateMigrationsTable()
    {
        if ($this->confirm('การดำเนินการนี้จะล้างข้อมูลในตาราง migrations ทั้งหมด แน่ใจหรือไม่?')) {
            DB::table('migrations')->truncate();
            $this->info('ล้างข้อมูลในตาราง migrations เรียบร้อยแล้ว');
        }
    }

    /**
     * ทำเครื่องหมายว่า migration ที่ pending ได้ถูกรันแล้ว
     */
    private function markPendingMigrationsAsRun()
    {
        // ดึงรายการ migration ที่มีอยู่
        $existingMigrations = DB::table('migrations')->pluck('migration')->toArray();
        
        // สแกนหาไฟล์ migration ทั้งหมด
        $migrationFiles = glob(database_path('migrations/*.php'));
        
        $pendingMigrations = [];
        foreach ($migrationFiles as $file) {
            $fileName = basename($file, '.php');
            if (!in_array($fileName, $existingMigrations)) {
                $pendingMigrations[] = $fileName;
            }
        }
        
        if (empty($pendingMigrations)) {
            $this->info('ไม่พบ migration ที่รอดำเนินการ');
            return;
        }
        
        $this->info('พบ migration ที่รอดำเนินการ '. count($pendingMigrations) . ' รายการ:');
        $this->table(['Migration'], array_map(fn($m) => [$m], $pendingMigrations));
        
        if ($this->confirm('ต้องการทำเครื่องหมายว่ารายการเหล่านี้ได้ถูกรันแล้วหรือไม่?')) {
            $latestBatch = DB::table('migrations')->max('batch') ?: 0;
            $latestBatch++;
            
            foreach ($pendingMigrations as $migration) {
                DB::table('migrations')->insert([
                    'migration' => $migration,
                    'batch' => $latestBatch
                ]);
            }
            
            $this->info('ทำเครื่องหมายเรียบร้อยแล้ว');
        }
    }

    /**
     * แสดงสถานะ migration
     */
    private function showMigrationStatus()
    {
        $migrations = DB::table('migrations')->orderBy('batch')->orderBy('migration')->get();
        
        $this->info('Migration status:');
        $this->table(
            ['ID', 'Migration', 'Batch'], 
            $migrations->map(fn($m) => [$m->id, $m->migration, $m->batch])->toArray()
        );
    }

    /**
     * แก้ไข migration ที่ระบุ
     */
    private function fixSpecificMigrations()
    {
        $action = $this->choice(
            'เลือกการดำเนินการ:',
            ['ลบ migration บางรายการ', 'เพิ่ม migration ที่ขาดหายไป', 'ยกเลิก'],
            2
        );
        
        switch ($action) {
            case 'ลบ migration บางรายการ':
                $this->deleteMigrationEntries();
                break;
                
            case 'เพิ่ม migration ที่ขาดหายไป':
                $this->addMissingMigrations();
                break;
                
            default:
                $this->info('ยกเลิกการดำเนินการ');
                break;
        }
    }

    /**
     * ลบรายการ migration ที่ระบุ
     */
    private function deleteMigrationEntries()
    {
        $migrationName = $this->ask('ระบุชื่อ migration ที่ต้องการลบ (หรือส่วนหนึ่งของชื่อ):');
        
        $migrations = DB::table('migrations')
            ->where('migration', 'like', '%' . $migrationName . '%')
            ->get();
            
        if ($migrations->isEmpty()) {
            $this->error('ไม่พบ migration ที่ตรงกับคำค้นหา');
            return;
        }
        
        $this->info('พบ migration ที่ตรงกับคำค้นหา:');
        $this->table(
            ['ID', 'Migration', 'Batch'], 
            $migrations->map(fn($m) => [$m->id, $m->migration, $m->batch])->toArray()
        );
        
        $idsToDelete = $this->ask('ระบุ ID ที่ต้องการลบ (คั่นด้วยเครื่องหมายคอมม่า):');
        $ids = array_map('trim', explode(',', $idsToDelete));
        
        if (!empty($ids)) {
            $count = DB::table('migrations')->whereIn('id', $ids)->delete();
            $this->info("ลบรายการ migration จำนวน {$count} รายการเรียบร้อยแล้ว");
        }
    }

    /**
     * เพิ่ม migration ที่ขาดหายไป
     */
    private function addMissingMigrations()
    {
        $migrationName = $this->ask('ระบุชื่อ migration ที่ต้องการเพิ่ม:');
        
        // ตรวจสอบว่ามีไฟล์นี้จริงหรือไม่
        $migrationFiles = glob(database_path('migrations/*' . $migrationName . '*.php'));
        
        if (empty($migrationFiles)) {
            $this->error('ไม่พบไฟล์ migration ที่ตรงกับคำค้นหา');
            return;
        }
        
        $this->info('พบไฟล์ migration:');
        $fileOptions = [];
        foreach ($migrationFiles as $index => $file) {
            $fileName = basename($file, '.php');
            $fileOptions[$fileName] = $fileName;
            $this->line(($index + 1) . ". {$fileName}");
        }
        
        $selectedMigration = $this->choice('เลือก migration ที่ต้องการเพิ่ม:', $fileOptions);
        $batch = $this->ask('ระบุ batch number:', DB::table('migrations')->max('batch') ?: 1);
        
        DB::table('migrations')->updateOrInsert(
            ['migration' => $selectedMigration],
            ['batch' => $batch]
        );
        
        $this->info("เพิ่มรายการ {$selectedMigration} เรียบร้อยแล้ว");
    }
}
