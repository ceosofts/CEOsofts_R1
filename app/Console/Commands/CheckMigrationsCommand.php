<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use function base_path;
use function database_path;
use function app_path;

/**
 * Class CheckMigrationsCommand
 * @package App\Console\Commands
 */
class CheckMigrationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrations:check {--fix : แก้ไขปัญหาอัตโนมัติ}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ตรวจสอบปัญหาทั่วไปในไฟล์ migrations';

    /**
     * Execute the console command.
     * @return int
     */
    public function handle(): int
    {
        $this->info("กำลังตรวจสอบไฟล์ migrations...");

        $migrationPaths = glob(database_path("migrations/*.php"));
        $problemsFound = false;
        $problems = [];

        foreach ($migrationPaths as $migrationPath) {
            $content = file_get_contents($migrationPath);
            $filename = basename($migrationPath);

            // ตรวจสอบการใช้ classes และ imports
            $this->checkImports($migrationPath, $content, $problems, $problemsFound);

            // ตรวจสอบ imports ซ้ำซ้อน
            $this->checkDuplicateImports($migrationPath, $content, $problems, $problemsFound);
        }

        if ($problemsFound) {
            $this->warn("พบปัญหาในไฟล์ migration:");

            foreach ($problems as $path => $fileProblems) {
                $filename = basename($path);
                $this->line("- $filename:");

                foreach ($fileProblems as $problem) {
                    $this->line("  • $problem");
                }

                // แก้ไขปัญหาอัตโนมัติหากเลือก option --fix
                if ($this->option('fix')) {
                    $this->fixMigration($path, $fileProblems);
                    $this->info("  ✓ แก้ไขปัญหาแล้ว");
                }
            }

            if (!$this->option('fix')) {
                $this->line("\nรันคำสั่งด้วย --fix เพื่อแก้ไขปัญหาโดยอัตโนมัติ");
            }

            return 1;
        } else {
            $this->info("ตรวจสอบเสร็จสิ้น ไม่พบปัญหา");
            return 0;
        }
    }

    /**
     * ตรวจสอบการใช้ classes แต่ไม่มี imports
     * 
     * @param string $path
     * @param string $content
     * @param array &$problems
     * @param bool &$problemsFound
     */
    private function checkImports(string $path, string $content, array &$problems, bool &$problemsFound): void
    {
        $classesToCheck = [
            'DB' => 'use Illuminate\Support\Facades\DB;',
            'Log' => 'use Illuminate\Support\Facades\Log;',
            'Validator' => 'use Illuminate\Support\Facades\Validator;',
            'Route' => 'use Illuminate\Support\Facades\Route;',
            'Artisan' => 'use Illuminate\Support\Facades\Artisan;',
            'Hash' => 'use Illuminate\Support\Facades\Hash;',
            'Auth' => 'use Illuminate\Support\Facades\Auth;',
        ];

        foreach ($classesToCheck as $class => $import) {
            // ตรวจสอบว่ามีการใช้ class แต่ไม่มีการ import
            if (
                (preg_match('/\\\\' . $class . '::|' . $class . '::|\$this->' . strtolower($class) . '->|->table\(/', $content) ||
                    (strtolower($class) === 'db' && preg_match('/->table\(/', $content))) &&
                !Str::contains($content, $import)
            ) {
                $problems[$path][] = "ใช้ {$class} แต่ไม่มีการ import: {$import}";
                $problemsFound = true;
            }
        }
    }

    /**
     * ตรวจสอบ imports ที่ซ้ำซ้อน
     * 
     * @param string $path
     * @param string $content
     * @param array &$problems
     * @param bool &$problemsFound
     */
    private function checkDuplicateImports(string $path, string $content, array &$problems, bool &$problemsFound): void
    {
        // ดึงเอา imports ทั้งหมด
        preg_match_all('/use\s+([^;]+);/', $content, $matches);

        if (!empty($matches[1])) {
            $imports = $matches[1];
            $duplicates = array_unique(array_diff_assoc($imports, array_unique($imports)));

            foreach ($duplicates as $duplicate) {
                $problems[$path][] = "มีการ import '{$duplicate}' ซ้ำซ้อน";
                $problemsFound = true;
            }
        }
    }

    /**
     * แก้ไขไฟล์ migration ที่มีปัญหา
     * @param string $path
     * @param array $problems
     * @return void
     */
    private function fixMigration(string $path, array $problems): void
    {
        $content = file_get_contents($path);
        $importsToAdd = [];

        // รวบรวม imports ที่ต้องเพิ่ม
        foreach ($problems as $problem) {
            if (preg_match('/ใช้ .+ แต่ไม่มีการ import: (use .+;)/', $problem, $matches)) {
                $importsToAdd[] = $matches[1];
            }
        }

        // ลบ imports ที่ซ้ำซ้อน
        if (str_contains($content, 'use Illuminate')) {
            // หา imports ที่มีอยู่แล้ว
            preg_match_all('/use\s+([^;]+);/', $content, $matches, PREG_SET_ORDER);

            $existingImports = [];
            foreach ($matches as $match) {
                $import = $match[0];

                // เก็บ import แรก ลบ imports ซ้ำ
                if (!in_array($import, $existingImports)) {
                    $existingImports[] = $import;
                } else {
                    $content = str_replace($import . "\n", '', $content);
                    $content = str_replace($import, '', $content);
                }
            }
        }

        // เพิ่ม imports ที่จำเป็น
        if (!empty($importsToAdd)) {
            $uniqueImports = array_unique($importsToAdd);
            $useStatements = '';

            foreach ($uniqueImports as $import) {
                // ตรวจสอบว่ามี import นี้อยู่แล้วหรือไม่
                if (!str_contains($content, $import)) {
                    $useStatements .= $import . "\n";
                }
            }

            if (!empty($useStatements)) {
                // เพิ่ม use statements หลัง use ตัวสุดท้ายที่มีอยู่
                if (preg_match('/use [^;]+;(?:\s*use [^;]+;)*/', $content, $matches, PREG_OFFSET_CAPTURE)) {
                    $lastUseEndPos = $matches[0][1] + strlen($matches[0][0]);
                    $content = substr($content, 0, $lastUseEndPos) . "\n" . $useStatements . substr($content, $lastUseEndPos);
                } else {
                    // หรือเพิ่มหลัง <?php
                    $content = preg_replace('/(<\?php)/', '$1' . "\n\n" . $useStatements, $content);
                }
            }
        }

        file_put_contents($path, $content);
    }
}
