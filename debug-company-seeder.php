<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Import DB Facade
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// แสดงค่าตาราง companies ในฐานข้อมูล
echo "ตรวจสอบตาราง companies:\n";
$schema = DB::select("PRAGMA table_info(companies)");
var_dump($schema);

// ทดสอบข้อมูลที่จะใส่เข้าไป
echo "\n\nทดสอบข้อมูลสำหรับ seeder:\n";
$company = [
    'name' => 'บริษัท ซีอีโอซอฟต์ จำกัด',
    'code' => 'CEOSOFT',
    'address' => '55/99 ถนนรัชดาภิเษก แขวงดินแดง เขตดินแดง กรุงเทพมหานคร 10400',
    'phone' => '02-123-4567',
    'email' => 'info@ceosofts.com',
    'tax_id' => '0105564123456',
    'website' => 'https://www.ceosofts.com',
    'logo' => null, // ทดสอบค่า null
    'is_active' => true,
    'status' => 'active',
    'settings' => json_encode([
        'invoice_prefix' => 'INV-CEOSOFT',
        'receipt_prefix' => 'REC-CEOSOFT',
    ]),
    'metadata' => json_encode([
        'founded_year' => 2015,
        'industry' => 'Software Development',
    ]),
    'uuid' => (string) Str::uuid(),
    'ulid' => (string) Str::ulid(), // เพิ่มค่า ulid ที่จำเป็น
];

var_dump($company);

// ทดสอบค่าที่ถูกส่งเข้า query
echo "\n\nทดสอบ SQL query:\n";
try {
    // ทดลองใส่ข้อมูล
    $id = DB::table('companies')->insertGetId($company);
    echo "สำเร็จ! ใส่ข้อมูล ID: $id\n";
} catch (Exception $e) {
    echo "เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
}
