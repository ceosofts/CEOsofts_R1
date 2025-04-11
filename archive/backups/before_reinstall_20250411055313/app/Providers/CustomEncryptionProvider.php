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