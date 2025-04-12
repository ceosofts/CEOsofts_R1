<?php
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// ตรวจสอบโครงสร้างตาราง companies
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('companies');
echo "Columns in companies table:\n";
echo implode(", ", $columns) . "\n\n";

// ตรวจสอบมีไฟล์ migration ที่ซ้ำซ้อน
$migrations = glob(__DIR__ . '/database/migrations/*_create_companies_table.php');
echo "Company migration files:\n";
foreach ($migrations as $migration) {
    echo basename($migration) . "\n";
}

// แสดงไฟล์ Model Company
echo "\nModel Company properties:\n";
$model = new ReflectionClass(\App\Models\Company::class);
$properties = $model->getDefaultProperties();
echo "Fillable: " . json_encode($properties['fillable']) . "\n";
