<?php

// สคริปต์สำหรับตรวจสอบข้อมูลบริษัทโดยเฉพาะ
// รันด้วยคำสั่ง: php database/check-companies.php

use App\Models\Company;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // นับจำนวนบริษัททั้งหมด
    $count = Company::count();
    echo "จำนวนบริษัททั้งหมดในระบบ (จาก Model): " . $count . PHP_EOL;

    // ดึงข้อมูลจาก DB โดยตรง
    $companies = DB::table('companies')->get();
    echo "จำนวนบริษัททั้งหมดในระบบ (จาก DB): " . $companies->count() . PHP_EOL;

    // แสดงโครงสร้างของตาราง
    echo "\nโครงสร้างตาราง companies:" . PHP_EOL;
    $columns = DB::select("PRAGMA table_info(companies)");
    foreach ($columns as $column) {
        echo "- {$column->name} ({$column->type})" . PHP_EOL;
    }

    // แสดงข้อมูลบริษัท
    echo "\nข้อมูลบริษัทในระบบ:" . PHP_EOL;
    echo str_repeat('-', 80) . PHP_EOL;
    echo sprintf("%-5s | %-30s | %-15s | %-20s", "ID", "ชื่อบริษัท", "รหัส", "สถานะ") . PHP_EOL;
    echo str_repeat('-', 80) . PHP_EOL;

    foreach ($companies as $company) {
        $status = isset($company->is_active) && $company->is_active ? "ใช้งาน" : "ไม่ใช้งาน";
        echo sprintf(
            "%-5s | %-30s | %-15s | %-20s",
            $company->id,
            $company->name,
            $company->code ?? 'ไม่ระบุ',
            $status
        ) . PHP_EOL;
    }

    echo str_repeat('-', 80) . PHP_EOL;
} catch (\Exception $e) {
    echo "เกิดข้อผิดพลาด: " . $e->getMessage() . PHP_EOL;
    echo "ที่: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
}
