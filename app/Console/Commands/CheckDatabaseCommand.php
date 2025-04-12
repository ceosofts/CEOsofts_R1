<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'แสดงข้อมูลฐานข้อมูลที่ใช้งานอยู่';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connection = config('database.default');
        $database = DB::connection()->getDatabaseName();
        $driver = DB::connection()->getDriverName();

        $this->info("===== ข้อมูลฐานข้อมูล =====");
        $this->info("Connection: {$connection}");
        $this->info("Driver: {$driver}");
        $this->info("Database: {$database}");

        // แสดงพาธไฟล์ถ้าเป็น SQLite
        if ($driver === 'sqlite') {
            try {
                $path = DB::connection()->getPdo()->query('pragma database_list')->fetchAll()[0]['file'];
                $this->info("Path: {$path}");
            } catch (\Exception $e) {
                $this->warn("ไม่สามารถดึงพาธของไฟล์ SQLite: " . $e->getMessage());
            }
        }

        // แสดงเพิ่มเติมถ้าเป็น MySQL
        if ($driver === 'mysql') {
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');
            $username = config('database.connections.mysql.username');
            $this->info("Host: {$host}:{$port}");
            $this->info("Username: {$username}");
        }

        // แสดงตารางทั้งหมด - เปลี่ยนการดึงตามชนิดของฐานข้อมูล
        $tables = [];

        try {
            if ($driver === 'sqlite') {
                // สำหรับ SQLite ใช้วิธีนี้แทน SHOW TABLES
                $query = "SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'";
                $result = DB::select($query);
                $tables = collect($result)->pluck('name')->toArray();
            } else if ($driver === 'mysql') {
                // สำหรับ MySQL ใช้ SHOW TABLES
                $tablesRaw = DB::select('SHOW TABLES');
                $tables = collect($tablesRaw)->map(function ($val) {
                    foreach ($val as $key => $tableName) {
                        return $tableName;
                    }
                })->toArray();
            } else if ($driver === 'pgsql') {
                // สำหรับ PostgreSQL
                $result = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema='public'");
                $tables = collect($result)->pluck('table_name')->toArray();
            }

            $this->info("\nตารางทั้งหมด: " . count($tables));
            foreach ($tables as $index => $table) {
                $this->line(" - {$table}");
            }

            // ตรวจสอบว่ามีตาราง companies หรือไม่
            if (Schema::hasTable('companies')) {
                $this->info("\n🔍 ตรวจสอบตาราง companies");
                $count = DB::table('companies')->count();
                $this->info("จำนวนข้อมูลในตาราง companies: {$count} รายการ");

                if ($count > 0) {
                    // แสดงข้อมูลตัวอย่าง
                    $this->info("\nข้อมูลตัวอย่าง:");
                    $companies = DB::table('companies')->limit(3)->get();
                    foreach ($companies as $company) {
                        $this->line("ID: {$company->id}, Name: " . ($company->name ?? 'N/A'));
                    }
                }
            } else {
                $this->warn("ไม่พบตาราง companies ในฐานข้อมูล");
            }
        } catch (\Exception $e) {
            $this->error("ไม่สามารถดึงข้อมูลตาราง: " . $e->getMessage());
        }

        return 0;
    }
}
