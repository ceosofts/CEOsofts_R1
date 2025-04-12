<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OptimizeDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:optimize {--clean-old-data : ลบข้อมูลเก่าที่ไม่จำเป็น}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ปรับปรุงประสิทธิภาพฐานข้อมูล เช่น การเพิ่มดัชนีและลบข้อมูลเก่า';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('เริ่มการปรับปรุงฐานข้อมูล...');

        // เพิ่มดัชนี (index) ให้กับคอลัมน์ที่สำคัญ
        $this->addIndexes();

        // ลบข้อมูลเก่าที่ไม่จำเป็น (ถ้าระบุ option)
        if ($this->option('clean-old-data')) {
            $this->cleanOldData();
        }

        $this->info('การปรับปรุงฐานข้อมูลเสร็จสมบูรณ์!');
        return Command::SUCCESS;
    }

    /**
     * เพิ่มดัชนี (index) ให้กับคอลัมน์ที่สำคัญ
     */
    protected function addIndexes()
    {
        $this->info('กำลังเพิ่มดัชนี (index)...');

        $tablesAndColumns = [
            'users' => ['email', 'created_at'],
            'orders' => ['customer_id', 'created_at'],
            'products' => ['slug', 'category_id'],
        ];

        foreach ($tablesAndColumns as $table => $columns) {
            if (!Schema::hasTable($table)) {
                $this->warn("ตาราง {$table} ไม่พบในฐานข้อมูล");
                continue;
            }

            foreach ($columns as $column) {
                if (Schema::hasColumn($table, $column)) {
                    $indexName = "{$table}_{$column}_index";
                    if (!$this->indexExists($table, $indexName)) {
                        Schema::table($table, function ($table) use ($column) {
                            $table->index($column);
                        });
                        $this->info("เพิ่มดัชนีให้กับคอลัมน์ {$column} ในตาราง {$table}");
                    } else {
                        $this->line("ดัชนีสำหรับคอลัมน์ {$column} ในตาราง {$table} มีอยู่แล้ว");
                    }
                } else {
                    $this->warn("คอลัมน์ {$column} ไม่พบในตาราง {$table}");
                }
            }
        }
    }

    /**
     * ลบข้อมูลเก่าที่ไม่จำเป็น
     */
    protected function cleanOldData()
    {
        $this->info('กำลังลบข้อมูลเก่าที่ไม่จำเป็น...');

        $tablesAndConditions = [
            'logs' => ['created_at', now()->subMonths(6)],
            'sessions' => ['last_activity', now()->subDays(30)->timestamp],
        ];

        foreach ($tablesAndConditions as $table => $condition) {
            if (!Schema::hasTable($table)) {
                $this->warn("ตาราง {$table} ไม่พบในฐานข้อมูล");
                continue;
            }

            [$column, $threshold] = $condition;
            $deleted = DB::table($table)->where($column, '<', $threshold)->delete();
            $this->info("ลบข้อมูล {$deleted} แถวจากตาราง {$table}");
        }
    }

    /**
     * ตรวจสอบว่าดัชนี (index) มีอยู่แล้วหรือไม่
     */
    protected function indexExists($table, $indexName)
    {
        $connection = config('database.default');
        if ($connection === 'sqlite') {
            $query = "PRAGMA index_list('{$table}')";
            $indexes = DB::select($query);
            foreach ($indexes as $index) {
                if ($index->name === $indexName) {
                    return true;
                }
            }
        } else {
            $query = "SHOW INDEX FROM {$table} WHERE Key_name = '{$indexName}'";
            return DB::select($query) ? true : false;
        }

        return false;
    }
}
