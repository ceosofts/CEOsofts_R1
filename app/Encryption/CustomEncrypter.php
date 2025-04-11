<?php

namespace App\Encryption;

use Illuminate\Contracts\Encryption\Encrypter;

/**
 * CustomEncrypter - เข้ารหัสและถอดรหัสข้อมูลด้วย OpenSSL โดยตรง
 */
class CustomEncrypter implements Encrypter
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