<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixDuplicateImportsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrations:fix-imports {--path= : ระบุพาธเฉพาะที่จะแก้ไข}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'แก้ไขการ import ซ้ำซ้อนในไฟล์ migration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->option('path') ?? database_path('migrations');

        $this->info('กำลังแก้ไขการ import ซ้ำซ้อนในไฟล์...');

        // ค้นหาไฟล์ทั้งหมด
        $files = is_dir($path) ? File::glob("{$path}/*.php") : [$path];
        $count = 0;

        foreach ($files as $file) {
            if (!File::exists($file) || !File::isFile($file)) {
                continue;
            }

            $content = File::get($file);
            $hasChanged = false;

            // วิเคราะห์และแก้ไข imports ซ้ำซ้อน
            $newContent = $this->fixDuplicateImports($content, $hasChanged);

            if ($hasChanged) {
                File::put($file, $newContent);
                $this->line("✓ แก้ไขไฟล์ " . basename($file));
                $count++;
            }
        }

        if ($count > 0) {
            $this->info("แก้ไข {$count} ไฟล์เรียบร้อยแล้ว");
        } else {
            $this->info("ไม่พบไฟล์ที่ต้องแก้ไข");
        }

        return 0;
    }

    /**
     * แก้ไข imports ที่ซ้ำซ้อน
     * 
     * @param string $content เนื้อหาไฟล์
     * @param bool &$hasChanged ตัวแปรอ้างอิงที่จะกำหนดว่ามีการเปลี่ยนแปลงหรือไม่
     * @return string เนื้อหาที่แก้ไขแล้ว
     */
    private function fixDuplicateImports(string $content, bool &$hasChanged): string
    {
        // ดึง imports ทั้งหมด
        preg_match_all('/use\s+([^;]+);/', $content, $matches, PREG_OFFSET_CAPTURE);

        if (empty($matches[0])) {
            return $content;
        }

        // เก็บ imports ที่พบและตำแหน่ง
        $imports = [];
        $seenImports = [];
        $duplicates = [];

        foreach ($matches[0] as $index => $match) {
            $import = $match[0];
            $position = $match[1];
            $fullNamespace = trim($matches[1][$index][0]);

            if (in_array($fullNamespace, $seenImports)) {
                // พบ import ซ้ำ
                $duplicates[] = [
                    'import' => $import,
                    'position' => $position,
                    'length' => strlen($import)
                ];
                $hasChanged = true;
            } else {
                // เก็บ import ที่ไม่ซ้ำ
                $seenImports[] = $fullNamespace;
                $imports[] = [
                    'import' => $import,
                    'position' => $position,
                    'length' => strlen($import)
                ];
            }
        }

        // ไม่มี imports ซ้ำ
        if (empty($duplicates)) {
            return $content;
        }

        // เรียงลำดับตำแหน่งจากมากไปน้อย (เพื่อลบจากท้ายไฟล์ก่อน)
        usort($duplicates, function ($a, $b) {
            return $b['position'] - $a['position'];
        });

        // ลบ imports ที่ซ้ำ
        foreach ($duplicates as $duplicate) {
            $start = $duplicate['position'];
            $length = $duplicate['length'];

            // ลบบรรทัดทั้งหมดรวม newline
            if (substr($content, $start + $length, 1) === "\n") {
                $length += 1;
            }

            $content = substr_replace($content, '', $start, $length);
        }

        return $content;
    }
}
