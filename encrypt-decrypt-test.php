<?php

/**
 * ทดสอบการเข้ารหัสและถอดรหัสใน Laravel 
 * รวมทั้งแสดงข้อมูลวินิจฉัยสำหรับการแก้ไขปัญหา
 */

// โหลดไลบรารี Laravel
require __DIR__.'/vendor/autoload.php';

// ถ้า bootstrap/app.php มีอยู่ ให้โหลดมา
if (file_exists(__DIR__.'/bootstrap/app.php')) {
    echo "1. โหลด bootstrap/app.php สำเร็จ\n";
    $app = require_once __DIR__.'/bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    echo "2. Bootstrap Laravel application สำเร็จ\n";
} else {
    die("❌ ไม่พบไฟล์ bootstrap/app.php\n");
}

echo "\nการตรวจสอบระบบ Encryption\n";
echo "=======================\n";

// ตรวจสอบค่า key ใน config
$appKey = config('app.key');
$cipher = config('app.cipher');
echo "APP_KEY: " . ($appKey ? (substr($appKey, 0, 10) . '...') : 'ไม่พบค่า APP_KEY') . "\n";
echo "Cipher: " . ($cipher ?: 'ไม่พบค่า cipher') . "\n\n";

// ตรวจสอบไฟล์ .env
if (file_exists(__DIR__ . '/.env')) {
    $envContent = file_get_contents(__DIR__ . '/.env');
    if (preg_match('/APP_KEY=([^\n]+)/', $envContent, $matches)) {
        $envKey = $matches[1];
        echo "APP_KEY ใน .env: " . substr($envKey, 0, 15) . "...\n";
        
        if (strpos($envKey, 'base64:') === 0) {
            $decodedKey = base64_decode(substr($envKey, 7));
            $keyLength = strlen($decodedKey);
            echo "ความยาวของ key หลัง decode: $keyLength ไบต์ ";
            
            if ($keyLength == 32) {
                echo "(ถูกต้องสำหรับ aes-256-cbc)\n";
            } else {
                echo "(ไม่ถูกต้อง - ต้องเป็น 32 ไบต์สำหรับ aes-256-cbc)\n";
            }
        } else {
            echo "⚠️ APP_KEY ไม่ได้เริ่มด้วย 'base64:'\n";
        }
    } else {
        echo "⚠️ ไม่พบ APP_KEY ในไฟล์ .env\n";
    }
} else {
    echo "⚠️ ไม่พบไฟล์ .env\n";
}

echo "\nการทดสอบระบบ Encryption\n";
echo "=======================\n";

// ทดสอบ encryption service
try {
    $encrypter = app('encrypter');
    echo "✅ สามารถเรียกใช้ app('encrypter') ได้\n";
    echo "ประเภทของ encrypter: " . get_class($encrypter) . "\n";
    
    // เรียก method เพื่อตรวจสอบว่ามี method ที่จำเป็นหรือไม่
    $methods = get_class_methods($encrypter);
    $requiredMethods = ['encrypt', 'decrypt', 'getKey'];
    $optionalMethods = ['getAllKeys', 'getPreviousKeys', 'previousKeys'];
    
    echo "\nเช็คเมธอดสำคัญ:\n";
    foreach ($requiredMethods as $method) {
        echo "- $method: " . (in_array($method, $methods) ? "✅ มี" : "❌ ไม่มี") . "\n";
    }
    
    echo "\nเช็คเมธอดเพิ่มเติม:\n";
    foreach ($optionalMethods as $method) {
        echo "- $method: " . (in_array($method, $methods) ? "✅ มี" : "❓ ไม่มี") . "\n";
    }
    
    // ทดสอบการเข้ารหัสและถอดรหัส
    echo "\nทดสอบการเข้ารหัส/ถอดรหัส:\n";
    try {
        $originalText = 'ข้อความทดสอบ ภาษาไทย';
        $encryptedText = $encrypter->encrypt($originalText);
        echo "✅ เข้ารหัสสำเร็จ: " . substr($encryptedText, 0, 20) . "...\n";
        
        $decryptedText = $encrypter->decrypt($encryptedText);
        echo "✅ ถอดรหัสสำเร็จ: $decryptedText\n";
        
        if ($originalText === $decryptedText) {
            echo "✅ ผลลัพธ์ตรงกับข้อความต้นฉบับ\n";
        } else {
            echo "❌ ผลลัพธ์ไม่ตรงกับข้อความต้นฉบับ\n";
        }
    } catch (Exception $e) {
        echo "❌ เกิดข้อผิดพลาดระหว่างการเข้ารหัส/ถอดรหัส: " . $e->getMessage() . "\n";
    }
    
    // ทดสอบ encrypt() และ decrypt() helper function
    echo "\nทดสอบ helper function:\n";
    try {
        $originalText = 'ทดสอบ encrypt helper function';
        $encryptedText = encrypt($originalText);
        echo "✅ encrypt() helper สำเร็จ: " . substr($encryptedText, 0, 20) . "...\n";
        
        $decryptedText = decrypt($encryptedText);
        echo "✅ decrypt() helper สำเร็จ: $decryptedText\n";
        
        if ($originalText === $decryptedText) {
            echo "✅ ผลลัพธ์ตรงกับข้อความต้นฉบับ\n";
        } else {
            echo "❌ ผลลัพธ์ไม่ตรงกับข้อความต้นฉบับ\n";
        }
    } catch (Exception $e) {
        echo "❌ เกิดข้อผิดพลาดระหว่างการใช้ helper function: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ เกิดข้อผิดพลาดเมื่อเรียกใช้ app('encrypter'): " . $e->getMessage() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
    
    // ทางเลือกในการแก้ปัญหา
    echo "\nสิ่งที่ควรทำต่อไป:\n";
    echo "1. รัน php artisan key:generate --ansi\n";
    echo "2. ตรวจสอบว่า cipher ใน config/app.php เป็น 'aes-256-cbc'\n";
    echo "3. รัน full-encryption-db-fix.php เพื่อแก้ไขปัญหาทั้งระบบ\n";
}

echo "\nข้อมูลระบบ\n";
echo "=========\n";
echo "PHP version: " . PHP_VERSION . "\n";
echo "Laravel version: " . app()->version() . "\n";
echo "OpenSSL version: " . OPENSSL_VERSION_TEXT . "\n";
