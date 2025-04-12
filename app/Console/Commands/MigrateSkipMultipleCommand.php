<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateSkipMultipleCommand extends Command
{
    protected $signature = 'migrate:skip-multiple {--migrations=* : List of migration files to skip}';
    protected $description = 'Mark multiple migrations as completed without running them';

    public function handle()
    {
        $migrationNames = $this->option('migrations');

        if (empty($migrationNames)) {
            $this->error('กรุณาระบุชื่อ migration ที่ต้องการข้าม ตัวอย่าง: --migrations=create_users_table --migrations=create_cache_table');
            return 1;
        }

        $batch = DB::table('migrations')->max('batch') + 1;
        $skipped = [];

        foreach ($migrationNames as $partialName) {
            // ค้นหาไฟล์ migration ที่ตรงกับ pattern
            $migrations = glob(database_path("migrations/*{$partialName}*.php"));

            if (empty($migrations)) {
                $this->warn("ไม่พบ migration ที่ตรงกับ '{$partialName}'");
                continue;
            }

            foreach ($migrations as $migrationPath) {
                $filename = basename($migrationPath);
                $migration = str_replace('.php', '', $filename);

                // ตรวจสอบว่า migration นี้ถูกรันไปแล้วหรือไม่
                $exists = DB::table('migrations')->where('migration', $migration)->exists();

                if ($exists) {
                    $this->line("✓ Migration '{$migration}' ถูกประมวลผลแล้ว (ข้าม)");
                    continue;
                }

                // เพิ่ม migration นี้ลงในตาราง migrations โดยไม่รันจริง
                DB::table('migrations')->insert([
                    'migration' => $migration,
                    'batch' => $batch
                ]);

                $skipped[] = $migration;
                $this->info("✓ Migration '{$migration}' ถูกข้ามและทำเครื่องหมายว่าเสร็จสิ้นแล้ว");
            }
        }

        if (count($skipped) > 0) {
            $this->newLine();
            $this->info("ข้าม migration ทั้งหมด " . count($skipped) . " รายการสำเร็จ");
        } else {
            $this->warn("ไม่มี migration ที่ถูกข้าม");
        }

        return 0;
    }
}
