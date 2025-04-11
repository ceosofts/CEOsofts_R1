<?php

/**
 * สร้างไฟล์ bootstrap/app.php แบบขั้นต่ำสุดสำหรับ Laravel 12
 * เพื่อให้แอปพลิเคชันสามารถทำงานได้ก่อนแล้วค่อยแก้ไขปัญหาอื่นๆ
 */

echo "===== Create Minimal Bootstrap/App.php for Laravel 12 =====\n\n";

$bootstrapDir = __DIR__ . '/bootstrap';
$bootstrapAppPath = $bootstrapDir . '/app.php';

// สำรองไฟล์เก่า (ถ้ามี)
if (file_exists($bootstrapAppPath)) {
    $backupDir = __DIR__ . '/backups';
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }
    
    $timestamp = date('YmdHis');
    $backupPath = "{$backupDir}/app.php.{$timestamp}.bak";
    copy($bootstrapAppPath, $backupPath);
    echo "✅ สำรองไฟล์เดิมไว้ที่ {$backupPath}\n";
}

// สร้างโฟลเดอร์ bootstrap ถ้ายังไม่มี
if (!is_dir($bootstrapDir)) {
    mkdir($bootstrapDir, 0755, true);
    echo "✅ สร้างโฟลเดอร์ bootstrap/\n";
}

// สร้างไฟล์ app.php แบบขั้นต่ำสุด
$appContent = <<<'EOT'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    });
EOT;

file_put_contents($bootstrapAppPath, $appContent);
echo "✅ สร้างไฟล์ bootstrap/app.php เรียบร้อยแล้ว\n";

// สร้างโฟลเดอร์และไฟล์ routes ที่จำเป็น
$routesDir = __DIR__ . '/routes';
if (!is_dir($routesDir)) {
    mkdir($routesDir, 0755, true);
    echo "✅ สร้างโฟลเดอร์ routes/\n";
}

// สร้างไฟล์ web.php
$webRoutesPath = $routesDir . '/web.php';
if (!file_exists($webRoutesPath)) {
    $webRoutesContent = <<<'EOT'
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'Laravel app is working! APP_KEY: ' . config('app.key');
});
EOT;

    file_put_contents($webRoutesPath, $webRoutesContent);
    echo "✅ สร้างไฟล์ routes/web.php เรียบร้อยแล้ว\n";
}

// สร้างไฟล์ console.php
$consoleRoutesPath = $routesDir . '/console.php';
if (!file_exists($consoleRoutesPath)) {
    $consoleRoutesContent = <<<'EOT'
<?php

use Illuminate\Support\Facades\Artisan;

// Console routes here
EOT;

    file_put_contents($consoleRoutesPath, $consoleRoutesContent);
    echo "✅ สร้างไฟล์ routes/console.php เรียบร้อยแล้ว\n";
}

// พยายามเคลียร์ cache
echo "\nกำลังเคลียร์ cache...\n";
try {
    exec('php artisan config:clear', $output, $returnVar);
    if ($returnVar === 0) {
        echo "✅ เคลียร์ config cache สำเร็จ\n";
    }
} catch (Exception $e) {
    // ไม่ต้องทำอะไร
}

echo "\n===== คำแนะนำ =====\n";
echo "1. รันเซิร์ฟเวอร์เพื่อทดสอบ:\n";
echo "   php artisan serve --port=8010\n\n";
echo "2. ตรวจสอบว่าสามารถเข้าถึงหน้าเว็บได้หรือไม่\n\n";
echo "3. เมื่อระบบทำงานได้แล้ว ค่อยเพิ่ม SimpleEncrypter:\n";
echo "   php create-simple-encrypter-l12.php\n";
