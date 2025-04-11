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