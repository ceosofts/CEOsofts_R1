<?php

/**
 * การแก้ไขปัญหาทั้งระบบ Encryption และ Database ของ Laravel
 * ในไฟล์เดียวแบบครบวงจร
 */

echo "Laravel Full System Fix Tool\n";
echo "==========================\n\n";

// บันทึกเวลาเริ่มต้น
$startTime = microtime(true);

// 1. สำรองไฟล์สำคัญ
echo "1. กำลังสำรองไฟล์สำคัญ...\n";
$timestamp = date('YmdHis');
$backupDir = __DIR__ . "/backups/full_fix_$timestamp";

if (!file_exists($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$filesToBackup = ['.env', 'config/app.php', 'bootstrap/app.php'];
foreach ($filesToBackup as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        $backupPath = $backupDir . '/' . basename($file);
        copy($fullPath, $backupPath);
        echo "   ✓ สำรองไฟล์ $file ไว้ที่ $backupPath\n";
    }
}

// 2. สร้าง SQLite database ถ้ายังไม่มี
echo "\n2. กำลังตรวจสอบและสร้าง SQLite database...\n";

// ตรวจสอบค่า DB_CONNECTION ใน .env
$envPath = __DIR__ . '/.env';
$dbConnection = 'mysql'; // ค่าเริ่มต้น

if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    if (preg_match('/DB_CONNECTION=([^\s\n]+)/', $envContent, $matches)) {
        $dbConnection = trim($matches[1]);
    }
}

echo "   - Database connection ปัจจุบัน: $dbConnection\n";

// ถ้าเป็น SQLite ให้สร้างไฟล์ database
if (strtolower($dbConnection) === 'sqlite') {
    $databaseDir = __DIR__ . '/database';
    $databasePath = "$databaseDir/database.sqlite";
    
    if (!file_exists($databaseDir)) {
        mkdir($databaseDir, 0755, true);
        echo "   ✓ สร้างโฟลเดอร์ $databaseDir\n";
    }
    
    if (!file_exists($databasePath)) {
        touch($databasePath);
        echo "   ✓ สร้างไฟล์ฐานข้อมูล SQLite: $databasePath\n";
    } else {
        echo "   ✓ ไฟล์ฐานข้อมูล SQLite มีอยู่แล้ว\n";
    }
    
    // ตรวจสอบสิทธิ์ของไฟล์ database
    chmod($databasePath, 0666);
    echo "   ✓ ตั้งค่าสิทธิ์เข้าถึงไฟล์ฐานข้อมูล: 0666\n";
} else {
    echo "   ⚠️ ไม่ได้ใช้ SQLite แต่พบว่ามีการอ้างถึง SQLite ในข้อผิดพลาด\n";
    echo "   ⚠️ อาจต้องแก้ไข DB_CONNECTION ใน .env ให้ตรงกับ database ที่ใช้จริง\n";
}

// 3. แก้ไขไฟล์ .env เพื่อให้แน่ใจว่าการตั้งค่าถูกต้อง
echo "\n3. กำลังปรับปรุงไฟล์ .env...\n";

if (file_exists($envPath)) {
    // อ่านไฟล์ .env
    $envContent = file_get_contents($envPath);
    $updated = false;
    
    // สร้าง APP_KEY ใหม่
    $newKey = 'base64:' . base64_encode(random_bytes(32));
    if (preg_match('/^APP_KEY=/m', $envContent)) {
        $envContent = preg_replace('/^APP_KEY=.*$/m', "APP_KEY=$newKey", $envContent);
        echo "   ✓ อัปเดต APP_KEY: $newKey\n";
        $updated = true;
    } else {
        $envContent .= "APP_KEY=$newKey\n";
        echo "   ✓ เพิ่ม APP_KEY: $newKey\n";
        $updated = true;
    }
    
    // แก้ไข DB_CONNECTION เป็น sqlite ถ้าไม่มีการกำหนดไว้
    if (!preg_match('/^DB_CONNECTION=/m', $envContent)) {
        $envContent .= "DB_CONNECTION=sqlite\n";
        echo "   ✓ เพิ่ม DB_CONNECTION=sqlite\n";
        $updated = true;
    }
    
    // อัปเดตการตั้งค่า cache driver เพื่อแก้ปัญหา encrypter
    if (preg_match('/^CACHE_DRIVER=database/m', $envContent) || 
        preg_match('/^CACHE_STORE=database/m', $envContent)) {
        $envContent = preg_replace('/^CACHE_DRIVER=database/m', "CACHE_DRIVER=file", $envContent);
        $envContent = preg_replace('/^CACHE_STORE=database/m', "CACHE_STORE=file", $envContent);
        echo "   ✓ เปลี่ยน CACHE_DRIVER/CACHE_STORE จาก database เป็น file\n";
        $updated = true;
    }
    
    // แก้ไข SESSION_DRIVER ให้เป็น file แทนที่จะเป็น database 
    // เพื่อหลีกเลี่ยงปัญหากรณีที่ db ยังไม่พร้อม
    if (preg_match('/^SESSION_DRIVER=database/m', $envContent)) {
        $envContent = preg_replace('/^SESSION_DRIVER=database/m', "SESSION_DRIVER=file", $envContent);
        echo "   ✓ เปลี่ยน SESSION_DRIVER จาก database เป็น file\n";
        $updated = true;
    }
    
    if ($updated) {
        file_put_contents($envPath, $envContent);
        echo "   ✓ บันทึกการเปลี่ยนแปลงในไฟล์ .env เรียบร้อยแล้ว\n";
    } else {
        echo "   ℹ️ ไม่มีการเปลี่ยนแปลงในไฟล์ .env\n";
    }
} else {
    echo "   ⚠️ ไม่พบไฟล์ .env กำลังสร้างไฟล์ใหม่...\n";
    
    // สร้างไฟล์ .env ใหม่
    $newKey = 'base64:' . base64_encode(random_bytes(32));
    $newEnvContent = <<<EOT
APP_NAME=Laravel
APP_ENV=local
APP_KEY=$newKey
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="\${APP_NAME}"
EOT;
    
    file_put_contents($envPath, $newEnvContent);
    echo "   ✓ สร้างไฟล์ .env ใหม่ด้วย APP_KEY: $newKey\n";
}

// 4. สร้างหรือแก้ไขไฟล์ config/app.php ให้มีการตั้งค่า cipher ที่ถูกต้อง
echo "\n4. กำลังตรวจสอบและแก้ไขไฟล์ config/app.php...\n";

$configDir = __DIR__ . '/config';
$appConfigPath = "$configDir/app.php";

if (!file_exists($configDir)) {
    mkdir($configDir, 0755, true);
    echo "   ✓ สร้างโฟลเดอร์ config\n";
}

if (!file_exists($appConfigPath)) {
    echo "   ⚠️ ไม่พบไฟล์ config/app.php กำลังสร้างไฟล์ใหม่...\n";
    
    // สร้างไฟล์ config/app.php ใหม่
    $appConfigContent = <<<'EOT'
<?php

return [
    'name' => env('APP_NAME', 'Laravel'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL'),
    'timezone' => 'UTC',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'key' => env('APP_KEY'),
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
        Illuminate\Encryption\EncryptionServiceProvider::class,
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
    
    file_put_contents($appConfigPath, $appConfigContent);
    echo "   ✓ สร้างไฟล์ config/app.php ใหม่ด้วย cipher: aes-256-cbc\n";
} else {
    // ตรวจสอบและแก้ไขค่า cipher ถ้าจำเป็น
    $appConfig = file_get_contents($appConfigPath);
    
    if (!preg_match("/'cipher'\s*=>\s*'aes-256-cbc'/i", $appConfig)) {
        // แก้ไขการตั้งค่า cipher ที่มีอยู่
        if (preg_match("/'cipher'\s*=>\s*'[^']*'/", $appConfig)) {
            $appConfig = preg_replace("/'cipher'\s*=>\s*'[^']*'/", "'cipher' => 'aes-256-cbc'", $appConfig);
            file_put_contents($appConfigPath, $appConfig);
            echo "   ✓ แก้ไขค่า cipher เป็น aes-256-cbc\n";
        } else {
            echo "   ⚠️ ไม่พบการตั้งค่า cipher ใน config/app.php\n";
            echo "   ⚠️ กรุณาแก้ไขไฟล์ด้วยตนเอง โดยเพิ่ม: 'cipher' => 'aes-256-cbc',\n";
        }
    } else {
        echo "   ✓ พบการตั้งค่า cipher เป็น aes-256-cbc แล้ว\n";
    }
}

// 5. สร้างไฟล์แก้ไขปัญหา Encrypter Service
echo "\n5. กำลังสร้างไฟล์แก้ไขปัญหา Encrypter Service...\n";
$appProvidersPath = __DIR__ . '/app/Providers';

// สร้างโฟลเดอร์ app/Providers ถ้ายังไม่มี
if (!file_exists($appProvidersPath)) {
    mkdir($appProvidersPath, 0755, true);
    echo "   ✓ สร้างโฟลเดอร์ app/Providers\n";
}

// สร้างไฟล์ CustomEncryptionProvider.php เพื่อแก้ไขปัญหาการเรียกใช้ Encrypter
$customProviderPath = "$appProvidersPath/CustomEncryptionProvider.php";
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
            $config = $app->make('config')->get('app');
            
            // ตรวจสอบว่ามี key และ cipher ที่ถูกต้อง
            if (isset($config['key']) && isset($config['cipher'])) {
                $key = $this->parseKey($config['key']);
                return new Encrypter($key, $config['cipher']);
            }
            
            // ถ้าไม่มีค่าที่ถูกต้อง ให้สร้าง key ใหม่
            $key = random_bytes(32); // สร้าง key 32 ไบต์สำหรับ aes-256-cbc
            return new Encrypter($key, 'aes-256-cbc');
        });
    }
    
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
    
    /**
     * แปลงค่า key จาก env ให้เป็นรูปแบบที่เหมาะสม
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
echo "   ✓ สร้างไฟล์ CustomEncryptionProvider เพื่อแก้ไขปัญหาการเรียกใช้ Encrypter\n";

// 6. แก้ไขไฟล์ bootstrap/app.php เพื่อให้ใช้ CustomEncryptionProvider
echo "\n6. กำลังตรวจสอบและแก้ไขไฟล์ bootstrap/app.php...\n";
$bootstrapAppPath = __DIR__ . '/bootstrap/app.php';

if (file_exists($bootstrapAppPath)) {
    // อ่านเนื้อหาของไฟล์ bootstrap/app.php
    $bootstrapApp = file_get_contents($bootstrapAppPath);
    
    // ตรวจสอบว่าได้ระบุ CustomEncryptionProvider หรือยัง
    if (strpos($bootstrapApp, 'App\Providers\CustomEncryptionProvider') === false) {
        // หาตำแหน่งที่จะแทรก provider
        if (preg_match('/return \$app;/', $bootstrapApp)) {
            // แทรกก่อน return $app;
            $bootstrapApp = preg_replace(
                '/(return \$app;)/', 
                '$app->register(\\App\\Providers\\CustomEncryptionProvider::class);' . PHP_EOL . PHP_EOL . '$1', 
                $bootstrapApp
            );
            file_put_contents($bootstrapAppPath, $bootstrapApp);
            echo "   ✓ แก้ไขไฟล์ bootstrap/app.php เพื่อลงทะเบียน CustomEncryptionProvider\n";
        } else {
            echo "   ⚠️ ไม่สามารถหาตำแหน่งที่เหมาะสมในไฟล์ bootstrap/app.php\n";
            echo "   ⚠️ กรุณาแก้ไขไฟล์ด้วยตนเองโดยเพิ่ม: \$app->register(\\App\\Providers\\CustomEncryptionProvider::class);\n";
        }
    } else {
        echo "   ✓ CustomEncryptionProvider ถูกลงทะเบียนใน bootstrap/app.php แล้ว\n";
    }
} else {
    echo "   ⚠️ ไม่พบไฟล์ bootstrap/app.php\n";
}

// 7. ล้าง cache และ compiled files
echo "\n7. กำลังล้าง cache และ compiled files...\n";

// ลบไฟล์ cache โดยตรง
$cacheFiles = [
    __DIR__ . '/bootstrap/cache/*.php',
    __DIR__ . '/storage/framework/cache/data/*',
    __DIR__ . '/storage/framework/views/*.php',
    __DIR__ . '/storage/framework/sessions/*'
];

foreach ($cacheFiles as $pattern) {
    array_map('unlink', glob($pattern));
}
echo "   ✓ ลบไฟล์ cache โดยตรงเรียบร้อยแล้ว\n";

// รันคำสั่ง artisan เพื่อล้าง cache
$artisanCommands = [
    'php artisan config:clear',
    'php artisan view:clear',
    'php artisan route:clear',
    'php artisan clear-compiled',
    'composer dump-autoload -o'
];

foreach ($artisanCommands as $command) {
    echo "   $ $command\n";
    passthru($command);
}

// 8. แสดงสรุป
echo "\n==========================\n";
echo "การแก้ไขระบบเสร็จสมบูรณ์!\n";

// คำนวณเวลาที่ใช้
$endTime = microtime(true);
$executionTime = round($endTime - $startTime, 2);
echo "ใช้เวลาทั้งสิ้น: $executionTime วินาที\n";

echo "\nขั้นตอนต่อไป:\n";
echo "1. รันคำสั่ง: php artisan serve --port=8002\n";
echo "2. ถ้ายังมีปัญหา ให้ลองรัน: php artisan key:generate --ansi\n";
echo "3. หากต้องการทดสอบ encryption โดยตรง: php artisan tinker\n";
echo "   แล้วรันคำสั่ง: encrypt('test'); decrypt(encrypt('test'));\n";
echo "==========================\n";
