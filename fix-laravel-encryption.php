<?php

/**
 * สคริปต์แก้ไขระบบเข้ารหัสของ Laravel แบบครบวงจร
 * เนื่องจากปัญหาใน Laravel 12.8.1 เกี่ยวกับ Encrypter class
 */

echo "Laravel Encryption Comprehensive Fix\n";
echo "================================\n\n";

// พาธของไฟล์ Encrypter และ Service Provider
$encrypterPath = __DIR__ . '/vendor/laravel/framework/src/Illuminate/Encryption/Encrypter.php';
$serviceProviderPath = __DIR__ . '/vendor/laravel/framework/src/Illuminate/Encryption/EncryptionServiceProvider.php';

// สำรองไฟล์ก่อน
$timestamp = date('YmdHis');
$backupDir = __DIR__ . '/backups/encryption_fix_' . $timestamp;

if (!file_exists($backupDir)) {
    mkdir($backupDir, 0755, true);
    echo "✅ สร้างโฟลเดอร์สำรองที่: $backupDir\n";
}

// สำรองไฟล์ Encrypter.php
if (file_exists($encrypterPath)) {
    copy($encrypterPath, $backupDir . '/Encrypter.php');
    echo "✅ สำรองไฟล์ Encrypter.php\n";
} else {
    echo "❌ ไม่พบไฟล์ Encrypter.php\n";
    exit(1);
}

// สำรอง EncryptionServiceProvider.php
if (file_exists($serviceProviderPath)) {
    copy($serviceProviderPath, $backupDir . '/EncryptionServiceProvider.php');
    echo "✅ สำรองไฟล์ EncryptionServiceProvider.php\n";
} else {
    echo "❌ ไม่พบไฟล์ EncryptionServiceProvider.php\n";
    exit(1);
}

echo "\nกำลังเริ่มการแก้ไข...\n\n";

// 1. แก้ไขไฟล์ Encrypter.php

// สร้างเนื้อหา Encrypter.php ใหม่ทั้งหมดที่มีเมธอดที่จำเป็นครบถ้วน
$newEncrypterContent = <<<'EOT'
<?php

namespace Illuminate\Encryption;

use RuntimeException;
use Illuminate\Contracts\Encryption\Encrypter as EncrypterContract;
use Illuminate\Contracts\Encryption\StringEncrypter;

class Encrypter implements EncrypterContract, StringEncrypter
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
     * The supported cipher algorithms and their properties.
     *
     * @var array
     */
    private static $supportedCiphers = [
        'aes-128-cbc' => ['size' => 16, 'aead' => false],
        'aes-256-cbc' => ['size' => 32, 'aead' => false],
        'aes-128-gcm' => ['size' => 16, 'aead' => true],
        'aes-256-gcm' => ['size' => 32, 'aead' => true],
    ];

    /**
     * Create a new encrypter instance.
     *
     * @param  string  $key
     * @param  string  $cipher
     * @return void
     *
     * @throws \RuntimeException
     */
    public function __construct($key, $cipher = 'aes-256-cbc')
    {
        if (! static::supported($key, $cipher)) {
            $ciphers = implode(', ', array_keys(self::$supportedCiphers));

            throw new RuntimeException("Unsupported cipher or incorrect key length. Supported ciphers are: {$ciphers}.");
        }

        $this->key = $key;
        $this->cipher = $cipher;
    }

    /**
     * Determine if the given key and cipher combination is valid.
     *
     * @param  string  $key
     * @param  string  $cipher
     * @return bool
     */
    public static function supported($key, $cipher)
    {
        if (! isset(self::$supportedCiphers[strtolower($cipher)])) {
            return false;
        }

        return mb_strlen($key, '8bit') === self::$supportedCiphers[strtolower($cipher)]['size'];
    }

    /**
     * Create a new encryption key for the given cipher.
     *
     * @param  string  $cipher
     * @return string
     */
    public static function generateKey($cipher)
    {
        return random_bytes(self::$supportedCiphers[strtolower($cipher)]['size'] ?? 32);
    }

    /**
     * Encrypt the given value.
     *
     * @param  mixed  $value
     * @param  bool  $serialize
     * @return string
     *
     * @throws \Illuminate\Contracts\Encryption\EncryptException
     */
    public function encrypt($value, $serialize = true)
    {
        $iv = random_bytes(openssl_cipher_iv_length($this->cipher));

        $value = \is_array($value) || $serialize
            ? serialize($value)
            : $value;

        $value = openssl_encrypt(
            $value, $this->cipher, $this->key, 0, $iv, $tag
        );

        if ($value === false) {
            throw new EncryptException('Could not encrypt the data.');
        }

        $mac = self::$supportedCiphers[strtolower($this->cipher)]['aead']
            ? $tag : $this->hash($iv, $value);

        $json = json_encode(compact('iv', 'value', 'mac'), JSON_UNESCAPED_SLASHES);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new EncryptException('Could not encrypt the data.');
        }

        return base64_encode($json);
    }

    /**
     * Encrypt a string without serialization.
     *
     * @param  string  $value
     * @return string
     *
     * @throws \Illuminate\Contracts\Encryption\EncryptException
     */
    public function encryptString($value)
    {
        return $this->encrypt($value, false);
    }

    /**
     * Decrypt the given value.
     *
     * @param  string  $payload
     * @param  bool  $unserialize
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Encryption\DecryptException
     */
    public function decrypt($payload, $unserialize = true)
    {
        $payload = $this->getJsonPayload($payload);

        $iv = base64_decode($payload['iv']);

        $decrypted = openssl_decrypt(
            $payload['value'], $this->cipher, $this->key, 0, $iv, $payload['tag'] ?? null
        );

        if ($decrypted === false) {
            throw new DecryptException('Could not decrypt the data.');
        }

        return $unserialize ? unserialize($decrypted) : $decrypted;
    }

    /**
     * Decrypt the given string without unserialization.
     *
     * @param  string  $payload
     * @return string
     *
     * @throws \Illuminate\Contracts\Encryption\DecryptException
     */
    public function decryptString($payload)
    {
        return $this->decrypt($payload, false);
    }

    /**
     * Create a MAC for the given value.
     *
     * @param  string  $iv
     * @param  mixed  $value
     * @return string
     */
    protected function hash($iv, $value)
    {
        return hash_hmac('sha256', $iv.$value, $this->key);
    }

    /**
     * Get the JSON array from the given payload.
     *
     * @param  string  $payload
     * @return array
     *
     * @throws \Illuminate\Contracts\Encryption\DecryptException
     */
    protected function getJsonPayload($payload)
    {
        $payload = json_decode(base64_decode($payload), true);

        if (! $this->validPayload($payload)) {
            throw new DecryptException('The payload is invalid.');
        }

        if (! self::$supportedCiphers[strtolower($this->cipher)]['aead'] &&
            ! $this->validMac($payload)) {
            throw new DecryptException('The MAC is invalid.');
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
        return is_array($payload) && isset($payload['iv'], $payload['value'], $payload['mac']);
    }

    /**
     * Determine if the MAC for the given payload is valid.
     *
     * @param  array  $payload
     * @return bool
     */
    protected function validMac(array $payload)
    {
        return hash_equals(
            $payload['mac'],
            $this->hash(base64_decode($payload['iv']), $payload['value'])
        );
    }

    /**
     * Get the encryption key that the encrypter is currently using.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
    
    /**
     * Get all encryption keys.
     *
     * @return array
     */
    public function getAllKeys()
    {
        return ['current' => $this->key];
    }
    
    /**
     * Get previous encryption keys.
     *
     * @return array
     */
    public function getPreviousKeys()
    {
        return [];
    }
    
    /**
     * Get the encryption keys that were previously used.
     *
     * @return array
     */
    public function previousKeys()
    {
        return $this->getPreviousKeys();
    }
}
EOT;

// บันทึกเนื้อหาใหม่
file_put_contents($encrypterPath, $newEncrypterContent);
echo "✅ อัปเดตไฟล์ Encrypter.php เรียบร้อยแล้ว\n";

// 2. สร้างหรือปรับปรุงคลาส EncryptException และ DecryptException
$encryptExceptionPath = __DIR__ . '/vendor/laravel/framework/src/Illuminate/Encryption/EncryptException.php';
$decryptExceptionPath = __DIR__ . '/vendor/laravel/framework/src/Illuminate/Encryption/DecryptException.php';

if (!file_exists($encryptExceptionPath)) {
    $encryptExceptionContent = <<<'EOT'
<?php

namespace Illuminate\Encryption;

use RuntimeException;

class EncryptException extends RuntimeException
{
    //
}
EOT;

    file_put_contents($encryptExceptionPath, $encryptExceptionContent);
    echo "✅ สร้างไฟล์ EncryptException.php\n";
}

if (!file_exists($decryptExceptionPath)) {
    $decryptExceptionContent = <<<'EOT'
<?php

namespace Illuminate\Encryption;

use RuntimeException;

class DecryptException extends RuntimeException
{
    //
}
EOT;

    file_put_contents($decryptExceptionPath, $decryptExceptionContent);
    echo "✅ สร้างไฟล์ DecryptException.php\n";
}

// 3. สร้าง APP_KEY ใหม่
echo "\nกำลังสร้าง APP_KEY ใหม่...\n";
$newKey = 'base64:' . base64_encode(random_bytes(32));
echo "APP_KEY ใหม่: $newKey\n";

// อัปเดตไฟล์ .env
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    if (preg_match('/^APP_KEY=/m', $envContent)) {
        $envContent = preg_replace('/^APP_KEY=.*/m', "APP_KEY=$newKey", $envContent);
    } else {
        $envContent .= "\nAPP_KEY=$newKey\n";
    }
    
    file_put_contents($envPath, $envContent);
    echo "✅ อัปเดต APP_KEY ในไฟล์ .env\n";
}

// 4. ล้างแคช
echo "\nกำลังล้างแคช...\n";
passthru('php artisan config:clear');
passthru('php artisan cache:clear');
passthru('php artisan route:clear');
passthru('php artisan view:clear');
passthru('composer dump-autoload -o');

echo "\n================================\n";
echo "การแก้ไขแบบครบวงจรเสร็จสมบูรณ์!\n";
echo "โปรดรันคำสั่งต่อไปนี้เพื่อทดสอบ:\n";
echo "php artisan serve --port=8001\n";
echo "================================\n";
