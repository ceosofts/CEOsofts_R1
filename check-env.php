<?php

// เพิ่ม import DB Facade
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// แสดงข้อมูล Environment ทั้งหมด
echo "========== ENVIRONMENT VARIABLES ==========\n";
foreach ($_ENV as $key => $value) {
    if (strpos($key, 'DB_') === 0 || $key === 'APP_ENV' || $key === 'APP_DEBUG') {
        echo "$key: $value\n";
    }
}

echo "\n========== DATABASE CONFIG ==========\n";
echo "Default Connection: " . config('database.default') . "\n";
echo "SQLite Path: " . config('database.connections.sqlite.database') . "\n";
echo "MySQL Database: " . config('database.connections.mysql.database') . "\n";

echo "\n========== FILE SYSTEM ==========\n";
$sqlitePath = database_path('ceosofts_db_R1.sqlite');
echo "SQLite file exists: " . (file_exists($sqlitePath) ? "Yes" : "No") . "\n";
if (file_exists($sqlitePath)) {
    echo "SQLite file size: " . filesize($sqlitePath) . " bytes\n";
    echo "SQLite file permissions: " . substr(sprintf('%o', fileperms($sqlitePath)), -4) . "\n";
}

echo "\n========== TESTING CONNECTION ==========\n";
try {
    $pdo = DB::connection()->getPdo();
    echo "Connected successfully using: " . DB::connection()->getName() . "\n";
    echo "Database name: " . DB::connection()->getDatabaseName() . "\n";
} catch (\Exception $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
