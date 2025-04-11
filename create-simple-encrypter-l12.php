<?php

/**
 * สร้าง SimpleEncrypter สำหรับใช้กับ Laravel 12
 * โดยไม่กระทบกับไฟล์อื่นๆ
 */

echo "===== Creating Simple Encrypter for Laravel 12 =====\n\n";

// 1. สร้างโฟลเดอร์ที่จำเป็น
$encryptionDir = __DIR__ . '/app/Encryption';
if (!is_dir($encryptionDir)) {
    mkdir($encryptionDir, 0755, true);
    echo "✅ สร้างโฟลเดอร์ app/Encryption\n";
}

// 2. สร้างคลาส SimpleEncrypter
$simpleEncrypterPath = $encryptionDir . '/SimpleEncrypter.php';
$simpleEncrypterContent = <<<'EOT'
<?php

namespace App\Encryption;

/**
 * SimpleEncrypter - ใช้ OpenSSL โดยตรงเพื่อแก้ไขปัญหาการเข้ารหัสใน Laravel
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
     * สร้าง instance ของ SimpleEncrypter
     *
     * @param string|null $key
     * @param string $cipher
     * @throws \RuntimeException
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
        if ($serialize) {
            $value = serialize($value);
        }
        
        $iv = random_bytes(openssl_cipher_iv_length($this->cipher));
        
        $value = openssl_encrypt(
            $value, $this->cipher, $this->key, 0, $iv
        );
        
        if ($value === false) {
            throw new \RuntimeException('Could not encrypt the data.');
        }
        
        $mac = hash_hmac('sha256', $value, $this->key);
        
        $json = json_encode([
            'iv' => base64_encode($iv),
            'value' => $value,
            'mac' => $mac,
        ]);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Could not format encrypted data for JSON encoding');
        }
        
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
        
        $iv = base64_decode($payload['iv']);
        
        $decrypted = openssl_decrypt(
            $payload['value'], $this->cipher, $this->key, 0, $iv
        );
        
        if ($decrypted === false) {
            throw new \RuntimeException('Could not decrypt the data.');
        }
        
        return $unserialize ? unserialize($decrypted) : $decrypted;
    }

    /**
     * อ่านและแปลง JSON payload
     *
     * @param string $payload
     * @return array
     * @throws \RuntimeException
     */
    protected function getJsonPayload($payload)
    {
        $payload = json_decode(base64_decode($payload), true);
        
        if (!$payload || !isset($payload['iv']) || !isset($payload['value']) || !isset($payload['mac'])) {
            throw new \RuntimeException('Invalid data.');
        }
        
        if (!hash_equals($payload['mac'], hash_hmac('sha256', $payload['value'], $this->key))) {
            throw new \RuntimeException('Invalid MAC value, data might be tampered.');
        }
        
        return $payload;
    }

    /**
     * ดึง key จากไฟล์ .env
     *
     * @return string
     * @throws \RuntimeException
     */
    protected function getKeyFromEnv()
    {
        $path = __DIR__ . '/../../.env';
        
        if (!file_exists($path)) {
            throw new \RuntimeException('The .env file does not exist.');
        }
        
        $content = file_get_contents($path);
        
        if (preg_match('/APP_KEY=base64:([^\s\n]+)/', $content, $matches)) {
            $key = base64_decode($matches[1]);
            
            if (strlen($key) !== 32) {
                throw new \RuntimeException('APP_KEY in .env has incorrect length.');
            }
            
            return $key;
        }
        
        throw new \RuntimeException('APP_KEY not found in .env file.');
    }

    /**
     * ตรวจสอบความถูกต้องของ key และ cipher
     *
     * @return void
     * @throws \RuntimeException
     */
    protected function validateKeyAndCipher()
    {
        if (!in_array($this->cipher, openssl_get_cipher_methods())) {
            throw new \RuntimeException("Unsupported cipher: {$this->cipher}");
        }
        
        if (strlen($this->key) !== 32 && $this->cipher === 'aes-256-cbc') {
            throw new \RuntimeException('Key length must be 32 bytes for AES-256-CBC.');
        }
        
        if (strlen($this->key) !== 16 && $this->cipher === 'aes-128-cbc') {
            throw new \RuntimeException('Key length must be 16 bytes for AES-128-CBC.');
        }
    }

    /**
     * Get encryption key
     * 
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Get all encryption keys (ใช้เพื่อความเข้ากันได้กับ Laravel)
     *
     * @return array
     */
    public function getAllKeys()
    {
        return ['current' => $this->key];
    }
    
    /**
     * Get previous encryption keys (ใช้เพื่อความเข้ากันได้กับ Laravel)
     *
     * @return array
     */
    public function getPreviousKeys()
    {
        return [];
    }
    
    /**
     * เข้ารหัสสตริง
     *
     * @param string $value
     * @return string
     */
    public function encryptString($value)
    {
        return $this->encrypt($value, false);
    }
    
    /**
     * ถอดรหัสสตริง
     *
     * @param string $payload
     * @return string
     */
    public function decryptString($payload)
    {
        return $this->decrypt($payload, false);
    }
}
EOT;

file_put_contents($simpleEncrypterPath, $simpleEncrypterContent);
echo "✅ สร้างคลาส SimpleEncrypter เรียบร้อยแล้ว\n";

// 3. สร้างไฟล์ทดสอบ
$testFilePath = __DIR__ . '/test-encrypter.php';
$testFileContent = <<<'EOT'
<?php

require __DIR__ . '/vendor/autoload.php';

use App\Encryption\SimpleEncrypter;

echo "===== ทดสอบการเข้ารหัส =====\n\n";

try {
    // สร้าง SimpleEncrypter
    echo "1. กำลังสร้าง SimpleEncrypter...\n";
    $encrypter = new SimpleEncrypter();
    echo "✅ สร้าง SimpleEncrypter สำเร็จ\n\n";
    
    // ทดสอบเข้ารหัสข้อความ
    echo "2. ทดสอบเข้ารหัสข้อความ\n";
    $text = "ข้อความทดสอบการเข้ารหัส";
    echo "ข้อความต้นฉบับ: {$text}\n";
    
    $encrypted = $encrypter->encrypt($text);
    echo "ข้อความที่เข้ารหัสแล้ว: {$encrypted}\n\n";
    
    // ทดสอบถอดรหัสข้อความ
    echo "3. ทดสอบถอดรหัสข้อความ\n";
    $decrypted = $encrypter->decrypt($encrypted);
    echo "ข้อความที่ถอดรหัสแล้ว: {$decrypted}\n";
    
    if ($text === $decrypted) {
        echo "✅ ทดสอบสำเร็จ: ข้อความตรงกัน\n\n";
    } else {
        echo "❌ ทดสอบล้มเหลว: ข้อความไม่ตรงกัน\n\n";
    }
    
    // ทดสอบเข้ารหัส array
    echo "4. ทดสอบเข้ารหัส Array\n";
    $array = ['id' => 1, 'name' => 'ทดสอบ', 'active' => true];
    echo "Array ต้นฉบับ: " . json_encode($array, JSON_UNESCAPED_UNICODE) . "\n";
    
    $encrypted = $encrypter->encrypt($array);
    echo "Array ที่เข้ารหัสแล้ว: " . substr($encrypted, 0, 32) . "...\n";
    
    $decrypted = $encrypter->decrypt($encrypted);
    echo "Array ที่ถอดรหัสแล้ว: " . json_encode($decrypted, JSON_UNESCAPED_UNICODE) . "\n";
    
    if ($array === $decrypted) {
        echo "✅ ทดสอบสำเร็จ: Array ตรงกัน\n";
    } else {
        echo "❌ ทดสอบล้มเหลว: Array ไม่ตรงกัน\n";
    }
    
} catch (Exception $e) {
    echo "❌ เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
EOT;

file_put_contents($testFilePath, $testFileContent);
echo "✅ สร้างไฟล์ test-encrypter.php เรียบร้อยแล้ว\n";

echo "\n===== คำแนะนำ =====\n";
echo "1. ทดสอบ SimpleEncrypter:\n";
echo "   php test-encrypter.php\n\n";
echo "2. ตรวจสอบว่าแก้ไขไฟล์ bootstrap/app.php แล้วหรือยัง:\n";
echo "   php fix-bootstrap-laravel12.php\n\n";
echo "3. รันเซิร์ฟเวอร์:\n";
echo "   php artisan serve --port=8009\n";
