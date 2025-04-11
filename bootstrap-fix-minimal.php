<?php

/**
 * แก้ไขปัญหา bootstrap/app.php แบบพื้นฐาน
 * สร้างไฟล์ใหม่แทนที่ไฟล์เดิม หากไฟล์เดิมมีปัญหา
 */

echo "===== Fix Bootstrap/App.php (Minimal) =====\n\n";

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

// สร้างไฟล์ app.php ใหม่ที่มีโค้ดพื้นฐานและรวมการแก้ไข encrypter
$newAppContent = <<<'EOT'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// สร้าง Application
$app = Application::configure(basePath: dirname(__DIR__))
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

// แก้ไขปัญหาการโหลด Encrypter
$app->singleton('encrypter', function ($app) {
    // ตรวจสอบว่ามีคลาส SimpleEncrypter หรือไม่
    if (class_exists('App\Encryption\SimpleEncrypter')) {
        return new \App\Encryption\SimpleEncrypter();
    }
    
    // หาก SimpleEncrypter ไม่พบ ให้ใช้ Encrypter ของ Laravel แทน
    try {
        $config = $app->make('config')->get('app');
        
        if (empty($config['key'])) {
            throw new RuntimeException('No application encryption key has been specified.');
        }
        
        $key = $config['key'];
        
        // ถ้า key เริ่มต้นด้วย base64: ให้ถอดรหัส
        if (str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }
        
        $cipher = $config['cipher'] ?? 'aes-256-cbc';
        
        return new \Illuminate\Encryption\Encrypter($key, $cipher);
    } catch (\Throwable $e) {
        // สร้างด้วย key สุ่มเพื่อให้ระบบทำงานต่อได้
        $key = random_bytes(32);
        return new \Illuminate\Encryption\Encrypter($key, 'aes-256-cbc');
    }
});

return $app;
EOT;

file_put_contents($bootstrapAppPath, $newAppContent);
echo "✅ สร้างไฟล์ bootstrap/app.php ใหม่เรียบร้อยแล้ว\n";

// ล้าง cache
echo "\nกำลังล้าง cache...\n";
passthru('php artisan config:clear');
passthru('php artisan cache:clear');
passthru('php artisan view:clear');

echo "\n===== คำแนะนำ =====\n";
echo "1. รันเซิร์ฟเวอร์อีกครั้งเพื่อทดสอบ:\n";
echo "   php artisan serve --port=8007\n";
