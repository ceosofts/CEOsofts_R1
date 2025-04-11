<?php

require __DIR__ . '/vendor/autoload.php';

use App\Encryption\SimpleEncrypter;

echo "===== Testing SimpleEncrypter =====\n\n";

try {
    // สร้าง instance
    $encrypter = new SimpleEncrypter();
    echo "✅ สร้าง SimpleEncrypter สำเร็จ\n";
    
    // ทดสอบเข้ารหัสข้อความ
    $original = "ทดสอบการเข้ารหัสด้วย SimpleEncrypter";
    echo "\nข้อมูลต้นฉบับ: $original\n";
    
    $encrypted = $encrypter->encrypt($original);
    echo "ข้อมูลที่เข้ารหัสแล้ว: " . $encrypted . "\n";
    
    $decrypted = $encrypter->decrypt($encrypted);
    echo "ข้อมูลที่ถอดรหัสแล้ว: " . $decrypted . "\n";
    
    echo "\nผลการทดสอบ: " . ($original === $decrypted ? "✅ ผ่าน" : "❌ ไม่ผ่าน") . "\n";
    
    // ทดสอบเข้ารหัสอาร์เรย์
    echo "\nทดสอบกับข้อมูลที่เป็น Array:\n";
    $array = ['name' => 'ทดสอบ', 'value' => 123];
    $encrypted = $encrypter->encrypt($array);
    echo "เข้ารหัสสำเร็จ: " . $encrypted . "\n";
    
    $decrypted = $encrypter->decrypt($encrypted);
    echo "ถอดรหัสกลับเป็น Array สำเร็จ: " . json_encode($decrypted, JSON_UNESCAPED_UNICODE) . "\n";
    
} catch (Exception $e) {
    echo "❌ เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}