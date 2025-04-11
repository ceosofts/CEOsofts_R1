<?php

/**
 * ไฟล์สำหรับทดสอบ Encrypter แบบแมนนวล
 * โดยไม่ต้องอาศัยกลไก service container ของ Laravel
 */

// อัตโนมัติโหลดคลาสจาก Composer
require __DIR__.'/vendor/autoload.php';

use Illuminate\Encryption\Encrypter;

echo "Laravel Manual Encrypter Test\n";
echo "============================\n\n";

try {
    // อ่าน key จากไฟล์ .env
    $envPath = __DIR__ . '/.env';
    $key = null;
    
    if (file_exists($envPath)) {
        $content = file_get_contents($envPath);
        if (preg_match('/APP_KEY=base64:([^\s\n]+)/', $content, $matches)) {
            $key = base64_decode($matches[1]);
            $keyLength = strlen($key);
            echo "1. พบ APP_KEY ในไฟล์ .env (ความยาว: $keyLength ไบต์)\n";
        }
    }
    
    // ถ้าไม่มี key หรือความยาวไม่ถูกต้อง ให้สร้างใหม่
    if (!$key || strlen($key) !== 32) {
        $key = random_bytes(32);
        echo "2. สร้าง key ใหม่ขนาด 32 ไบต์สำหรับ aes-256-cbc\n";
    } else {
        echo "2. ใช้ key จาก .env\n";
    }
    
    // สร้าง instance ของ Encrypter โดยตรง
    $encrypter = new Encrypter($key, 'aes-256-cbc');
    echo "3. สร้าง Encrypter instance สำเร็จ\n";
    
    // ทดสอบการเข้ารหัสและถอดรหัส
    $originalText = 'ทดสอบการเข้ารหัสและถอดรหัส';
    $encrypted = $encrypter->encrypt($originalText);
    echo "4. เข้ารหัสข้อความสำเร็จ: " . substr($encrypted, 0, 30) . "...\n";
    
    $decrypted = $encrypter->decrypt($encrypted);
    echo "5. ถอดรหัสกลับมาสำเร็จ: $decrypted\n";
    
    if ($originalText === $decrypted) {
        echo "\n✅ ผลลัพธ์: การทดสอบผ่าน! การเข้ารหัสและถอดรหัสทำงานได้ปกติ\n";
    } else {
        echo "\n❌ ผลลัพธ์: การทดสอบไม่ผ่าน! ข้อความที่ถอดรหัสไม่ตรงกับต้นฉบับ\n";
    }
    
    echo "\nข้อมูลเพิ่มเติม:\n";
    echo "- Cipher ที่ใช้: aes-256-cbc\n";
    echo "- ความยาวของ key: " . strlen($key) . " ไบต์\n";
    
    // สร้างคำสั่งสำหรับสร้าง APP_KEY ใหม่ใน .env
    $newEnvKey = 'base64:' . base64_encode($key);
    echo "\nหากต้องการอัปเดต APP_KEY ใน .env ให้ใช้ค่านี้:\n";
    echo "APP_KEY=$newEnvKey\n\n";
    
    // สร้างคำสั่งสำหรับเรียกใช้ Encrypter ใน artisan tinker
    echo "คำสั่งสำหรับทดสอบใน artisan tinker:\n";
    echo "encrypt('test');\n";
    echo "decrypt(encrypt('test'));\n";
    
} catch (Exception $e) {
    echo "\n❌ เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
    echo "สาเหตุอาจมาจาก:\n";
    echo "- ความยาวของ key ไม่ถูกต้อง (ต้องเป็น 16 หรือ 32 ไบต์)\n";
    echo "- Cipher ไม่รองรับ\n";
    echo "- ไลบรารีที่จำเป็นไม่ได้ถูกติดตั้ง\n";
    
    echo "\nStack trace:\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n============================\n";
