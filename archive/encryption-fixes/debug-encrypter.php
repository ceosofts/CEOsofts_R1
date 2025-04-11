<?php

// ไฟล์สำหรับตรวจสอบการทำงานของ Encrypter ในระดับต่ำ

require __DIR__.'/vendor/autoload.php';

// ดึงมาตรงๆ จาก vendor ไม่ผ่าน Laravel
$encrypterClass = new ReflectionClass(\Illuminate\Encryption\Encrypter::class);
echo "1. สามารถโหลด Encrypter class: สำเร็จ\n\n";

// แสดงข้อมูล static properties
echo "2. Static properties:\n";
try {
    $supportedCiphersProperty = $encrypterClass->getProperty('supportedCiphers');
    $supportedCiphersProperty->setAccessible(true);
    $supportedCiphers = $supportedCiphersProperty->getValue();
    echo "supportedCiphers: " . json_encode($supportedCiphers, JSON_PRETTY_PRINT) . "\n";
} catch (Exception $e) {
    echo "ไม่สามารถเข้าถึง supportedCiphers: {$e->getMessage()}\n";
}
echo "\n";

// ทดสอบการสร้าง key และสร้าง instance
echo "3. ทดสอบสร้าง instance:\n";
try {
    $key = random_bytes(32);
    $encrypter = new \Illuminate\Encryption\Encrypter($key, 'aes-256-cbc');
    echo "สร้าง instance สำเร็จ\n";
    
    $value = "ทดสอบการเข้ารหัส";
    $encrypted = $encrypter->encrypt($value);
    $decrypted = $encrypter->decrypt($encrypted);
    
    echo "เข้ารหัส: " . $encrypted . "\n";
    echo "ถอดรหัส: " . $decrypted . "\n";
    echo "ผลลัพธ์: " . ($value === $decrypted ? "ถูกต้อง ✓" : "ไม่ถูกต้อง ✗") . "\n";
} catch (Exception $e) {
    echo "เกิดข้อผิดพลาด: {$e->getMessage()}\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

// ลองทดสอบแบบใช้ key uppercase
echo "\n4. ทดสอบด้วย cipher ตัวพิมพ์ใหญ่:\n";
try {
    $key = random_bytes(32);
    $encrypter = new \Illuminate\Encryption\Encrypter($key, 'AES-256-CBC');
    echo "สร้าง instance สำเร็จ\n";
    
    $value = "ทดสอบการเข้ารหัสด้วยตัวพิมพ์ใหญ่";
    $encrypted = $encrypter->encrypt($value);
    $decrypted = $encrypter->decrypt($encrypted);
    
    echo "เข้ารหัส: " . $encrypted . "\n";
    echo "ถอดรหัส: " . $decrypted . "\n";
    echo "ผลลัพธ์: " . ($value === $decrypted ? "ถูกต้อง ✓" : "ไม่ถูกต้อง ✗") . "\n";
} catch (Exception $e) {
    echo "เกิดข้อผิดพลาด: {$e->getMessage()}\n";
}

// บู๊ตแอป Laravel และทดสอบ
echo "\n5. ทดสอบผ่าน Laravel container:\n";
try {
    $app = require_once __DIR__.'/bootstrap/app.php';
    $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    $encrypter = app('encrypter');
    echo "ได้รับ encrypter จาก container สำเร็จ\n";
    
    $value = "ทดสอบการเข้ารหัสผ่าน container";
    $encrypted = $encrypter->encrypt($value);
    $decrypted = $encrypter->decrypt($encrypted);
    
    echo "เข้ารหัส: " . $encrypted . "\n";
    echo "ถอดรหัส: " . $decrypted . "\n";
    echo "ผลลัพธ์: " . ($value === $decrypted ? "ถูกต้อง ✓" : "ไม่ถูกต้อง ✗") . "\n";
    
    // แสดง cipher ที่ใช้จริง
    $reflectionEncrypter = new ReflectionClass($encrypter);
    $cipherProperty = $reflectionEncrypter->getProperty('cipher');
    $cipherProperty->setAccessible(true);
    $cipherValue = $cipherProperty->getValue($encrypter);
    
    echo "Cipher ที่ใช้จริง: " . $cipherValue . "\n";
    
    // แสดงค่า key ที่ใช้จริง (แสดงแค่ความยาว)
    $keyProperty = $reflectionEncrypter->getProperty('key');
    $keyProperty->setAccessible(true);
    $keyValue = $keyProperty->getValue($encrypter);
    
    echo "Key length: " . strlen($keyValue) . " bytes\n";
    
    // ทดสอบ encrypt helper function
    $helperEncrypted = encrypt("ทดสอบ helper function");
    $helperDecrypted = decrypt($helperEncrypted);
    
    echo "\nทดสอบ encrypt/decrypt helpers: " . ($helperDecrypted === "ทดสอบ helper function" ? "ถูกต้อง ✓" : "ไม่ถูกต้อง ✗") . "\n";
} catch (Exception $e) {
    echo "เกิดข้อผิดพลาด: {$e->getMessage()}\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}