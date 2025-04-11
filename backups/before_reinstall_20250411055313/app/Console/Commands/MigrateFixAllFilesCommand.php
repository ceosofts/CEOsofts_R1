<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class MigrateFixAllFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:fix-all-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'แก้ไขไฟล์ migration ทั้งหมดที่มีปัญหา';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('กำลังแก้ไขปัญหา migrations ทั้งหมด...');

        // 1. แก้ไขไฟล์ migration
        $this->info('1. แก้ไขไฟล์ migration');
        $this->call('migrate:fix-files');

        // 2. แก้ไข migration status ใน database
        $this->info("\n2. ตรวจสอบสถานะ migrations");
        $this->call('migrate:status');

        // 3. ถามว่าต้องการ migrate หรือ skip migrations ที่มีปัญหา
        $choice = $this->choice(
            'คุณต้องการดำเนินการอย่างไรกับ migrations ที่ยังไม่ได้รัน?',
            ['รัน migrate', 'ข้าม migrations ที่มีปัญหา (skip)', 'ไม่ดำเนินการใดๆ'],
            0
        );

        if ($choice === 'รัน migrate') {
            $this->info("\nกำลังรัน migrations...");
            $this->call('migrate');
        } elseif ($choice === 'ข้าม migrations ที่มีปัญหา (skip)') {
            // ดึงรายการ migrations ที่ยังไม่ได้รัน
            $this->info("\nกำลังข้าม migrations ที่ยังไม่ได้รัน...");
            $this->skipPendingMigrations();
        }

        $this->info("\nเสร็จสิ้น! กระบวนการแก้ไขปัญหา migrations เรียบร้อยแล้ว.");

        return Command::SUCCESS;
    }

    /**
     * ข้าม migrations ที่ยังไม่ได้รัน
     */
    protected function skipPendingMigrations()
    {
        // รัน migrate:status และวิเคราะห์ผลลัพธ์
        $process = Process::fromShellCommandline('php artisan migrate:status --no-ansi');
        $process->run();
        $output = $process->getOutput();

        // แยกบรรทัด
        $lines = explode("\n", $output);
        $pendingMigrations = [];

        // วิเคราะห์หา migrations ที่ยังไม่ได้รัน (มี No หรือ Pending ในสถานะ)
        foreach ($lines as $line) {
            if (preg_match('/\|\s+(\d{4}_\d{2}_\d{2}_\d{6}_[a-z0-9_]+)\s+\|\s+(No|Pending)\s+\|/', $line, $matches)) {
                $pendingMigrations[] = $matches[1];
            }
        }

        if (empty($pendingMigrations)) {
            $this->info('ไม่พบ migrations ที่ยังไม่ได้รัน');
            return;
        }

        $this->info('พบ migrations ที่ยังไม่ได้รัน ' . count($pendingMigrations) . ' รายการ');
        
        // ข้ามแต่ละ migration
        foreach ($pendingMigrations as $migration) {
            $this->info("กำลังข้าม migration: {$migration}");
            $this->call('migrate:skip', ['migration' => $migration]);
        }
    }
}
