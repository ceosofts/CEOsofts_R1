<?php

// เพิ่ม import DB Facade
use Illuminate\Support\Facades\DB;

// Force SQLite connection
$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['DB_DATABASE'] = __DIR__ . '/database/ceosofts_db_R1.sqlite';

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Environment: " . app()->environment() . "\n";
echo "Database Connection: " . config('database.default') . "\n";
echo "Database Path: " . config('database.connections.sqlite.database') . "\n";

try {
    $pdo = DB::connection()->getPdo();
    echo "Connected successfully to: " . DB::connection()->getDatabaseName() . "\n\n";

    // Count companies
    $companies = DB::table('companies')->get();
    echo "Found " . count($companies) . " companies\n";

    foreach ($companies as $company) {
        echo "- {$company->name} (ID: {$company->id})\n";
    }
} catch (\Exception $e) {
    echo "Connection failed: " . $e->getMessage() . "\n";
}
