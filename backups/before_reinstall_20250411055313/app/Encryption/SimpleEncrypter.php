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