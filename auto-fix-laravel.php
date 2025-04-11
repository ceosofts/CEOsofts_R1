<?php

/**
 * แก้ไขไฟล์ artisan และ public/index.php อัตโนมัติตามเวอร์ชัน Laravel
 */

echo "===== Auto-fixing Laravel 12 Files =====\n\n";

// 1. สำรองไฟล์เดิม
$backupDir = __DIR__ . '/backups/auto_fix_' . date('YmdHis');
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
    echo "✅ สร้างโฟลเดอร์ \n";
}

// สำรองไฟล์ artisan
if (file_exists(__DIR__ . '/artisan')) {
    copy(__DIR__ . '/artisan', $backupDir . '/artisan');
    echo "✅ สำรองไฟล์ artisan เรียบร้อยแล้ว\n";
}

// สำรองไฟล์ public/index.php
if (file_exists(__DIR__ . '/public/index.php')) {
    if (!is_dir($backupDir . '/public')) {
        mkdir($backupDir . '/public', 0755, true);
    }
    copy(__DIR__ . '/public/index.php', $backupDir . '/public/index.php');
    echo "✅ สำรองไฟล์ public/index.php เรียบร้อยแล้ว\n";
}

// 2. แก้ไขไฟล์ artisan
echo "\n1. กำลังแก้ไขไฟล์ artisan...\n";

$artisanContent = <<<'ARTISAN'
#!/usr/bin/env php
<?php

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| When we run the console application, the current CLI command will be
| executed in this console and the response sent back to a terminal
| or another output device for the developers. Here goes nothing!
|
*/

$app = (require_once __DIR__.'/bootstrap/app.php')->create();

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput,
    new Symfony\Component\Console\Output\ConsoleOutput
);

$kernel->terminate($input, $status);

exit($status);
ARTISAN;

file_put_contents(__DIR__ . '/artisan', $artisanContent);
chmod(__DIR__ . '/artisan', 0755); // Make it executable
echo "✅ แก้ไขไฟล์ artisan เรียบร้อยแล้ว\n";

// 3. แก้ไขไฟล์ public/index.php
echo "\n2. กำลังแก้ไขไฟล์ public/index.php...\n";

$indexContent = <<<'INDEX'
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = (require_once __DIR__.'/../bootstrap/app.php')->create();

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
INDEX;

// ตรวจสอบและสร้างไดเรกทอรี public หากยังไม่มี
if (!is_dir(__DIR__ . '/public')) {
    mkdir(__DIR__ . '/public', 0755, true);
    echo "✅ สร้างโฟลเดอร์ public\n";
}

file_put_contents(__DIR__ . '/public/index.php', $indexContent);
echo "✅ แก้ไขไฟล์ public/index.php เรียบร้อยแล้ว\n";

echo "\n===== การแก้ไขเสร็จสมบูรณ์! =====\n";
echo "คุณสามารถรันเซิร์ฟเวอร์ใหม่ด้วยคำสั่ง:\n";
echo "php artisan serve --port=8030\n";
echo "หรือดูเวอร์ชัน Laravel:\n";
echo "php artisan --version\n";