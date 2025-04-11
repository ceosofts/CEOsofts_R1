<?php

/**
 * ทดสอบ Encrypter โดยตรงโดยไม่ต้องผ่าน Laravel Framework
 * วิธีนี้จะช่วยเราตรวจสอบว่าปัญหาอยู่ที่ Laravel หรือที่การเข้ารหัส
 */

echo "===== ทดสอบการสร้าง Encrypter โดยตรง =====\n\n";

// โหลด autoloader ของ Composer เพื่อให้ใช้คลาสต่างๆ ได้
require __DIR__ . '/vendor/autoload.php';

// 1. อ่าน APP_KEY จาก .env
echo "1. กำลังอ่านค่าจาก .env...\n";
$envPath = __DIR__ . '/.env';
$appKey = null;

if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    if (preg_match('/APP_KEY=(.+?)(\s|$)/m', $envContent, $matches)) {
        $appKey = $matches[1];
        echo "พบ APP_KEY: " . $appKey . "\n";
    }
}

if (!$appKey) {
    echo "❌ ไม่พบ APP_KEY ในไฟล์ .env กำลังสร้างคีย์ใหม่..\n";
    $appKey = 'base64:' . base64_encode(random_bytes(32));
}

// 2. แปลง APP_KEY เป็นรูปแบบที่ใช้งานได้
echo "\n2. กำลังแปลง APP_KEY เป็นรูปแบบที่ใช้งานได้...\n";
$key = null;

if (strpos($appKey, 'base64:') === 0) {
    $key = base64_decode(substr($appKey, 7));
    $keyLength = strlen($key);
    echo "ความยาวของคีย์: " . $keyLength . " ไบต์\n";
    
    if ($keyLength !== 32) {
        echo "⚠️ คำเตือน: ความยาวของคีย์ไม่เท่ากับ 32 ไบต์ ที่ต้องการสำหรับ aes-256-cbc\n";
    }
} else {
    echo "❌ ข้อผิดพลาด: APP_KEY ไม่ได้อยู่ในรูปแบบ base64:\n";
    exit(1);
}

// ตรวจสอบว่ามีคลาส Encrypter หรือไม่
echo "\n3. กำลังตรวจสอบคลาส Encrypter...\n";

if (!class_exists('Illuminate\Encryption\Encrypter')) {
    echo "❌ ข้อผิดพลาด: ไม่พบคลาส Illuminate\\Encryption\\Encrypter\n";
    exit(1);
}

// 3. สร้าง Encrypter instance
echo "\n4. กำลังสร้าง Encrypter instance...\n";
try {
    $encrypter = new \Illuminate\Encryption\Encrypter($key, 'aes-256-cbc');
    echo "✅ สร้าง Encrypter instance สำเร็จ\n";
    
    // ดูว่ามี method อะไรบ้างใน Encrypter
    $methods = get_class_methods($encrypter);
    echo "เมธอดที่มีในคลาส Encrypter: " . implode(', ', $methods) . "\n";
    
    // ตรวจสอบเมธอดที่จำเป็น
    $requiredMethods = ['encrypt', 'decrypt', 'getKey', 'getAllKeys', 'getPreviousKeys'];
    $missingMethods = [];
    
    foreach ($requiredMethods as $method) {
        if (!method_exists($encrypter, $method)) {
            $missingMethods[] = $method;
        }
    }
    
    if (!empty($missingMethods)) {
        echo "⚠️ เมธอดที่หายไป: " . implode(', ', $missingMethods) . "\n";
    } else {
        echo "✅ มีเมธอดครบตามที่ต้องการ\n";
    }
} catch (\Exception $e) {
    echo "❌ ข้อผิดพลาด: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

// 4. ทดสอบการเข้ารหัสและถอดรหัส
echo "\n5. กำลังทดสอบการเข้ารหัสและถอดรหัส...\n";
try {
    $originalText = 'ทดสอบระบบเข้ารหัสและถอดรหัส';
    $encrypted = $encrypter->encrypt($originalText);
    echo "เข้ารหัส: " . $encrypted . "\n";
    
    $decrypted = $encrypter->decrypt($encrypted);
    echo "ถอดรหัส: " . $decrypted . "\n";
    
    if ($decrypted === $originalText) {
        echo "✅ การทดสอบผ่าน! ข้อความที่ถอดรหัสตรงกับต้นฉบับ\n";
    } else {
        echo "❌ การทดสอบไม่ผ่าน! ข้อความที่ถอดรหัสไม่ตรงกับต้นฉบับ\n";
    }
} catch (\Exception $e) {
    echo "❌ ข้อผิดพลาดระหว่างการทดสอบ: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

// 5. สรุปและคำแนะนำ
echo "\n===== สรุปและคำแนะนำ =====\n";

if (!isset($missingMethods) || empty($missingMethods)) {
    echo "✅ คลาส Encrypter ทำงานได้ปกติโดยตรง\n";
    echo "🔍 ปัญหาน่าจะอยู่ที่การเรียกใช้ผ่าน Laravel Service Container\n";
    echo "คำแนะนำ:\n";
    echo "1. ลอง register CustomEncryptionProvider:\n";
    echo "   - สร้างไฟล์ app/Providers/CustomEncryptionProvider.php\n";
    echo "   - ลงทะเบียนใน bootstrap/app.php\n";
    echo "2. ปรับปรุง config/app.php ให้ไม่ใช้ env() function โดยตรง\n";
    echo "3. รัน: php artisan config:clear\n";
} else {
    echo "⚠️ พบปัญหาในคลาส Encrypter (เมธอดหายไป)\n";
    echo "คำแนะนำ:\n";
    echo "1. อัปเดตหรือรีอินสตอล Laravel package: composer update illuminate/encryption\n";
    echo "2. สร้างไฟล์ interface ที่ขาดหายไป\n";
}

echo "\nจบการทดสอบ\n";
