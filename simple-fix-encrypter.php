<?php

/**
 * แก้ไขปัญหา Encrypter อย่างง่าย โดยการสร้าง Interface และ CustomEncryptionProvider
 */

echo "===== Simple Encrypter Fix =====\n\n";

// ตรวจสอบว่ามีโฟลเดอร์ app/Providers หรือไม่
if (!file_exists(__DIR__ . '/app/Providers')) {
    mkdir(__DIR__ . '/app/Providers', 0755, true);
    echo "✅ สร้างโฟลเดอร์ app/Providers เรียบร้อยแล้ว\n\n";
}

// สร้างไฟล์ CustomEncryptionProvider.php
$customProviderPath = __DIR__ . '/app/Providers/CustomEncryptionProvider.php';
$customProviderContent = <<<'EOT'
<?php

namespace App\Providers;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\ServiceProvider;

class CustomEncryptionProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // ลงทะเบียน encrypter เอง เพื่อแก้ปัญหา
        $this->app->singleton('encrypter', function ($app) {
            $config = $app->make('config');
            $key = $this->parseKey($config->get('app.key'));
            $cipher = $config->get('app.cipher', 'aes-256-cbc');
            
            return new Encrypter($key, $cipher);
        });
    }
    
    /**
     * แปลง key ให้อยู่ในรูปแบบที่ถูกต้อง
     */
    protected function parseKey($key)
    {
        if (strpos($key, 'base64:') === 0) {
            $key = base64_decode(substr($key, 7));
        }
        
        return $key;
    }
}
EOT;

file_put_contents($customProviderPath, $customProviderContent);
echo "✅ สร้างไฟล์ CustomEncryptionProvider.php เรียบร้อยแล้ว\n";

// ตรวจสอบว่ามีไฟล์ bootstrap/app.php หรือไม่
$bootstrapAppPath = __DIR__ . '/bootstrap/app.php';
if (file_exists($bootstrapAppPath)) {
    // อ่านเนื้อหาของไฟล์
    $bootstrapContent = file_get_contents($bootstrapAppPath);
    
    // ตรวจสอบว่าได้มีการเพิ่ม CustomEncryptionProvider ไปแล้วหรือยัง
    if (strpos($bootstrapContent, 'App\Providers\CustomEncryptionProvider') === false) {
        // หาตำแหน่ง return $app; เพื่อเพิ่ม code ก่อนส่วนนี้
        $returnPos = strpos($bootstrapContent, 'return $app;');
        
        if ($returnPos !== false) {
            // เพิ่ม code ก่อนบรรทัด return $app;
            $newContent = substr($bootstrapContent, 0, $returnPos);
            $newContent .= "\$app->register(\\App\\Providers\\CustomEncryptionProvider::class);\n\n";
            $newContent .= substr($bootstrapContent, $returnPos);
            
            // เขียนไฟล์ใหม่
            file_put_contents($bootstrapAppPath, $newContent);
            echo "✅ เพิ่ม CustomEncryptionProvider ใน bootstrap/app.php เรียบร้อยแล้ว\n";
        } else {
            echo "❌ ไม่พบตำแหน่งที่จะเพิ่มโค้ดใน bootstrap/app.php\n";
        }
    } else {
        echo "✓ CustomEncryptionProvider ถูกเพิ่มใน bootstrap/app.php อยู่แล้ว\n";
    }
} else {
    echo "❌ ไม่พบไฟล์ bootstrap/app.php\n";
}

// ล้าง Cache
echo "\nกำลังล้าง Cache...\n";
$cacheCommands = [
    'php artisan config:clear',
    'php artisan cache:clear',
    'php artisan view:clear',
    'php artisan route:clear',
    'composer dump-autoload'
];

foreach ($cacheCommands as $command) {
    echo "$ $command\n";
    passthru($command);
}

// แนะนำขั้นตอนต่อไป
echo "\n===== ขั้นตอนต่อไป =====\n";
echo "1. ลองรัน PHP Server อีกครั้ง:\n";
echo "   php artisan serve --port=8004\n";
echo "2. หากยังพบปัญหา อาจต้องสร้าง APP_KEY ใหม่:\n";
echo "   php artisan key:generate --ansi\n";
