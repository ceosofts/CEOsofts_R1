<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create {name=ceosofts_db_R1 : ชื่อฐานข้อมูลที่ต้องการสร้าง}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'สร้างฐานข้อมูล MySQL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dbName = $this->argument('name');
        $this->info("กำลังสร้างฐานข้อมูล {$dbName}...");

        try {
            $charset = config('database.connections.mysql.charset', 'utf8mb4');
            $collation = config('database.connections.mysql.collation', 'utf8mb4_unicode_ci');

            // เชื่อมต่อกับ MySQL โดยไม่ระบุชื่อฐานข้อมูล
            $connection = config('database.default');
            $currentDb = config("database.connections.{$connection}.database");
            
            config(["database.connections.{$connection}.database" => null]);
            DB::purge($connection);
            
            // สร้างฐานข้อมูล
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET {$charset} COLLATE {$collation}");
            
            // ตั้งค่ากลับคืน
            config(["database.connections.{$connection}.database" => $currentDb]);
            DB::purge($connection);
            
            $this->info("สร้างฐานข้อมูล {$dbName} สำเร็จแล้ว");
            
            // อัปเดตไฟล์ .env
            if ($this->confirm('ต้องการอัปเดต DB_DATABASE ในไฟล์ .env หรือไม่?')) {
                $this->updateEnvFile('DB_DATABASE', $dbName);
                $this->info('อัปเดตไฟล์ .env เรียบร้อยแล้ว');
            }
            
        } catch (\Exception $e) {
            $this->error("เกิดข้อผิดพลาด: " . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * อัปเดตไฟล์ .env
     */
    protected function updateEnvFile($key, $value)
    {
        $path = base_path('.env');
        
        if (file_exists($path)) {
            $content = file_get_contents($path);
            
            // ถ้าพบค่านี้แล้ว ทำการแทนที่
            if (str_contains($content, $key.'=')) {
                $content = preg_replace(
                    '/^'.$key.'=.*/m',
                    $key.'='.$value,
                    $content
                );
            } else {
                // ถ้ายังไม่มี เพิ่มค่าใหม่
                $content .= "\n".$key.'='.$value."\n";
            }
            
            file_put_contents($path, $content);
        }
    }
}
