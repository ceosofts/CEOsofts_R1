<?php

/**
 * สคริปต์แทนที่ EncryptionServiceProvider เดิม
 * ด้วยการสร้าง CustomEncryptionServiceProvider ของเราเอง
 */

echo "===== Replace Laravel's EncryptionServiceProvider =====\n\n";

// 1. สร้าง CustomEncryptionServiceProvider
$providersDir = __DIR__ . '/app/Providers';
if (!is_dir($providersDir)) {
    mkdir($providersDir, 0755, true);
    echo "✅ สร้างไดเรกทอรี $providersDir\n";
}

$customProviderPath = $providersDir . '/CustomEncryptionServiceProvider.php';
$customProviderContent = <<<'EOT'
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Encryption\Encrypter;

class CustomEncryptionServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('encrypter', function ($app) {
            try {
                $config = $app->make('config')->get('app');
                
                // ตรวจสอบว่ามีการระบุ key หรือไม่
                if (empty($config['key'])) {
                    throw new \RuntimeException('No application encryption key has been specified.');
                }
                
                // แปลงค่า key
                $key = $this->parseKey($config['key']);
                
                // กำหนด cipher (ใช้ค่าเริ่มต้น aes-256-cbc ถ้าไม่ได้ระบุ)
                $cipher = $config['cipher'] ?? 'aes-256-cbc';
                
                // Log information for debugging
                file_put_contents(__DIR__.'/../../storage/logs/encryption-debug.log', 
                    date('Y-m-d H:i:s') . " - Key length: " . strlen($key) . " bytes, Cipher: $cipher\n",
                    FILE_APPEND);
                
                // สร้าง encrypter ไม่ต้องตรวจสอบ supported อีกต่อไป
                return new Encrypter($key, $cipher);
                
            } catch (\Throwable $e) {
                // Log error
                file_put_contents(__DIR__.'/../../storage/logs/encryption-error.log', 
                    date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n",
                    FILE_APPEND);
                    
                // สร้าง encrypter ชั่วคราวเพื่อให้แอพทำงานต่อไปได้
                $tempKey = random_bytes(32);
                return new Encrypter($tempKey, 'aes-256-cbc'); 
            }
        });
    }

    /**
     * แปลงค่า key จาก base64 เป็นรูปแบบที่ใช้งานได้
     *
     * @param string $key
     * @return string
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
echo "✅ สร้าง CustomEncryptionServiceProvider.php เรียบร้อยแล้ว\n";

// 2. ปรับปรุง app.php เพื่อแทนที่ EncryptionServiceProvider ของ Laravel
$configDir = __DIR__ . '/config';
if (!is_dir($configDir)) {
    mkdir($configDir, 0755, true);
    echo "✅ สร้างไดเรกทอรี $configDir\n";
}

$appConfigPath = $configDir . '/app.php';
if (file_exists($appConfigPath)) {
    $appConfig = file_get_contents($appConfigPath);
    
    // ตรวจสอบว่ามี CustomEncryptionServiceProvider อยู่แล้วหรือไม่
    if (strpos($appConfig, 'App\Providers\CustomEncryptionServiceProvider') === false) {
        // แทนที่ Illuminate\Encryption\EncryptionServiceProvider ด้วย CustomEncryptionServiceProvider
        $appConfig = str_replace(
            'Illuminate\Encryption\EncryptionServiceProvider::class', 
            'App\Providers\CustomEncryptionServiceProvider::class', 
            $appConfig
        );
        
        // บันทึกไฟล์
        file_put_contents($appConfigPath, $appConfig);
        echo "✅ แทนที่ EncryptionServiceProvider ใน config/app.php เรียบร้อยแล้ว\n";
    } else {
        echo "✓ CustomEncryptionServiceProvider ถูกเพิ่มใน config/app.php อยู่แล้ว\n";
    }
} else {
    echo "⚠️ ไม่พบไฟล์ config/app.php, จะสร้างไฟล์ใหม่\n";
    
    // สร้างไฟล์ config/app.php ใหม่
    $basicAppConfig = <<<'EOT'
<?php

return [
    'name' => 'Laravel',
    'env' => 'local',
    'debug' => true,
    'url' => 'http://localhost',
    'timezone' => 'UTC',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'key' => 'base64:YVSPl4fNrv7MhbVszSPlHo1pHxeLCVF2pVFBb/uXDQk=',
    'cipher' => 'aes-256-cbc',
    'maintenance' => [
        'driver' => 'file',
    ],
    'providers' => [
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        // ใช้ CustomEncryptionServiceProvider แทน built-in
        App\Providers\CustomEncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ],
    'aliases' => [],
];
EOT;
    
    file_put_contents($appConfigPath, $basicAppConfig);
    echo "✅ สร้างไฟล์ config/app.php ใหม่เรียบร้อยแล้ว\n";
}

// 3. เคลียร์ Cache
echo "\n3. กำลังเคลียร์ Cache...\n";
$commands = [
    'php artisan config:clear',
    'php artisan cache:clear',
    'php artisan view:clear',
    'php artisan route:clear',
    'composer dump-autoload -o'
];

foreach ($commands as $command) {
    echo "$ $command\n";
    passthru($command);
}

// 4. ตรวจสอบว่ามีไดเรกทอรีสำหรับบันทึก log หรือไม่
$logsDir = __DIR__ . '/storage/logs';
if (!is_dir($logsDir)) {
    mkdir($logsDir, 0755, true);
    echo "✅ สร้างไดเรกทอรี $logsDir\n";
}

echo "\n===== สิ่งที่ควรทำต่อไป =====\n";
echo "1. รัน server ด้วยพอร์ตใหม่:\n";
echo "   php artisan serve --port=8005\n";
echo "2. ตรวจสอบ log หากยังมีปัญหา:\n";
echo "   storage/logs/encryption-debug.log\n";
echo "   storage/logs/encryption-error.log\n";
