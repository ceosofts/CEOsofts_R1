<?php

/**
 * ตรวจสอบเวอร์ชัน Laravel ที่แน่นอนและวิธีที่ถูกต้องในการแก้ไขไฟล์
 */

echo "===== Laravel Version Checker =====\n\n";

// ตรวจสอบเวอร์ชัน Laravel
if (file_exists(__DIR__ . '/vendor/laravel/framework/src/Illuminate/Foundation/Application.php')) {
    $appContent = file_get_contents(__DIR__ . '/vendor/laravel/framework/src/Illuminate/Foundation/Application.php');
    
    if (preg_match("/const VERSION = '([^']+)'/", $appContent, $matches)) {
        $version = $matches[1];
        echo "Laravel Version: $version\n\n";
    } else {
        echo "ไม่สามารถระบุเวอร์ชัน Laravel\n\n";
        $version = "unknown";
    }
} else {
    echo "ไม่พบไฟล์ Application.php - อาจมีการติดตั้งแบบไม่สมบูรณ์\n\n";
    $version = "unknown";
}

// ตรวจสอบโครงสร้างของ ApplicationBuilder
echo "ตรวจสอบ ApplicationBuilder class:\n";
$builderPath = __DIR__ . '/vendor/laravel/framework/src/Illuminate/Foundation/Configuration/ApplicationBuilder.php';

if (file_exists($builderPath)) {
    $builderContent = file_get_contents($builderPath);
    
    // ตรวจสอบ methods ที่มีใน ApplicationBuilder
    $methods = [];
    preg_match_all('/public function ([a-zA-Z0-9_]+)\(/', $builderContent, $matches);
    
    if (isset($matches[1]) && count($matches[1]) > 0) {
        $methods = $matches[1];
        echo "พบ methods ใน ApplicationBuilder: " . implode(", ", $methods) . "\n\n";
    } else {
        echo "ไม่พบ method ใด ๆ ใน ApplicationBuilder\n\n";
    }
} else {
    echo "ไม่พบไฟล์ ApplicationBuilder.php\n\n";
    $methods = [];
}

// ตรวจสอบการกลับไปที่ Application object
echo "ตรวจสอบวิธีการกลับไปยัง Application instance:\n";

$convertMethod = null;
if (strpos($builderContent ?? '', 'public function build(') !== false) {
    $convertMethod = 'build';
    echo "พบ method 'build()' สำหรับแปลง ApplicationBuilder เป็น Application\n";
} elseif (strpos($builderContent ?? '', 'public function create(') !== false) {
    $convertMethod = 'create';
    echo "พบ method 'create()' สำหรับแปลง ApplicationBuilder เป็น Application\n";
} else {
    echo "ไม่พบวิธีการแปลง ApplicationBuilder เป็น Application\n";
}

// ตรวจสอบไฟล์ artisan
echo "\nตรวจสอบไฟล์ artisan:\n";
if (file_exists(__DIR__ . '/artisan')) {
    $artisanContent = file_get_contents(__DIR__ . '/artisan');
    echo "พบไฟล์ artisan\n";
    
    // ตรวจสอบรูปแบบการใช้งาน
    if (strpos($artisanContent, 'handleCommand(') !== false) {
        echo "ไฟล์ artisan ใช้ method handleCommand() ซึ่งไม่มีใน ApplicationBuilder\n";
    }
} else {
    echo "ไม่พบไฟล์ artisan\n";
}

// ตรวจสอบไฟล์ public/index.php
echo "\nตรวจสอบไฟล์ public/index.php:\n";
if (file_exists(__DIR__ . '/public/index.php')) {
    $indexContent = file_get_contents(__DIR__ . '/public/index.php');
    echo "พบไฟล์ public/index.php\n";
    
    // ตรวจสอบรูปแบบการใช้งาน
    if (strpos($indexContent, 'handleRequest(') !== false) {
        echo "ไฟล์ public/index.php ใช้ method handleRequest() ซึ่งไม่มีใน ApplicationBuilder\n";
    }
} else {
    echo "ไม่พบไฟล์ public/index.php\n";
}

echo "\n===== คำแนะนำในการแก้ไข =====\n";

if ($convertMethod) {
    echo "1. แก้ไขไฟล์ artisan โดยใช้ method '$convertMethod()' แทน 'handleCommand()'\n";
    echo "2. แก้ไขไฟล์ public/index.php โดยใช้ method '$convertMethod()' แทน 'handleRequest()'\n";
    
    echo "\nกำลังสร้างไฟล์แก้ไข auto-fix-laravel.php...\n";
    
    // สร้างไฟล์แก้ไข auto-fix-laravel.php
    $fixContent = <<<EOF
<?php

/**
 * แก้ไขไฟล์ artisan และ public/index.php อัตโนมัติตามเวอร์ชัน Laravel
 */

echo "===== Auto-fixing Laravel 12 Files =====\\n\\n";

// 1. สำรองไฟล์เดิม
\$backupDir = __DIR__ . '/backups/auto_fix_' . date('YmdHis');
if (!is_dir(\$backupDir)) {
    mkdir(\$backupDir, 0755, true);
    echo "✅ สร้างโฟลเดอร์ {$backupDir}\\n";
}

// สำรองไฟล์ artisan
if (file_exists(__DIR__ . '/artisan')) {
    copy(__DIR__ . '/artisan', \$backupDir . '/artisan');
    echo "✅ สำรองไฟล์ artisan เรียบร้อยแล้ว\\n";
}

// สำรองไฟล์ public/index.php
if (file_exists(__DIR__ . '/public/index.php')) {
    if (!is_dir(\$backupDir . '/public')) {
        mkdir(\$backupDir . '/public', 0755, true);
    }
    copy(__DIR__ . '/public/index.php', \$backupDir . '/public/index.php');
    echo "✅ สำรองไฟล์ public/index.php เรียบร้อยแล้ว\\n";
}

// 2. แก้ไขไฟล์ artisan
echo "\\n1. กำลังแก้ไขไฟล์ artisan...\\n";

\$artisanContent = <<<'ARTISAN'
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

\$app = (require_once __DIR__.'/bootstrap/app.php')->$convertMethod();

\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);

\$status = \$kernel->handle(
    \$input = new Symfony\Component\Console\Input\ArgvInput,
    new Symfony\Component\Console\Output\ConsoleOutput
);

\$kernel->terminate(\$input, \$status);

exit(\$status);
ARTISAN;

file_put_contents(__DIR__ . '/artisan', \$artisanContent);
chmod(__DIR__ . '/artisan', 0755); // Make it executable
echo "✅ แก้ไขไฟล์ artisan เรียบร้อยแล้ว\\n";

// 3. แก้ไขไฟล์ public/index.php
echo "\\n2. กำลังแก้ไขไฟล์ public/index.php...\\n";

\$indexContent = <<<'INDEX'
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

if (file_exists(\$maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require \$maintenance;
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

\$app = (require_once __DIR__.'/../bootstrap/app.php')->$convertMethod();

\$kernel = \$app->make(Illuminate\Contracts\Http\Kernel::class);

\$response = \$kernel->handle(
    \$request = Request::capture()
)->send();

\$kernel->terminate(\$request, \$response);
INDEX;

// ตรวจสอบและสร้างไดเรกทอรี public หากยังไม่มี
if (!is_dir(__DIR__ . '/public')) {
    mkdir(__DIR__ . '/public', 0755, true);
    echo "✅ สร้างโฟลเดอร์ public\\n";
}

file_put_contents(__DIR__ . '/public/index.php', \$indexContent);
echo "✅ แก้ไขไฟล์ public/index.php เรียบร้อยแล้ว\\n";

echo "\\n===== การแก้ไขเสร็จสมบูรณ์! =====\\n";
echo "คุณสามารถรันเซิร์ฟเวอร์ใหม่ด้วยคำสั่ง:\\n";
echo "php artisan serve --port=8030\\n";
echo "หรือดูเวอร์ชัน Laravel:\\n";
echo "php artisan --version\\n";
EOF;
    
    // แทนที่ตัวแปร $convertMethod ในไฟล์
    $fixContent = str_replace('$convertMethod', $convertMethod, $fixContent);
    
    file_put_contents(__DIR__ . '/auto-fix-laravel.php', $fixContent);
    echo "\nสร้างไฟล์ auto-fix-laravel.php เรียบร้อยแล้ว\n";
    echo "รันคำสั่งต่อไปนี้เพื่อแก้ไขไฟล์อัตโนมัติ:\n";
    echo "php auto-fix-laravel.php\n";
} else {
    echo "ไม่สามารถระบุวิธีการแก้ไขได้เนื่องจากไม่พบ method ที่เหมาะสม\n";
    echo "อาจต้องลองติดตั้ง Laravel ใหม่ด้วยคำสั่ง:\n";
    echo "composer create-project --prefer-dist laravel/laravel laravel_new\n";
    echo "จากนั้นคัดลอกไฟล์ที่จำเป็นจาก laravel_new มาใช้กับโปรเจกต์ปัจจุบัน\n";
}
