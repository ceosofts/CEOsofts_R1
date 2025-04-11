<?php

/**
 * แก้ไขปัญหา EncryptException: Could not encrypt the data
 * โดยการแทนที่ Encrypter ของ Laravel ด้วย Encrypter แบบกำหนดเองที่ใช้ OpenSSL โดยตรง
 */

echo "===== Laravel Encrypter Fix =====\n\n";

// 1. สำรองไฟล์ที่จะแก้ไข
$backupDir = __DIR__ . '/backups/encryption_fix_' . date('YmdHis');
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
    echo "✅ สร้างไดเรกทอรีสำรอง {$backupDir}\n";
}

// 2. สร้างคลาส CustomEncrypter
$encryptionDir = __DIR__ . '/app/Encryption';
if (!is_dir($encryptionDir)) {
    mkdir($encryptionDir, 0755, true);
    echo "✅ สร้างไดเรกทอรี {$encryptionDir}\n";
}

$customEncrypterPath = $encryptionDir . '/CustomEncrypter.php';
$customEncrypterContent = <<<'EOT'
<?php

namespace App\Encryption;

/**
 * CustomEncrypter - เข้ารหัสและถอดรหัสข้อมูลด้วย OpenSSL โดยตรง
 */
class CustomEncrypter
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
     * Create a new encrypter instance.
     *
     * @param  string  $key
     * @param  string  $cipher
     * @return void
     */
    public function __construct($key = null, $cipher = 'aes-256-cbc')
    {
        // ถ้าไม่กำหนด key ให้ใช้จาก .env
        if ($key === null) {
            $key = $this->getKeyFromEnv();
        }
        
        $this->key = $key;
        $this->cipher = $cipher;
        
        // ตรวจสอบว่า key และ cipher ถูกต้อง
        $this->validateKey($key);
        $this->validateCipher($cipher);
    }

    /**
     * Encrypt the given value.
     *
     * @param  mixed  $value
     * @param  bool  $serialize
     * @return string
     */
    public function encrypt($value, $serialize = true)
    {
        try {
            // เข้ารหัสข้อมูลที่เป็น object หรือ array
            if ($serialize && (is_object($value) || is_array($value))) {
                $value = serialize($value);
            }
            
            // สร้าง IV
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher));
            
            // เข้ารหัสข้อมูล
            $encrypted = openssl_encrypt($value, $this->cipher, $this->key, 0, $iv);
            
            if ($encrypted === false) {
                throw new \Exception("OpenSSL encryption error: " . openssl_error_string());
            }
            
            // สร้าง MAC เพื่อตรวจสอบความถูกต้องของข้อมูล
            $mac = hash_hmac('sha256', $iv . $encrypted, $this->key);
            
            // รวมข้อมูล IV, encrypted data และ MAC
            $json = json_encode([
                'iv' => base64_encode($iv),
                'value' => $encrypted,
                'mac' => $mac,
            ], JSON_UNESCAPED_SLASHES);
            
            return base64_encode($json);
            
        } catch (\Exception $e) {
            // บันทึก log สำหรับ debugging
            error_log('Encryption error: ' . $e->getMessage());
            error_log('Data type: ' . gettype($value));
            error_log('Data preview: ' . (is_string($value) ? substr($value, 0, 50) : 'non-string'));
            
            throw new \RuntimeException('Could not encrypt the data: ' . $e->getMessage());
        }
    }

    /**
     * Decrypt the given value.
     *
     * @param  string  $payload
     * @param  bool  $unserialize
     * @return mixed
     */
    public function decrypt($payload, $unserialize = true)
    {
        if (is_null($payload) || !is_string($payload)) {
            return null;
        }

        try {
            $payload = $this->getJsonPayload($payload);
            
            $iv = base64_decode($payload['iv']);
            $decrypted = openssl_decrypt(
                $payload['value'], $this->cipher, $this->key, 0, $iv
            );
            
            if ($decrypted === false) {
                throw new \Exception("OpenSSL decryption error: " . openssl_error_string());
            }
            
            return $unserialize ? unserialize($decrypted) : $decrypted;
            
        } catch (\Exception $e) {
            error_log('Decryption error: ' . $e->getMessage());
            throw new \RuntimeException('Could not decrypt the data: ' . $e->getMessage());
        }
    }

    /**
     * Get the JSON payload from the encryption payload.
     *
     * @param  string  $payload
     * @return array
     */
    protected function getJsonPayload($payload)
    {
        $payload = json_decode(base64_decode($payload), true);
        
        if (! $this->validPayload($payload)) {
            throw new \RuntimeException('Invalid encryption payload.');
        }
        
        if (! $this->validMac($payload)) {
            throw new \RuntimeException('MAC verification failed.');
        }
        
        return $payload;
    }

    /**
     * Verify that the encryption payload is valid.
     *
     * @param  mixed  $payload
     * @return bool
     */
    protected function validPayload($payload)
    {
        return is_array($payload) && isset($payload['iv']) && isset($payload['value']) && isset($payload['mac']);
    }

    /**
     * Verify that the MAC for the given payload is valid.
     *
     * @param  array  $payload
     * @return bool
     */
    protected function validMac(array $payload)
    {
        $iv = base64_decode($payload['iv']);
        $calculated = hash_hmac('sha256', $iv . $payload['value'], $this->key);
        
        return hash_equals($calculated, $payload['mac']);
    }

    /**
     * ดึง key จากไฟล์ .env
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
            return base64_decode($matches[1]);
        }
        
        throw new \RuntimeException('ไม่พบ APP_KEY ในไฟล์ .env');
    }

    /**
     * Validate the given encryption key.
     *
     * @param  string  $key
     * @return void
     */
    protected function validateKey($key)
    {
        if (!is_string($key)) {
            throw new \RuntimeException('Encryption key must be a string.');
        }
        
        // ตรวจสอบความยาวของ key สำหรับ AES
        if ($this->cipher === 'aes-128-cbc' && strlen($key) !== 16) {
            throw new \RuntimeException('AES-128-CBC encryption key must be 16 bytes long.');
        }
        
        if ($this->cipher === 'aes-256-cbc' && strlen($key) !== 32) {
            throw new \RuntimeException('AES-256-CBC encryption key must be 32 bytes long.');
        }
    }

    /**
     * Validate the given cipher.
     *
     * @param  string  $cipher
     * @return void
     */
    protected function validateCipher($cipher)
    {
        if (!in_array($cipher, openssl_get_cipher_methods())) {
            throw new \RuntimeException('Encryption cipher "' . $cipher . '" is not supported.');
        }
    }

    /**
     * Get the encryption key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Methods for compatibility with Laravel's Encrypter
     */
    public function encryptString($value)
    {
        return $this->encrypt($value, false);
    }
    
    public function decryptString($payload)
    {
        return $this->decrypt($payload, false);
    }
    
    public function getAllKeys()
    {
        return ['current' => $this->key];
    }
    
    public function getPreviousKeys()
    {
        return [];
    }
}
EOT;

file_put_contents($customEncrypterPath, $customEncrypterContent);
echo "✅ สร้างคลาส CustomEncrypter เรียบร้อยแล้ว\n";

// 3. สร้าง Encryption Service Provider
$serviceProviderDir = __DIR__ . '/app/Providers';
if (!is_dir($serviceProviderDir)) {
    mkdir($serviceProviderDir, 0755, true);
    echo "✅ สร้างไดเรกทอรี {$serviceProviderDir}\n";
}

$serviceProviderPath = $serviceProviderDir . '/CustomEncryptionServiceProvider.php';
$serviceProviderContent = <<<'EOT'
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Encryption\CustomEncrypter;

class CustomEncryptionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // แทนที่ encrypter service ด้วย CustomEncrypter
        $this->app->singleton('encrypter', function ($app) {
            return new CustomEncrypter();
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
echo "✅ สร้าง CustomEncryptionServiceProvider เรียบร้อยแล้ว\n";

// 4. ลงทะเบียน Service Provider ใน config/app.php
$configAppPath = __DIR__ . '/config/app.php';
if (file_exists($configAppPath)) {
    copy($configAppPath, $backupDir . '/app.php');
    echo "✅ สำรองไฟล์ config/app.php แล้ว\n";
    
    $configAppContent = file_get_contents($configAppPath);
    
    // ตรวจสอบว่ามี Encryption Provider หรือไม่
    if (strpos($configAppContent, 'Illuminate\Encryption\EncryptionServiceProvider') !== false) {
        // แทนที่ Provider เดิม
        $configAppContent = str_replace(
            'Illuminate\Encryption\EncryptionServiceProvider::class',
            'App\Providers\CustomEncryptionServiceProvider::class',
            $configAppContent
        );
        
        file_put_contents($configAppPath, $configAppContent);
        echo "✅ แทนที่ EncryptionServiceProvider ด้วย CustomEncryptionServiceProvider แล้ว\n";
    } else {
        // ตรวจสอบว่ามี providers array และเพิ่ม provider ใหม่
        if (preg_match("/'providers'\s*=>\s*\[(.*?)\]/s", $configAppContent, $matches)) {
            $providers = $matches[1];
            $newProviders = $providers . "\n        App\Providers\CustomEncryptionServiceProvider::class,";
            $configAppContent = str_replace($providers, $newProviders, $configAppContent);
            
            file_put_contents($configAppPath, $configAppContent);
            echo "✅ เพิ่ม CustomEncryptionServiceProvider ใน config/app.php แล้ว\n";
        } else {
            echo "⚠️ ไม่สามารถหา providers array ใน config/app.php\n";
        }
    }
} else {
    echo "⚠️ ไม่พบไฟล์ config/app.php\n";
}

// 5. สร้างไฟล์ทดสอบ
$testEncryptionFilePath = __DIR__ . '/test-custom-encryption.php';
$testEncryptionContent = <<<'EOT'
<?php

/**
 * ทดสอบการเข้ารหัสและถอดรหัสด้วย CustomEncrypter
 */

require __DIR__ . '/vendor/autoload.php';

use App\Encryption\CustomEncrypter;

echo "===== Testing CustomEncrypter =====\n\n";

// 1. ทดสอบโดยตรงกับ CustomEncrypter class
try {
    echo "1. ทดสอบ CustomEncrypter โดยตรง:\n";
    
    // สร้าง instance
    $encrypter = new CustomEncrypter();
    echo "   ✅ สร้าง CustomEncrypter สำเร็จ\n";
    
    // ทดสอบเข้ารหัส/ถอดรหัสข้อความ
    $text = "ข้อความทดสอบการเข้ารหัส";
    echo "   ข้อความต้นฉบับ: {$text}\n";
    
    $encrypted = $encrypter->encrypt($text);
    echo "   ข้อความที่เข้ารหัสแล้ว: {$encrypted}\n";
    
    $decrypted = $encrypter->decrypt($encrypted);
    echo "   ข้อความหลังถอดรหัส: {$decrypted}\n";
    
    if ($text === $decrypted) {
        echo "   ✅ ผลการทดสอบ: สำเร็จ - ข้อความตรงกัน\n";
    } else {
        echo "   ❌ ผลการทดสอบ: ล้มเหลว - ข้อความไม่ตรงกัน\n";
    }

    // ทดสอบเข้ารหัส/ถอดรหัส array
    echo "\n   ทดสอบกับข้อมูลเป็น array:\n";
    $array = ['id' => 1, 'name' => 'ทดสอบ'];
    
    $encrypted = $encrypter->encrypt($array);
    echo "   Array ที่เข้ารหัสแล้ว: " . substr($encrypted, 0, 30) . "...\n";
    
    $decrypted = $encrypter->decrypt($encrypted);
    
    if (is_array($decrypted) && $decrypted['id'] === 1 && $decrypted['name'] === 'ทดสอบ') {
        echo "   ✅ ผลการทดสอบ: สำเร็จ - Array ตรงกัน\n";
    } else {
        echo "   ❌ ผลการทดสอบ: ล้มเหลว - Array ไม่ตรงกัน\n";
        var_dump($decrypted);
    }
    
} catch (Exception $e) {
    echo "   ❌ เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
    echo "   " . $e->getTraceAsString() . "\n";
}

// 2. สร้างฟังก์ชันทดแทนของ Laravel
echo "\n2. สร้างฟังก์ชันทดแทน encrypt() และ decrypt():\n";

if (!function_exists('custom_encrypt')) {
    function custom_encrypt($value, $serialize = true) {
        static $encrypter = null;
        
        if ($encrypter === null) {
            $encrypter = new CustomEncrypter();
        }
        
        return $encrypter->encrypt($value, $serialize);
    }
}

if (!function_exists('custom_decrypt')) {
    function custom_decrypt($payload, $unserialize = true) {
        static $encrypter = null;
        
        if ($encrypter === null) {
            $encrypter = new CustomEncrypter();
        }
        
        return $encrypter->decrypt($payload, $unserialize);
    }
}

// ทดสอบฟังก์ชันทดแทน
try {
    $text = "ข้อความทดสอบฟังก์ชัน custom_encrypt";
    
    $encrypted = custom_encrypt($text);
    echo "   ข้อความที่เข้ารหัสแล้ว: {$encrypted}\n";
    
    $decrypted = custom_decrypt($encrypted);
    echo "   ข้อความหลังถอดรหัส: {$decrypted}\n";
    
    if ($text === $decrypted) {
        echo "   ✅ ผลการทดสอบ: สำเร็จ - ข้อความตรงกัน\n";
    } else {
        echo "   ❌ ผลการทดสอบ: ล้มเหลว - ข้อความไม่ตรงกัน\n";
    }
} catch (Exception $e) {
    echo "   ❌ เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
}

echo "\n===== ข้อแนะนำในการใช้งาน =====\n";
echo "1. แทนที่ encrypt() ด้วย custom_encrypt() ในโค้ดของคุณ\n";
echo "2. แทนที่ decrypt() ด้วย custom_decrypt() ในโค้ดของคุณ\n";
echo "หรือเพิ่ม CustomEncryptionServiceProvider ใน config/app.php เพื่อแทนที่ encrypter ของ Laravel\n";
EOT;

file_put_contents($testEncryptionFilePath, $testEncryptionContent);
echo "✅ สร้างไฟล์ทดสอบ test-custom-encryption.php แล้ว\n";

// 6. สร้างไฟล์ helper.php สำหรับแทนที่ encrypt() และ decrypt()
$helperDir = __DIR__ . '/app';
$helperPath = $helperDir . '/helpers.php';

if (file_exists($helperPath)) {
    copy($helperPath, $backupDir . '/helpers.php');
    echo "✅ สำรองไฟล์ app/helpers.php แล้ว\n";
}

$helperContent = <<<'EOT'
<?php

use App\Encryption\CustomEncrypter;

/**
 * Helper functions แทนที่ encrypt() และ decrypt() ของ Laravel
 * เพื่อแก้ปัญหา "Could not encrypt the data"
 */

if (!function_exists('encrypt')) {
    /**
     * Encrypt the given value.
     *
     * @param  mixed  $value
     * @param  bool  $serialize
     * @return string
     */
    function encrypt($value, $serialize = true)
    {
        static $encrypter = null;
        
        if ($encrypter === null) {
            $encrypter = new CustomEncrypter();
        }
        
        return $encrypter->encrypt($value, $serialize);
    }
}

if (!function_exists('decrypt')) {
    /**
     * Decrypt the given value.
     *
     * @param  string  $value
     * @param  bool  $unserialize
     * @return mixed
     */
    function decrypt($value, $unserialize = true)
    {
        static $encrypter = null;
        
        if ($encrypter === null) {
            $encrypter = new CustomEncrypter();
        }
        
        return $encrypter->decrypt($value, $unserialize);
    }
}
EOT;

file_put_contents($helperPath, $helperContent);
echo "✅ สร้าง/แก้ไขไฟล์ app/helpers.php แล้ว\n";

// 7. อัปเดต composer.json เพื่อให้โหลด helpers.php
$composerJsonPath = __DIR__ . '/composer.json';
if (file_exists($composerJsonPath)) {
    copy($composerJsonPath, $backupDir . '/composer.json');
    echo "✅ สำรองไฟล์ composer.json แล้ว\n";
    
    $composerJsonContent = file_get_contents($composerJsonPath);
    $composerJson = json_decode($composerJsonContent, true);
    
    if (is_array($composerJson)) {
        if (!isset($composerJson['autoload']['files'])) {
            $composerJson['autoload']['files'] = [];
        }
        
        if (!in_array('app/helpers.php', $composerJson['autoload']['files'])) {
            $composerJson['autoload']['files'][] = 'app/helpers.php';
            
            file_put_contents(
                $composerJsonPath, 
                json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );
            echo "✅ อัปเดต composer.json เพื่อโหลด app/helpers.php แล้ว\n";
            
            echo "\nกำลังรัน composer dump-autoload เพื่ออัปเดตการโหลด app/helpers.php\n";
            passthru('composer dump-autoload');
        } else {
            echo "ℹ️ ไฟล์ app/helpers.php ถูกกำหนดให้โหลดใน composer.json อยู่แล้ว\n";
        }
    } else {
        echo "⚠️ ไม่สามารถอ่าน composer.json เป็น JSON ได้\n";
    }
} else {
    echo "⚠️ ไม่พบไฟล์ composer.json\n";
}

// 8. เคลียร์ cache
echo "\nกำลังเคลียร์ cache...\n";

try {
    echo "\nรันคำสั่ง php artisan config:clear\n";
    passthru('php artisan config:clear');
    
    echo "\nรันคำสั่ง php artisan cache:clear\n";
    passthru('php artisan cache:clear');
    
    echo "\nรันคำสั่ง php artisan view:clear\n";
    passthru('php artisan view:clear');
} catch (Exception $e) {
    echo "⚠️ เกิดข้อผิดพลาดในการเคลียร์ cache: " . $e->getMessage() . "\n";
}

echo "\n===== การติดตั้งและแก้ไขเสร็จสมบูรณ์ =====\n\n";
echo "คุณสามารถทดสอบการเข้ารหัส/ถอดรหัสด้วย CustomEncrypter ได้โดย:\n";
echo "php test-custom-encryption.php\n\n";
echo "วิธีแก้ปัญหา:\n";
echo "1. คลาส CustomEncrypter จะถูกใช้แทนที่ Laravel Encrypter โดยอัตโนมัติ\n";
echo "2. ถ้ายังมีปัญหา ให้แก้ไขโค้ดเดิมโดยแทนที่ encrypt() ด้วย custom_encrypt()\n";
echo "3. รันเซิร์ฟเวอร์เพื่อทดสอบ:\n";
echo "   php artisan serve --port=8050\n";
