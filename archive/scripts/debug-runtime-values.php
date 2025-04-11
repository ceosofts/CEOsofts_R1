<?php

/**
 * สคริปต์ตรวจสอบค่าต่างๆ ที่ใช้ในระบบ Encryption ขณะ runtime
 */

echo "Laravel Encryption Debug Tool\n";
echo "=========================\n\n";

// ไม่ใช้ Laravel's config system แต่อ่านค่าโดยตรงจากไฟล์เพื่อตรวจสอบ
echo "A. ตรวจสอบค่าจากไฟล์โดยตรง (ไม่ผ่าน Laravel)\n";
echo "-----------------------------------\n";

// ตรวจสอบ .env
if (file_exists(__DIR__ . '/.env')) {
    $envContent = file_get_contents(__DIR__ . '/.env');
    if (preg_match('/APP_KEY=([^\n]+)/', $envContent, $matches)) {
        $envKey = $matches[1];
        echo "APP_KEY จาก .env: " . $envKey . "\n";
        
        if (strpos($envKey, 'base64:') === 0) {
            $decodedKey = base64_decode(substr($envKey, 7));
            $keyLength = strlen($decodedKey);
            echo "ความยาวของ key หลัง decode: $keyLength ไบต์\n";
            
            // ตรวจสอบว่าคีย์มีอักขระพิเศษหรือเป็น binary ที่อาจมีปัญหาหรือไม่
            $safeChars = true;
            for ($i = 0; $i < $keyLength; $i++) {
                $byte = ord($decodedKey[$i]);
                if ($byte < 32 || $byte > 126) {
                    $safeChars = false;
                    break;
                }
            }
            echo "คีย์ประกอบด้วยอักขระพิมพ์ได้ทั้งหมด: " . ($safeChars ? "ใช่" : "ไม่ใช่ (มี binary data)") . "\n";
            
            // แสดง key ในรูปแบบ hex เพื่อตรวจสอบ
            echo "คีย์ในรูปแบบ hex: " . bin2hex($decodedKey) . "\n";
        }
    }
}

// ตรวจสอบ config/app.php
echo "\nค่าจาก config/app.php:\n";
if (file_exists(__DIR__ . '/config/app.php')) {
    $appConfig = include __DIR__ . '/config/app.php';
    echo "cipher: " . ($appConfig['cipher'] ?? 'ไม่พบค่า cipher') . "\n";
    
    // ตรวจสอบค่า key ที่อาจระบุไว้ใน config โดยตรง (แทนที่จะดึงจาก env)
    echo "key: " . (isset($appConfig['key']) ? substr($appConfig['key'], 0, 10) . '...' : 'ไม่ได้ระบุในไฟล์ (ใช้ค่าจาก env)') . "\n";
}

// พยายามโหลด vendor/laravel และตรวจสอบ classes ที่เกี่ยวข้อง
echo "\nB. ตรวจสอบ Laravel Classes โดยตรง\n";
echo "---------------------------\n";

// โหลด autoloader
require __DIR__ . '/vendor/autoload.php';

// ตรวจสอบไฟล์ Encrypter class
$encrypterPath = __DIR__ . '/vendor/laravel/framework/src/Illuminate/Encryption/Encrypter.php';
if (file_exists($encrypterPath)) {
    echo "พบไฟล์ Encrypter.php\n";
    
    // อ่านเนื้อหาไฟล์และตรวจสอบ supportedCiphers
    $encrypterContent = file_get_contents($encrypterPath);
    if (preg_match('/private\s+static\s+\$supportedCiphers\s*=\s*\[(.*?)\]/s', $encrypterContent, $matches)) {
        echo "supportedCiphers ใน Encrypter.php: \n";
        $supportedCiphers = $matches[1];
        $lines = explode("\n", $supportedCiphers);
        foreach ($lines as $line) {
            if (trim($line) && strpos($line, '//') !== 0) {
                echo "  " . trim($line) . "\n";
            }
        }
    }
    
    // ตรวจสอบเมธอด supported()
    if (preg_match('/public\s+static\s+function\s+supported\(\$key,\s*\$cipher\)\s*{(.*?)}/s', $encrypterContent, $matches)) {
        echo "\nเมธอด supported() ใน Encrypter.php: \n";
        $supportedMethod = $matches[1];
        $lines = explode("\n", $supportedMethod);
        foreach ($lines as $line) {
            if (trim($line)) {
                echo "  " . trim($line) . "\n";
            }
        }
    }
} else {
    echo "❌ ไม่พบไฟล์ Encrypter.php ที่ $encrypterPath\n";
}

// ลองสร้าง Encrypter instance โดยตรง
echo "\nC. ทดสอบสร้าง Encrypter instance โดยตรง\n";
echo "--------------------------------\n";

try {
    // ดึงค่า key จาก .env มาใช้
    if (file_exists(__DIR__ . '/.env')) {
        $envContent = file_get_contents(__DIR__ . '/.env');
        if (preg_match('/APP_KEY=base64:([^\n]+)/', $envContent, $matches)) {
            $keyBase64 = $matches[1];
            $key = base64_decode($keyBase64);
            
            echo "กำลังสร้าง Encrypter ด้วย key จาก .env และ cipher='aes-256-cbc'...\n";
            
            // ตรวจสอบว่ามีคลาส Illuminate\Encryption\Encrypter หรือไม่
            if (class_exists('Illuminate\Encryption\Encrypter')) {
                $encrypter = new \Illuminate\Encryption\Encrypter($key, 'aes-256-cbc');
                echo "✅ สร้าง Encrypter instance สำเร็จ!\n";
                
                // ทดลองเข้ารหัสและถอดรหัสข้อความ
                $encrypted = $encrypter->encrypt("test");
                $decrypted = $encrypter->decrypt($encrypted);
                echo "ทดสอบเข้ารหัสและถอดรหัส: " . ($decrypted === "test" ? "สำเร็จ ✓" : "ไม่สำเร็จ ✗") . "\n";
            } else {
                echo "❌ ไม่พบคลาส Illuminate\\Encryption\\Encrypter\n";
            }
        }
    }
} catch (\Exception $e) {
    echo "❌ เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

// พยายามเข้าถึง encrypter ผ่าน Laravel container (ถ้ามีการโหลด app)
echo "\nD. ทดสอบเข้าถึง Encrypter ผ่าน Laravel Container\n";
echo "----------------------------------------\n";

try {
    // ถ้ามีไฟล์ bootstrap/app.php ให้ลองโหลด
    if (file_exists(__DIR__ . '/bootstrap/app.php')) {
        $app = require_once __DIR__ . '/bootstrap/app.php';
        
        if (method_exists($app, 'bootstrapWith')) {
            $app->bootstrapWith([
                \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
                \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
            ]);
            
            echo "ค่า config('app.key'): " . ($app['config']['app.key'] ?? 'ไม่พบ') . "\n";
            echo "ค่า config('app.cipher'): " . ($app['config']['app.cipher'] ?? 'ไม่พบ') . "\n";
            
            // ตรวจสอบว่า encrypter ถูกลงทะเบียนในหรือไม่
            echo "Services ที่ลงทะเบียน: " . (isset($app['encrypter']) ? 'มี encrypter' : 'ไม่มี encrypter') . "\n";
            
            if (isset($app['encrypter'])) {
                $encrypter = $app['encrypter'];
                $result = is_object($encrypter) ? get_class($encrypter) : gettype($encrypter);
                echo "ประเภทของ encrypter: $result\n";
            }
        } else {
            echo "❌ ไม่สามารถ bootstrap application ได้\n";
        }
    } else {
        echo "❌ ไม่พบไฟล์ bootstrap/app.php\n";
    }
} catch (\Exception $e) {
    echo "❌ เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
}

// ตรวจสอบ PHP extensions
echo "\nE. ตรวจสอบ PHP Extensions\n";
echo "--------------------\n";
echo "OpenSSL: " . (extension_loaded('openssl') ? "เปิดใช้งาน ✓" : "ไม่ได้เปิดใช้งาน ✗") . "\n";
echo "OpenSSL version: " . (defined('OPENSSL_VERSION_TEXT') ? OPENSSL_VERSION_TEXT : "ไม่มีข้อมูล") . "\n";

// แสดงวิธี cipher ทั้งหมดที่ OpenSSL รองรับ
$methods = openssl_get_cipher_methods();
echo "จำนวน cipher ที่ OpenSSL รองรับ: " . count($methods) . "\n";
echo "รองรับ aes-256-cbc: " . (in_array('aes-256-cbc', $methods) ? "ใช่ ✓" : "ไม่ใช่ ✗") . "\n";
