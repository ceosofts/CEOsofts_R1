<?php

/**
 * สร้าง Simple Encrypter ที่ใช้ OpenSSL โดยตรง
 * เพื่อแก้ไขปัญหา Laravel Encrypter ที่มีปัญหา
 */

echo "===== Creating Simple OpenSSL Encrypter =====\n\n";

// 1. สร้างไดเรกทอรี app/Encryption
$encryptionDir = __DIR__ . '/app/Encryption';
if (!is_dir($encryptionDir)) {
    mkdir($encryptionDir, 0755, true);
}

// 2. สร้างคลาส SimpleEncrypter
$simpleEncrypterPath = $encryptionDir . '/SimpleEncrypter.php';
$simpleEncrypterContent = <<<'EOT'
<?php

namespace App\Encryption;

/**
 * SimpleEncrypter - คลาสสำหรับเข้ารหัสและถอดรหัสข้อมูลโดยตรงด้วย OpenSSL
 * แก้ไขปัญหา Laravel Encrypter ที่มีปัญหา
 */
class SimpleEncrypter
{
    /**
     * The encryption key.
     *
     * @var string
     */
    protected $key;

    /**
     * The algorithm used for encryption.
     *
     * @var string
     */
    protected $cipher;

    /**
     * Constructor - รับ key และ cipher
     *
     * @param string $key
     * @param string $cipher
     */
    public function __construct($key = null, $cipher = 'aes-256-cbc')
    {
        // ถ้าไม่ระบุ key จะดึงจาก .env
        if ($key === null) {
            $key = $this->getKeyFromEnv();
        }
        
        $this->key = $key;
        $this->cipher = $cipher;
        
        // ตรวจสอบ key และ cipher
        $this->validateKeyAndCipher();
    }

    /**
     * เข้ารหัสข้อมูล
     *
     * @param mixed $value ข้อมูลที่ต้องการเข้ารหัส
     * @param bool $serialize เข้ารหัสแบบ serialize หรือไม่
     * @return string
     */
    public function encrypt($value, $serialize = true)
    {
        // เข้ารหัสข้อมูลที่เป็น array หรือ object
        if ($serialize && (is_array($value) || is_object($value))) {
            $value = serialize($value);
        }
        
        // สร้าง IV (Initialization Vector)
        $iv = random_bytes(openssl_cipher_iv_length($this->cipher));
        
        // เข้ารหัสข้อมูลด้วย OpenSSL
        $value = openssl_encrypt($value, $this->cipher, $this->key, 0, $iv);
        
        if ($value === false) {
            throw new \RuntimeException('Could not encrypt the data: ' . openssl_error_string());
        }
        
        // คำนวณ MAC เพื่อตรวจสอบความถูกต้อง
        $mac = hash_hmac('sha256', $value, $this->key);
        
        // สร้าง payload รวม
        $json = json_encode([
            'iv' => base64_encode($iv),
            'value' => $value,
            'mac' => $mac,
        ], JSON_UNESCAPED_SLASHES);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Could not encrypt the data - JSON error: ' . json_last_error_msg());
        }
        
        // เข้ารหัส Base64 อีกครั้งเพื่อให้ปลอดภัยในการส่ง
        return base64_encode($json);
    }

    /**
     * ถอดรหัสข้อมูล
     *
     * @param string $payload ข้อมูลที่เข้ารหัสแล้ว
     * @param bool $unserialize ถอดรหัสแบบ unserialize หรือไม่
     * @return mixed
     */
    public function decrypt($payload, $unserialize = true)
    {
        // ถอดรหัส Base64 ส่วนแรก
        $payload = $this->getJsonPayload($payload);
        
        // ถอดรหัส Base64 ของ IV
        $iv = base64_decode($payload['iv']);
        
        // ถอดรหัส
        $decrypted = openssl_decrypt(
            $payload['value'], $this->cipher, $this->key, 0, $iv
        );
        
        if ($decrypted === false) {
            throw new \RuntimeException('Could not decrypt the data: ' . openssl_error_string());
        }
        
        // Unserialize ถ้าต้องการ
        return $unserialize ? unserialize($decrypted) : $decrypted;
    }

    /**
     * ดึงข้อมูล payload จาก JSON
     *
     * @param string $payload
     * @return array
     */
    protected function getJsonPayload($payload)
    {
        $payload = json_decode(base64_decode($payload), true);
        
        if (!$payload || !isset($payload['iv']) || !isset($payload['value']) || !isset($payload['mac'])) {
            throw new \RuntimeException('Invalid payload structure.');
        }
        
        // ตรวจสอบความถูกต้องของข้อมูลด้วย MAC
        $calculatedMac = hash_hmac('sha256', $payload['value'], $this->key);
        if (!hash_equals($calculatedMac, $payload['mac'])) {
            throw new \RuntimeException('Invalid MAC - ข้อมูลอาจถูกเปลี่ยนแปลง');
        }
        
        return $payload;
    }

    /**
     * ดึงค่า encryption key จาก .env
     *
     * @return string
     */
    protected function getKeyFromEnv()
    {
        $envPath = __DIR__ . '/../../.env';
        if (!file_exists($envPath)) {
            throw new \RuntimeException('Could not find .env file');
        }
        
        $env = file_get_contents($envPath);
        if (preg_match('/APP_KEY=base64:([^\s\n]+)/', $env, $matches)) {
            return base64_decode($matches[1]);
        }
        
        throw new \RuntimeException('Could not find APP_KEY in .env file');
    }

    /**
     * ตรวจสอบความถูกต้องของ key และ cipher
     */
    protected function validateKeyAndCipher()
    {
        // ตรวจสอบว่า cipher รองรับหรือไม่
        if (!in_array($this->cipher, openssl_get_cipher_methods())) {
            throw new \RuntimeException("Unsupported cipher: {$this->cipher}");
        }
        
        // ตรวจสอบความยาวของ key
        $keyLength = strlen($this->key);
        $expectedLength = 0;
        
        if ($this->cipher === 'aes-256-cbc') {
            $expectedLength = 32; // 256 bits
        } elseif ($this->cipher === 'aes-128-cbc') {
            $expectedLength = 16; // 128 bits
        }
        
        if ($keyLength !== $expectedLength) {
            throw new \RuntimeException(
                "Incorrect key length for {$this->cipher}. Expected: {$expectedLength} bytes, got: {$keyLength} bytes."
            );
        }
    }
}
EOT;

file_put_contents($simpleEncrypterPath, $simpleEncrypterContent);
echo "✅ สร้างคลาส SimpleEncrypter เรียบร้อยแล้ว\n";

// 3. สร้าง Service Provider สำหรับ SimpleEncrypter
$providersDir = __DIR__ . '/app/Providers';
if (!is_dir($providersDir)) {
    mkdir($providersDir, 0755, true);
}

$serviceProviderPath = $providersDir . '/SimpleEncryptionServiceProvider.php';
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
        // ลงทะเบียน simple-encrypter
        $this->app->singleton('simple-encrypter', function ($app) {
            return new SimpleEncrypter();
        });
        
        // ลงทะเบียนแทนที่ encrypter เดิมเพื่อความเข้ากันได้
        if (!$this->app->bound('encrypter')) {
            $this->app->singleton('encrypter', function ($app) {
                return $app->make('simple-encrypter');
            });
        }
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
echo "✅ สร้าง SimpleEncryptionServiceProvider เรียบร้อยแล้ว\n";

// 4. สร้าง helper functions
$helpersPath = __DIR__ . '/app/helpers.php';
$helpersContent = <<<'EOT'
<?php

use App\Encryption\SimpleEncrypter;

if (!function_exists('simple_encrypt')) {
    /**
     * เข้ารหัสข้อมูลด้วย SimpleEncrypter
     */
    function simple_encrypt($value, $serialize = true) {
        return app('simple-encrypter')->encrypt($value, $serialize);
    }
}

if (!function_exists('simple_decrypt')) {
    /**
     * ถอดรหัสข้อมูลด้วย SimpleEncrypter
     */
    function simple_decrypt($payload, $unserialize = true) {
        return app('simple-encrypter')->decrypt($payload, $unserialize);
    }
}

// แทนที่ฟังก์ชัน encrypt และ decrypt ดั้งเดิมเพื่อแก้ไขปัญหา
if (!function_exists('encrypt')) {
    /**
     * เข้ารหัสข้อมูล (แทนที่ฟังก์ชัน encrypt เดิม)
     */
    function encrypt($value, $serialize = true) {
        return simple_encrypt($value, $serialize);
    }
}

if (!function_exists('decrypt')) {
    /**
     * ถอดรหัสข้อมูล (แทนที่ฟังก์ชัน decrypt เดิม)
     */
    function decrypt($payload, $unserialize = true) {
        return simple_decrypt($payload, $unserialize);
    }
}
EOT;

// ตรวจสอบว่า helpers.php มีอยู่แล้วหรือไม่ และมีเนื้อหาเกี่ยวกับ encryption หรือไม่
if (file_exists($helpersPath)) {
    $existingContent = file_get_contents($helpersPath);
    if (strpos($existingContent, 'simple_encrypt') === false) {
        $helpersContent = $existingContent . "\n\n" . $helpersContent;
    } else {
        echo "✓ ฟังก์ชัน simple_encrypt มีอยู่แล้วในไฟล์ helpers.php\n";
        $helpersContent = null;
    }
}

if ($helpersContent !== null) {
    file_put_contents($helpersPath, $helpersContent);
    echo "✅ เพิ่มฟังก์ชัน helper เรียบร้อยแล้ว\n";
}

// 5. อัปเดต composer.json
$composerJsonPath = __DIR__ . '/composer.json';
if (file_exists($composerJsonPath)) {
    $composerJson = json_decode(file_get_contents($composerJsonPath), true);
    
    // เพิ่มการโหลด app/helpers.php
    if (!isset($composerJson['autoload']['files']) || !in_array('app/helpers.php', $composerJson['autoload']['files'])) {
        $composerJson['autoload']['files'] = $composerJson['autoload']['files'] ?? [];
        if (!in_array('app/helpers.php', $composerJson['autoload']['files'])) {
            $composerJson['autoload']['files'][] = 'app/helpers.php';
        }
    }
    
    // บันทึก composer.json
    file_put_contents($composerJsonPath, json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo "✅ อัปเดต composer.json เรียบร้อยแล้ว\n";
    
    // รัน composer dump-autoload
    echo "\nกำลังรัน composer dump-autoload...\n";
    passthru('composer dump-autoload -o');
}

// 6. สร้างไฟล์ทดสอบ
$testFilePath = __DIR__ . '/test-simple-encrypter.php';
$testFileContent = <<<'EOT'
<?php

require __DIR__ . '/vendor/autoload.php';

use App\Encryption\SimpleEncrypter;

echo "===== Testing SimpleEncrypter =====\n\n";

try {
    // สร้าง instance
    $encrypter = new SimpleEncrypter();
    echo "✅ สร้าง SimpleEncrypter สำเร็จ\n";
    
    // ทดสอบเข้ารหัสข้อความ
    $original = "ทดสอบการเข้ารหัสด้วย SimpleEncrypter";
    echo "\nข้อมูลต้นฉบับ: $original\n";
    
    $encrypted = $encrypter->encrypt($original);
    echo "ข้อมูลที่เข้ารหัสแล้ว: " . $encrypted . "\n";
    
    $decrypted = $encrypter->decrypt($encrypted);
    echo "ข้อมูลที่ถอดรหัสแล้ว: " . $decrypted . "\n";
    
    echo "\nผลการทดสอบ: " . ($original === $decrypted ? "✅ ผ่าน" : "❌ ไม่ผ่าน") . "\n";
    
    // ทดสอบเข้ารหัสอาร์เรย์
    echo "\nทดสอบกับข้อมูลที่เป็น Array:\n";
    $array = ['name' => 'ทดสอบ', 'value' => 123];
    $encrypted = $encrypter->encrypt($array);
    echo "เข้ารหัสสำเร็จ: " . $encrypted . "\n";
    
    $decrypted = $encrypter->decrypt($encrypted);
    echo "ถอดรหัสกลับเป็น Array สำเร็จ: " . json_encode($decrypted, JSON_UNESCAPED_UNICODE) . "\n";
    
} catch (Exception $e) {
    echo "❌ เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
EOT;

file_put_contents($testFilePath, $testFileContent);
echo "✅ สร้างไฟล์ทดสอบ test-simple-encrypter.php เรียบร้อยแล้ว\n";

// 7. ลงทะเบียน Provider ใน config/app.php
$configAppPath = __DIR__ . '/config/app.php';
if (file_exists($configAppPath)) {
    $configApp = file_get_contents($configAppPath);
    
    // ตรวจสอบว่า Provider ถูกลงทะเบียนแล้วหรือไม่
    if (strpos($configApp, 'App\Providers\SimpleEncryptionServiceProvider') === false) {
        // แทนที่ EncryptionServiceProvider ด้วย SimpleEncryptionServiceProvider
        if (strpos($configApp, 'Illuminate\Encryption\EncryptionServiceProvider') !== false) {
            $configApp = str_replace(
                'Illuminate\Encryption\EncryptionServiceProvider::class',
                'App\Providers\SimpleEncryptionServiceProvider::class',
                $configApp
            );
        }
        // หรือเพิ่มใหม่ถ้าไม่พบ EncryptionServiceProvider
        else {
            $pattern = "/('providers'\s*=>\s*\[)(.*?)(\])/s";
            if (preg_match($pattern, $configApp, $matches)) {
                $providers = $matches[2];
                $newProviders = $providers . "\n        App\Providers\SimpleEncryptionServiceProvider::class,";
                $configApp = str_replace($providers, $newProviders, $configApp);
            }
        }
        
        file_put_contents($configAppPath, $configApp);
        echo "✅ ลงทะเบียน SimpleEncryptionServiceProvider ใน config/app.php เรียบร้อยแล้ว\n";
    } else {
        echo "✓ SimpleEncryptionServiceProvider ถูกลงทะเบียนใน config/app.php อยู่แล้ว\n";
    }
}

echo "\n===== สิ่งที่ควรทำต่อไป =====\n";
echo "1. ทดสอบการใช้งาน SimpleEncrypter:\n";
echo "   $ php test-simple-encrypter.php\n";
echo "2. ล้าง Cache ของ Laravel:\n";
echo "   $ php artisan config:clear\n";
echo "   $ php artisan cache:clear\n";
echo "3. รัน Server:\n";
echo "   $ php artisan serve --port=8006\n";
