<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FixMigrationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrations:fix {--check : เช็คปัญหาโดยไม่แก้ไข}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'แก้ไขปัญหาเฉพาะหน้าใน Migration ที่พบบ่อย';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('เริ่มตรวจสอบและแก้ไข Migration...');

        $migrationPath = database_path('migrations');
        $migrations = File::glob("{$migrationPath}/*.php");

        $fixed = false;

        foreach ($migrations as $migration) {
            $content = File::get($migration);
            $filename = basename($migration);
            $modified = $content;

            // ตรวจสอบการใช้ Log แต่ไม่มี import
            if (preg_match('/\\\\Log::|Log::/', $content) && !Str::contains($content, 'use Illuminate\Support\Facades\Log;')) {
                $this->line("พบการใช้ Log ใน {$filename} แต่ไม่มี import");

                if (!$this->option('check')) {
                    // เพิ่ม import Log
                    $modified = preg_replace(
                        '/(use Illuminate\\\\.*?;\n)/s',
                        '$1use Illuminate\\Support\\Facades\\Log;' . PHP_EOL,
                        $content
                    );

                    // ปรับปรุงการใช้ \Log เป็น Log
                    $modified = str_replace('\\Log::', 'Log::', $modified);

                    File::put($migration, $modified);
                    $this->info("✓ แก้ไข {$filename} เรียบร้อยแล้ว");
                    $fixed = true;
                }
            }

            // เพิ่มการตรวจสอบสำหรับ DB, Schema ฯลฯ
            foreach (
                [
                    'DB' => 'use Illuminate\\Support\\Facades\\DB;',
                    'Schema' => 'use Illuminate\\Support\\Facades\\Schema;',
                    'Validator' => 'use Illuminate\\Support\\Facades\\Validator;',
                    'Hash' => 'use Illuminate\\Support\\Facades\\Hash;',
                    'Auth' => 'use Illuminate\\Support\\Facades\\Auth;'
                ] as $facade => $import
            ) {
                if (preg_match('/\\\\' . $facade . '::|\b' . $facade . '::/', $content) && !Str::contains($content, $import)) {
                    $this->line("พบการใช้ {$facade} ใน {$filename} แต่ไม่มี import");

                    if (!$this->option('check')) {
                        // เพิ่ม import
                        $modified = preg_replace(
                            '/(use Illuminate\\\\.*?;\n)/s',
                            '$1' . $import . PHP_EOL,
                            $modified
                        );

                        // ปรับปรุงการใช้ \Facade เป็น Facade
                        $modified = str_replace('\\' . $facade . '::', $facade . '::', $modified);

                        File::put($migration, $modified);
                        $this->info("✓ แก้ไขการใช้ {$facade} ใน {$filename} เรียบร้อยแล้ว");
                        $fixed = true;
                    }
                }
            }
        }

        if ($this->option('check')) {
            $this->info('ตรวจสอบเสร็จสิ้น ไม่มีการแก้ไขไฟล์');
            return 0;
        } elseif ($fixed) {
            $this->info('แก้ไขปัญหาใน Migration เรียบร้อยแล้ว');
        } else {
            $this->info('ไม่พบปัญหาที่ต้องแก้ไขใน Migration');
        }

        return 0;
    }
}
