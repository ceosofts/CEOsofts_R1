<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// แสดงข้อมูลการเชื่อมต่อฐานข้อมูล
echo '<h1>Database Connection Test</h1>';
echo '<pre>';

try {
    // กำหนด path เต็มสำหรับ SQLite
    $sqlitePath = __DIR__ . '/../database/ceosofts_db_R1.sqlite';
    echo "Full SQLite Path: " . $sqlitePath . "\n";
    echo "File exists: " . (file_exists($sqlitePath) ? "Yes" : "No") . "\n\n";

    // บังคับใช้ SQLite connection
    Config::set('database.default', 'sqlite');
    Config::set('database.connections.sqlite.database', $sqlitePath);

    // ตรวจสอบค่า config
    echo "Database Default: " . config('database.default') . "\n";
    echo "Database Path (SQLite): " . config('database.connections.sqlite.database') . "\n\n";

    // ทดสอบการเชื่อมต่อ
    DB::purge('sqlite'); // ล้างการเชื่อมต่อเดิม
    $pdo = DB::connection('sqlite')->getPdo();
    echo "Connection Success: " . DB::connection('sqlite')->getName() . "\n";
    echo "Database Name: " . DB::connection('sqlite')->getDatabaseName() . "\n\n";

    // นับจำนวนบริษัท
    $companies = DB::connection('sqlite')->table('companies')->get();
    echo "Found " . count($companies) . " companies\n\n";

    foreach ($companies as $company) {
        echo "- {$company->name} (ID: {$company->id})\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString();
}

echo '</pre>';
