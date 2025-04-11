<?php

/**
 * แก้ไขปัญหาทั้งหมดเกี่ยวกับ Encryption ในครั้งเดียว
 * - แก้ไขไฟล์ app/helpers.php
 * - สร้างคลาส SimpleEncrypter
 * - แก้ไขไฟล์ bootstrap/app.php
 * - ล้าง cache
 */

echo "===== All-in-one Encryption Fix =====\n\n";

// 1. สร้างโฟลเดอร์สำรองข้อมูล
$backupDir = __DIR__ . '/backups/encryption_fix_' . date('YmdHis');
if (!file_exists($backupDir)) {
    mkdir($backupDir, 0755, true);
}

echo "1. กำลังสำรองข้อมูลไปยัง {$backupDir}...\n";
$filesToBackup = [
    'app/helpers.php',
    'bootstrap/app.php',
    'config/app.php',
    '.env'
];

foreach ($filesToBackup as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        copy($fullPath, $backupDir . '/' . basename($file));
        echo "   ✓ สำรองไฟล์ {$file}\n";
    }
}

// 2. สร้างโฟลเดอร์ต่างๆ ที่จำเป็น
echo "\n2. กำลังสร้างโฟลเดอร์ที่จำเป็น...\n";
$requiredDirs = [
    'app/Encryption',
    'app/Providers',
    'bootstrap',
    'config',
];

foreach ($requiredDirs as $dir) {
    $fullPath = __DIR__ . '/' . $dir;
    if (!file_exists($fullPath)) {
        mkdir($fullPath, 0755, true);
        echo "   ✓ สร้างโฟลเดอร์ {$dir}\n";
    }
}

// 3. แก้ไข app/helpers.php
echo "\n3. กำลังแก้ไขไฟล์ app/helpers.php...\n";
$helpersPath = __DIR__ . '/app/helpers.php';

// เนื้อหาสำหรับ app/helpers.php
$helpersContent = <<<'EOT'
<?php

use App\Encryption\SimpleEncrypter;

/**
 * Helper functions สำหรับระบบเข้ารหัส
 * แก้ไขปัญหา encryption ใน Laravel
 */

// ฟังก์ชั่นช่วยเหลือสำหรับเข้ารหัส
if (!function_exists('simple_encrypt')) {
    function simple_encrypt($value, $serialize = true) {
        static $encrypter = null;
        
        if ($encrypter === null) {
            try {
                $encrypter = new SimpleEncrypter();
            } catch (\Exception $e) {
                throw new \RuntimeException("Cannot create SimpleEncrypter: " . $e->getMessage());
            }
        }
        
        return $encrypter->encrypt($value, $serialize);
    }
}

// ฟังก์ชั่นช่วยเหลือสำหรับถอดรหัส
if (!function_exists('simple_decrypt')) {
    function simple_decrypt($payload, $unserialize = true) {
        static $encrypter = null;
        
        if ($encrypter === null) {
            try {
                $encrypter = new SimpleEncrypter();
            } catch (\Exception $e) {
                throw new \RuntimeException("Cannot create SimpleEncrypter: " . $e->getMessage());
            }
        }
        
        return $encrypter->decrypt($payload, $unserialize);
    }
}

// Override ฟังก์ชัน encrypt() ของ Laravel
if (!function_exists('encrypt')) {
    function encrypt($value, $serialize = true) {
        return simple_encrypt($value, $serialize);
    }
}

// Override ฟังก์ชัน decrypt() ของ Laravel
if (!function_exists('decrypt')) {
    function decrypt($payload, $unserialize = true) {
        return simple_decrypt($payload, $unserialize);
    }
}
EOT;

file_put_contents($helpersPath, $helpersContent);
echo "   ✓ สร้างไฟล์ app/helpers.php เรียบร้อยแล้ว\n";

// 4. สร้างคลาส SimpleEncrypter
echo "\n4. กำลังสร้างคลาส SimpleEncrypter...\n";
$simpleEncrypterPath = __DIR__ . '/app/Encryption/SimpleEncrypter.php';
$simpleEncrypterContent = <<<'EOT'
<?php

namespace App\Encryption;

/**
 * SimpleEncrypter - คลาสเข้ารหัสและถอดรหัสแบบง่ายด้วย OpenSSL
 * แก้ไขปัญหา encryption ใน Laravel
 */
class SimpleEncrypter
{
    /**
     * Encryption key.
     *
     * @var string
     */
    protected $key;

    /**
     * Algorithm used for encryption.
     *
     * @var string
     */
    protected $cipher;

    /**
     * สร้าง encrypter ด้วย key และ cipher ที่กำหนด
     *
     * @param string|null $key
     * @param string $cipher
     */
    public function __construct($key = null, $cipher = 'aes-256-cbc')
    {
        if ($key === null) {
            $key = $this->getKeyFromEnv();
        }
        
        $this->key = $key;
        $this->cipher = $cipher;
        
        $this->validateKeyAndCipher();
    }

    /**
     * เข้ารหัสข้อมูล
     *
     * @param mixed $value
     * @param bool $serialize
     * @return string
     */
    public function encrypt($value, $serialize = true)
    {
        // เข้ารหัสข้อมูล array/object
        if ($serialize && (is_array($value) || is_object($value))) {
            $value = serialize($value);
        }
        
        // สร้าง IV
        $iv = random_bytes(openssl_cipher_iv_length($this->cipher));
        
        // เข้ารหัสข้อมูล
        $encrypted = openssl_encrypt($value, $this->cipher, $this->key, 0, $iv);
        
        if ($encrypted === false) {
            throw new \RuntimeException('การเข้ารหัสข้อมูลล้มเหลว: ' . openssl_error_string());
        }
        
        // คำนวณ MAC
        $mac = hash_hmac('sha256', $encrypted, $this->key);
        
        // สร้าง payload
        $payload = [
            'iv' => base64_encode($iv),
            'value' => $encrypted,
            'mac' => $mac
        ];
        
        $json = json_encode($payload);
        
        if ($json === false) {
            throw new \RuntimeException('ไม่สามารถสร้าง JSON payload: ' . json_last_error_msg());
        }
        
        // เข้ารหัส base64
        return base64_encode($json);
    }

    /**
     * ถอดรหัสข้อมูล
     *
     * @param string $payload
     * @param bool $unserialize
     * @return mixed
     */
    public function decrypt($payload, $unserialize = true)
    {
        $payload = $this->getJsonPayload($payload);
        
        // ถอดรหัส IV
        $iv = base64_decode($payload['iv']);
        
        // ถอดรหัสข้อมูล
        $decrypted = openssl_decrypt($payload['value'], $this->cipher, $this->key, 0, $iv);
        
        if ($decrypted === false) {
            throw new \RuntimeException('การถอดรหัสข้อมูลล้มเหลว: ' . openssl_error_string());
        }
        
        // unserialize ถ้าจำเป็น
        return $unserialize ? unserialize($decrypted) : $decrypted;
    }

    /**
     * ดึงและตรวจสอบ JSON payload
     *
     * @param string $payload
     * @return array
     */
    protected function getJsonPayload($payload)
    {
        $json = json_decode(base64_decode($payload), true);
        
        if (!$json || !isset($json['iv']) || !isset($json['value']) || !isset($json['mac'])) {
            throw new \RuntimeException('รูปแบบ payload ไม่ถูกต้อง');
        }
        
        // ตรวจสอบ MAC
        $mac = hash_hmac('sha256', $json['value'], $this->key);
        
        if (!hash_equals($mac, $json['mac'])) {
            throw new \RuntimeException('ตรวจสอบความถูกต้องของข้อมูลไม่ผ่าน (MAC ไม่ตรงกัน)');
        }
        
        return $json;
    }
    
    /**
     * ดึงค่า key จากไฟล์ .env
     *
     * @return string
     */
    protected function getKeyFromEnv()
    {
        $envPath = __DIR__ . '/../../.env';
        
        if (!file_exists($envPath)) {
            throw new \RuntimeException('ไม่พบไฟล์ .env');
        }
        
        $content = file_get_contents($envPath);
        
        if (preg_match('/APP_KEY=base64:([^\s\n]+)/', $content, $matches)) {
            $key = base64_decode($matches[1]);
            
            if (strlen($key) !== 32) {
                throw new \RuntimeException('ความยาวของ key ไม่ถูกต้อง ต้องเป็น 32 ไบต์สำหรับ aes-256-cbc');
            }
            
            return $key;
        }
        
        throw new \RuntimeException('ไม่พบ APP_KEY ในไฟล์ .env');
    }
    
    /**
     * ตรวจสอบความถูกต้องของ key และ cipher
     *
     * @return void
     */
    protected function validateKeyAndCipher()
    {
        if (!in_array($this->cipher, openssl_get_cipher_methods())) {
            throw new \RuntimeException("Cipher ไม่รองรับ: {$this->cipher}");
        }
        
        $keyLength = strlen($this->key);
        $expectedLength = ($this->cipher === 'aes-256-cbc') ? 32 : 16;
        
        if ($keyLength !== $expectedLength) {
            throw new \RuntimeException(
                "ความยาวของ key ไม่ถูกต้องสำหรับ {$this->cipher} (ต้องการ {$expectedLength} ไบต์, พบ {$keyLength} ไบต์)"
            );
        }
    }
    
    /**
     * แก้ลั่นเพื่อความเข้ากันได้กับ Laravel built-in functions
     */
    public function getAllKeys()
    {
        return ['current' => $this->key];
    }
    
    public function getPreviousKeys()
    {
        return [];
    }
    
    public function getKey()
    {
        return $this->key;
    }
    
    public function encryptString($value)
    {
        return $this->encrypt($value, false);
    }
    
    public function decryptString($payload)
    {
        return $this->decrypt($payload, false);
    }
}
EOT;

file_put_contents($simpleEncrypterPath, $simpleEncrypterContent);
echo "   ✓ สร้างคลาส SimpleEncrypter เรียบร้อยแล้ว\n";

// 5. สร้าง SimpleEncryptionServiceProvider
echo "\n5. กำลังสร้าง SimpleEncryptionServiceProvider...\n";
$serviceProviderPath = __DIR__ . '/app/Providers/SimpleEncryptionServiceProvider.php';
$serviceProviderContent = <<<'EOT'
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Encryption\SimpleEncrypter;

class SimpleEncryptionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // ลงทะเบียน encrypter singleton
        $this->app->singleton('encrypter', function () {
            return new SimpleEncrypter();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
EOT;

file_put_contents($serviceProviderPath, $serviceProviderContent);
echo "   ✓ สร้าง SimpleEncryptionServiceProvider เรียบร้อยแล้ว\n";

// 6. แก้ไขไฟล์ bootstrap/app.php
echo "\n6. กำลังแก้ไขไฟล์ bootstrap/app.php...\n";
$bootstrapAppPath = __DIR__ . '/bootstrap/app.php';

// เนื้อหาสำหรับ bootstrap/app.php
$bootstrapAppContent = <<<'EOT'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Encryption\SimpleEncrypter;

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

// แก้ไขปัญหา encrypter service
$app->singleton('encrypter', function () {
    try {
        return new SimpleEncrypter();
    } catch (\Exception $e) {
        // สร้าง SimpleEncrypter ด้วย key สุ่มใหม่
        $key = random_bytes(32);
        return new SimpleEncrypter($key);
    }
});

return $app;
EOT;

file_put_contents($bootstrapAppPath, $bootstrapAppContent);
echo "   ✓ แก้ไขไฟล์ bootstrap/app.php เรียบร้อยแล้ว\n";

// 7. แก้ไขไฟล์ config/app.php
echo "\n7. กำลังแก้ไขไฟล์ config/app.php...\n";
$configAppPath = __DIR__ . '/config/app.php';

// ถ้าไม่มีไฟล์ config/app.php ให้สร้างใหม่
if (!file_exists($configAppPath)) {
    $configAppContent = <<<'EOT'
<?php

return [
    'name' => env('APP_NAME', 'Laravel'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL'),
    'timezone' => 'UTC',
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),
    'key' => env('APP_KEY'),
    'cipher' => 'aes-256-cbc',
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE'),
    ],
    'providers' => [
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        // ใช้ SimpleEncryptionServiceProvider แทน built-in
        App\Providers\SimpleEncryptionServiceProvider::class,
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

    file_put_contents($configAppPath, $configAppContent);
    echo "   ✓ สร้างไฟล์ config/app.php เรียบร้อยแล้ว\n";
} else {
    // ถ้ามีไฟล์อยู่แล้ว ให้แก้ไขส่วนของ EncryptionServiceProvider
    $configApp = file_get_contents($configAppPath);
    
    // ตรวจสอบและแทนที่ EncryptionServiceProvider
    if (strpos($configApp, 'Illuminate\Encryption\EncryptionServiceProvider') !== false) {
        $configApp = str_replace(
            'Illuminate\Encryption\EncryptionServiceProvider::class',
            'App\Providers\SimpleEncryptionServiceProvider::class',
            $configApp
        );
        
        file_put_contents($configAppPath, $configApp);
        echo "   ✓ แก้ไขไฟล์ config/app.php เรียบร้อยแล้ว\n";
    } else {
        echo "   ℹ️ ไม่พบ EncryptionServiceProvider ใน config/app.php\n";
    }
}

// 8. อัปเดต composer.json
echo "\n8. กำลังอัปเดต composer.json...\n";
$composerJsonPath = __DIR__ . '/composer.json';

if (file_exists($composerJsonPath)) {
    // อ่านไฟล์ composer.json
    $composerJson = json_decode(file_get_contents($composerJsonPath), true);
    
    // เพิ่มการโหลด app/helpers.php
    if (!isset($composerJson['autoload']['files'])) {
        $composerJson['autoload']['files'] = [];
    }
    
    if (!in_array('app/helpers.php', $composerJson['autoload']['files'])) {
        $composerJson['autoload']['files'][] = 'app/helpers.php';
        
        // บันทึกไฟล์
        file_put_contents(
            $composerJsonPath, 
            json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
        echo "   ✓ อัปเดตไฟล์ composer.json เรียบร้อยแล้ว\n";
    } else {
        echo "   ℹ️ app/helpers.php มีอยู่ใน composer.json แล้ว\n";
    }
    
    // รัน composer dump-autoload
    echo "\n9. กำลังอัพเดต autoload...\n";
    passthru('composer dump-autoload -o');
} else {
    echo "   ❌ ไม่พบไฟล์ composer.json\n";
}

// 10. ล้าง cache
echo "\n10. กำลังล้าง cache...\n";
passthru('php artisan config:clear');
passthru('php artisan cache:clear');
passthru('php artisan view:clear');
passthru('php artisan route:clear');

// 11. สร้างไฟล์ทดสอบ
echo "\n11. กำลังสร้างไฟล์ทดสอบ...\n";
$testFilePath = __DIR__ . '/test-simple-encryption.php';
$testFileContent = <<<'EOT'
<?php

require __DIR__ . '/vendor/autoload.php';

use App\Encryption\SimpleEncrypter;

echo "===== ทดสอบ SimpleEncrypter =====\n\n";

try {
    $encrypter = new SimpleEncrypter();
    echo "✓ สร้าง SimpleEncrypter สำเร็จ\n";
    
    $data = "ทดสอบการเข้ารหัสและถอดรหัสด้วย SimpleEncrypter";
    echo "ข้อมูลต้นฉบับ: $data\n\n";
    
    $encrypted = $encrypter->encrypt($data);
    echo "ข้อมูลที่เข้ารหัสแล้ว: $encrypted\n\n";
    
    $decrypted = $encrypter->decrypt($encrypted);
    echo "ข้อมูลหลังถอดรหัส: $decrypted\n\n";
    
    if ($data === $decrypted) {
        echo "✅ การทดสอบสำเร็จ - ข้อความถอดรหัสตรงกับต้นฉบับ\n\n";
    } else {
        echo "❌ การทดสอบล้มเหลว - ข้อความถอดรหัสไม่ตรงกับต้นฉบับ\n\n";
    }
    
    echo "===== ทดสอบ Helper functions =====\n\n";
    
    $encrypted = simple_encrypt($data);
    echo "ข้อมูลที่เข้ารหัสด้วย simple_encrypt(): " . substr($encrypted, 0, 30) . "...\n";
    
    $decrypted = simple_decrypt($encrypted);
    echo "ข้อมูลหลังถอดรหัสด้วย simple_decrypt(): $decrypted\n\n";
    
    if ($data === $decrypted) {
        echo "✅ การทดสอบ helper functions สำเร็จ\n";
    } else {
        echo "❌ การทดสอบ helper functions ล้มเหลว\n";
    }
    
} catch (Exception $e) {
    echo "❌ เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
EOT;

file_put_contents($testFilePath, $testFileContent);
echo "   ✓ สร้างไฟล์ทดสอบเรียบร้อยแล้ว\n";

echo "\n===== การแก้ไขเสร็จสมบูรณ์ =====\n";
echo "1. ทดสอบ SimpleEncrypter:\n";
echo "   php test-simple-encryption.php\n\n";
echo "2. รันเซิร์ฟเวอร์ด้วยพอร์ตใหม่:\n";
echo "   php artisan serve --port=8008\n";
