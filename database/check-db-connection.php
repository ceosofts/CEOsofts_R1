<?php

// สคริปต์สำหรับตรวจสอบการเชื่อมต่อฐานข้อมูล
// รันด้วยคำสั่ง: php database/check-db-connection.php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // ทดสอบการเชื่อมต่อกับฐานข้อมูล
    $pdo = DB::connection()->getPdo();
    echo "เชื่อมต่อฐานข้อมูลสำเร็จ!" . PHP_EOL;
    echo "ชื่อฐานข้อมูล: " . DB::connection()->getDatabaseName() . PHP_EOL;

    // แสดงชื่อตารางทั้งหมดในฐานข้อมูล (ใช้ query โดยตรงแทน Doctrine)
    echo "รายชื่อตารางในฐานข้อมูล:" . PHP_EOL;
    $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
    foreach ($tables as $table) {
        echo "- {$table->name}" . PHP_EOL;
    }

    // ตรวจสอบว่าตาราง companies มีอยู่หรือไม่
    if (Schema::hasTable('companies')) {
        // ทดสอบการอ่านข้อมูลจากตาราง companies
        $companies = DB::table('companies')->get();
        echo "จำนวนบริษัททั้งหมด: " . count($companies) . PHP_EOL;

        // แสดงข้อมูลบริษัท (จำกัดไว้ที่ 5 รายการ)
        if (count($companies) > 0) {
            echo "รายชื่อบริษัท:" . PHP_EOL;
            $counter = 0;
            foreach ($companies as $company) {
                echo "- {$company->name} (ID: {$company->id})" . PHP_EOL;
                $counter++;
                if ($counter >= 5) break;
            }
        } else {
            echo "ไม่พบข้อมูลบริษัทในฐานข้อมูล" . PHP_EOL;
        }
    } else {
        echo "ไม่พบตาราง companies ในฐานข้อมูล" . PHP_EOL;

        // แสดงคำแนะนำ
        echo "คุณอาจต้องรัน migration เพื่อสร้างตาราง:" . PHP_EOL;
        echo "php artisan migrate" . PHP_EOL;
    }
} catch (\Exception $e) {
    echo "เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: " . $e->getMessage() . PHP_EOL;

    // ตรวจสอบว่าไฟล์ฐานข้อมูล SQLite มีอยู่หรือไม่
    $dbPath = database_path('ceosofts_db_R1.sqlite');
    echo "ค้นหาฐานข้อมูล SQLite ที่: " . $dbPath . PHP_EOL;

    if (file_exists($dbPath)) {
        echo "ไฟล์ฐานข้อมูล SQLite มีอยู่ ขนาด: " . filesize($dbPath) . " bytes" . PHP_EOL;
    } else {
        echo "ไม่พบไฟล์ฐานข้อมูล SQLite!" . PHP_EOL;

        // แสดงคำแนะนำในการสร้างฐานข้อมูล
        echo "ลองรันคำสั่งต่อไปนี้เพื่อสร้างฐานข้อมูล:" . PHP_EOL;
        echo "touch " . $dbPath . PHP_EOL;
        echo "php artisan migrate" . PHP_EOL;
    }
}
