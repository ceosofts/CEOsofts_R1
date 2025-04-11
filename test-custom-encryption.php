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