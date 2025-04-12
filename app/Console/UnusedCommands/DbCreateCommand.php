<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDO;

class DbCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create {database? : ชื่อฐานข้อมูล}';

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
        try {
            $database = $this->argument('database');
            
            if (!$database) {
                $database = config('database.connections.' . config('database.default') . '.database');
                
                if (!$database) {
                    $database = $this->ask('ชื่อฐานข้อมูลที่ต้องการสร้าง');
                }
            }
            
            $charset = config('database.connections.mysql.charset', 'utf8mb4');
            $collation = config('database.connections.mysql.collation', 'utf8mb4_unicode_ci');
            
            // ถ้าเป็น SQLite ให้สร้างไฟล์เปล่า
            if (config('database.default') === 'sqlite') {
                $path = database_path($database);
                
                if (file_exists($path)) {
                    $this->error("ไฟล์ SQLite {$path} มีอยู่แล้ว");
                    return Command::FAILURE;
                }
                
                if (touch($path)) {
                    $this->info("สร้างไฟล์ SQLite {$path} เรียบร้อยแล้ว");
                    return Command::SUCCESS;
                } else {
                    $this->error("ไม่สามารถสร้างไฟล์ SQLite {$path}");
                    return Command::FAILURE;
                }
            }
            
            // กรณีเป็น MySQL
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            
            $this->info("กำลังเชื่อมต่อกับเซิร์ฟเวอร์ MySQL: {$host}:{$port}");
            
            // สร้างการเชื่อมต่อโดยตรงกับ MySQL (ไม่ระบุ database)
            $pdo = new PDO("mysql:host={$host};port={$port}", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // ตรวจสอบว่าฐานข้อมูลมีอยู่แล้วหรือไม่
            $statement = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$database}'");
            if ($statement->rowCount() > 0) {
                $this->info("ฐานข้อมูล '{$database}' มีอยู่แล้ว");
                return Command::SUCCESS;
            }
            
            // สร้างฐานข้อมูล
            $statement = $pdo->prepare("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET {$charset} COLLATE {$collation}");
            if ($statement->execute()) {
                $this->info("สร้างฐานข้อมูล '{$database}' เรียบร้อยแล้ว");
                return Command::SUCCESS;
            } else {
                $this->error("ไม่สามารถสร้างฐานข้อมูล '{$database}'");
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error("เกิดข้อผิดพลาด: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
