<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckDatabaseConnection extends Command
{
    protected $signature = 'db:check';
    protected $description = 'ตรวจสอบการเชื่อมต่อฐานข้อมูลและโครงสร้างตาราง orders';

    public function handle()
    {
        try {
            $this->info("กำลังตรวจสอบการเชื่อมต่อฐานข้อมูล...");
            DB::connection()->getPdo();
            $this->info("เชื่อมต่อฐานข้อมูลสำเร็จ: " . DB::connection()->getDatabaseName());
            
            $this->info("\nกำลังตรวจสอบโครงสร้างตาราง orders...");
            $columns = DB::select('PRAGMA table_info(orders)');
            
            $columnsTable = [];
            foreach ($columns as $column) {
                $columnsTable[] = [
                    'cid' => $column->cid,
                    'name' => $column->name,
                    'type' => $column->type,
                    'notnull' => $column->notnull,
                    'dflt_value' => $column->dflt_value,
                    'pk' => $column->pk,
                ];
            }
            
            $this->table(['ID', 'คอลัมน์', 'ชนิดข้อมูล', 'Not Null', 'ค่าเริ่มต้น', 'Primary Key'], $columnsTable);
            
            $this->info("\nกำลังตรวจสอบข้อมูล Model Order...");
            $order = new \App\Models\Order();
            $this->info("Fillable fields: " . implode(', ', $order->getFillable()));
            
            $this->info("\nกำลังตรวจสอบอินเด็กซ์ของตาราง orders...");
            $indexes = DB::select("SELECT name, sql FROM sqlite_master WHERE type='index' AND tbl_name='orders'");
            $this->table(['Name', 'SQL'], array_map(fn($idx) => [$idx->name, $idx->sql], $indexes));
            
            return 0;
        } catch (\Exception $e) {
            $this->error("เกิดข้อผิดพลาด: {$e->getMessage()}");
            Log::error("Database check error", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
