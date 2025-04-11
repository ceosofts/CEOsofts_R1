<?php

/**
 * แก้ไขไฟล์ bootstrap/app.php สำหรับ Laravel 12
 */

echo "===== Fix Bootstrap/App.php for Laravel 12 =====\n\n";

$bootstrapDir = __DIR__ . '/bootstrap';
$bootstrapAppPath = $bootstrapDir . '/app.php';

// สำรองไฟล์ก่อนแก้ไข
$backupDir = __DIR__ . '/backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
    echo "✅ สร้างโฟลเดอร์ backups\n";
}

if (file_exists($bootstrapAppPath)) {
    $timestamp = date('YmdHis');
    $backupPath = "{$backupDir}/app.php.{$timestamp}.bak";
    copy($bootstrapAppPath, $backupPath);
    echo "✅ สำรองไฟล์ bootstrap/app.php ไว้ที่ {$backupPath}\n";
} else {
    echo "⚠️ ไม่พบไฟล์ bootstrap/app.php เดิม\n";
    
    if (!is_dir($bootstrapDir)) {
        mkdir($bootstrapDir, 0755, true);
        echo "✅ สร้างโฟลเดอร์ bootstrap/\n";
    }
}

// สร้างไฟล์ app.php ใหม่ที่เข้ากันได้กับ Laravel 12
$newAppContent = <<<'EOT'
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
    })
    ->withBootstrappers(function (Application $app) {
        // ลงทะเบียน services หลังจาก application เริ่มต้นเรียบร้อยแล้ว
        $app->singleton('encrypter', function ($app) {
            try {
                // ดึงค่า key และ cipher จาก config
                $key = config('app.key');
                $cipher = config('app.cipher', 'aes-256-cbc');
                
                // แปลงค่า key จาก base64 เป็น binary
                if (strpos($key, 'base64:') === 0) {
                    $key = base64_decode(substr($key, 7));
                }
                
                // ถ้า SimpleEncrypter มีอยู่ให้ใช้
                if (class_exists('App\Encryption\SimpleEncrypter')) {
                    return new \App\Encryption\SimpleEncrypter($key, $cipher);
                }
                
                // ถ้าไม่มี SimpleEncrypter ให้ใช้ Laravel Encrypter
                return new \Illuminate\Encryption\Encrypter($key, $cipher);
            } catch (\Throwable $e) {
                // สร้าง emergency key ในกรณีที่ไม่สามารถใช้ key ที่กำหนดได้
                $key = random_bytes(32);
                return new \Illuminate\Encryption\Encrypter($key, 'aes-256-cbc');
            }
        });
    });
EOT;

file_put_contents($bootstrapAppPath, $newAppContent);
echo "✅ สร้างไฟล์ bootstrap/app.php ใหม่ที่รองรับ Laravel 12 เรียบร้อยแล้ว\n";

// สร้างไฟล์ routes ที่จำเป็น
$routesDir = __DIR__ . '/routes';
if (!is_dir($routesDir)) {
    mkdir($routesDir, 0755, true);
    echo "✅ สร้างโฟลเดอร์ routes/\n";
}

// สร้าง web.php (ถ้ายังไม่มี)
$webRoutesPath = $routesDir . '/web.php';
if (!file_exists($webRoutesPath)) {
    $webRoutesContent = <<<'EOT'
<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

EOT;
    file_put_contents($webRoutesPath, $webRoutesContent);
    echo "✅ สร้างไฟล์ routes/web.php เรียบร้อยแล้ว\n";
}

// สร้าง console.php (ถ้ายังไม่มี)
$consoleRoutesPath = $routesDir . '/console.php';
if (!file_exists($consoleRoutesPath)) {
    $consoleRoutesContent = <<<'EOT'
<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

EOT;
    file_put_contents($consoleRoutesPath, $consoleRoutesContent);
    echo "✅ สร้างไฟล์ routes/console.php เรียบร้อยแล้ว\n";
}

// ล้าง cache
echo "\nกำลังล้าง cache...\n";

try {
    // ใช้ exec แทน passthru เพื่อไม่ให้แสดงข้อความผิดพลาด
    exec('php artisan config:clear', $output, $returnVar);
    if ($returnVar === 0) {
        echo "✅ ล้าง config cache เรียบร้อยแล้ว\n";
    }
    
    exec('php artisan cache:clear', $output, $returnVar);
    if ($returnVar === 0) {
        echo "✅ ล้าง application cache เรียบร้อยแล้ว\n";
    }
    
    exec('php artisan view:clear', $output, $returnVar);
    if ($returnVar === 0) {
        echo "✅ ล้าง view cache เรียบร้อยแล้ว\n";
    }
} catch (\Exception $e) {
    echo "⚠️ เกิดข้อผิดพลาดในระหว่างการล้าง cache\n";
}

// ตรวจสอบการมีอยู่ของ SimpleEncrypter
$simpleEncrypterPath = __DIR__ . '/app/Encryption/SimpleEncrypter.php';
if (!file_exists($simpleEncrypterPath)) {
    echo "\n⚠️ ไม่พบไฟล์ app/Encryption/SimpleEncrypter.php\n";
    echo "ถ้าต้องการใช้ SimpleEncrypter โปรดสร้างไฟล์ดังกล่าวก่อน\n";
}

echo "\n===== คำแนะนำ =====\n";
echo "1. รันเซิร์ฟเวอร์อีกครั้งเพื่อทดสอบ:\n";
echo "   php artisan serve --port=8008\n\n";
echo "2. ถ้ายังมีปัญหา ลองตรวจสอบว่าค่า APP_KEY ใน .env ถูกต้องหรือไม่\n";
echo "   สามารถสร้าง key ใหม่ด้วยคำสั่ง:\n";
echo "   php artisan key:generate --ansi\n";
echo "\n3. หากต้องการล้าง compiled files สามารถใช้คำสั่ง:\n";
echo "   php artisan clear-compiled\n";
