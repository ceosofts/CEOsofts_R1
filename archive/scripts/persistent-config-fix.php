<?php

/**
 * สคริปต์แก้ไขการตั้งค่าแบบถาวร
 * แก้ไขปัญหา Encryption Key และ Cipher โดยตรงในไฟล์ที่เกี่ยวข้อง
 */

echo "Laravel Persistent Config Fix\n";
echo "============================\n\n";

// 1. แก้ไขไฟล์ config/app.php
echo "1. กำลังตรวจสอบและแก้ไขไฟล์ config/app.php\n";

$appConfigPath = __DIR__ . '/config/app.php';
if (!file_exists($appConfigPath)) {
    echo "❌ ไม่พบไฟล์ config/app.php\n";
    exit(1);
}

// อ่านไฟล์
$appConfig = file_get_contents($appConfigPath);

// สำรองไฟล์
$backupPath = __DIR__ . '/config/app.php.persistent-fix-backup';
file_put_contents($backupPath, $appConfig);
echo "✅ สำรองไฟล์ไว้ที่ " . basename($backupPath) . "\n";

// ตรวจหา pattern ของ Cipher
$cipherPattern = "/'cipher'\s*=>\s*'([^']*)'/";
if (preg_match($cipherPattern, $appConfig, $matches)) {
    $currentCipher = $matches[1];
    echo "พบค่า cipher ปัจจุบัน: {$currentCipher}\n";
    
    // ถ้า cipher ไม่ใช่ค่าที่ต้องการ (aes-256-cbc) ให้แก้ไข
    if (strtolower($currentCipher) !== 'aes-256-cbc') {
        $appConfig = preg_replace($cipherPattern, "'cipher' => 'aes-256-cbc'", $appConfig);
        echo "แก้ไข cipher เป็น 'aes-256-cbc'\n";
        
        // บันทึกไฟล์
        file_put_contents($appConfigPath, $appConfig);
        echo "บันทึกการเปลี่ยนแปลง\n";
    } else {
        echo "✅ cipher มีค่าถูกต้องแล้ว\n";
    }
} else {
    echo "⚠️ ไม่พบการกำหนดค่า cipher ในไฟล์\n";
}

// 2. ตรวจสอบว่ามีการกำหนดค่า key อื่นๆ ที่อาจทำให้เกิดปัญหาหรือไม่
echo "\n2. กำลังตรวจสอบการกำหนดค่า key อื่นๆ\n";

// ค้นหาการกำหนดค่า key แบบ hardcode
$keyPattern = "/'key'\s*=>\s*([^,]*)(?:,|$)/";
if (preg_match($keyPattern, $appConfig, $matches)) {
    $keyValue = trim($matches[1]);
    
    if ($keyValue !== "env('APP_KEY')") {
        echo "⚠️ พบการกำหนดค่า key แบบ hardcode: {$keyValue}\n";
        echo "กำลังแก้ไขให้ใช้ค่าจาก env...\n";
        
        $appConfig = preg_replace($keyPattern, "'key' => env('APP_KEY'),", $appConfig);
        file_put_contents($appConfigPath, $appConfig);
        echo "✅ แก้ไขให้ใช้ค่าจาก env เรียบร้อยแล้ว\n";
    } else {
        echo "✅ การกำหนดค่า key ถูกต้อง: {$keyValue}\n";
    }
} else {
    echo "⚠️ ไม่พบการกำหนดค่า key ในไฟล์\n";
}

// 3. แก้ไขไฟล์ .env
echo "\n3. กำลังตรวจสอบและแก้ไขไฟล์ .env\n";

$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    echo "❌ ไม่พบไฟล์ .env\n";
    
    if (file_exists(__DIR__ . '/.env.example')) {
        echo "กำลังคัดลอกไฟล์ .env.example เป็น .env...\n";
        copy(__DIR__ . '/.env.example', $envPath);
        echo "✅ สร้างไฟล์ .env จาก .env.example เรียบร้อยแล้ว\n";
    } else {
        echo "❌ ไม่พบไฟล์ .env.example ด้วยเช่นกัน\n";
        exit(1);
    }
}

// อ่านไฟล์ .env
$envContent = file_get_contents($envPath);

// สำรองไฟล์ .env
$envBackupPath = $envPath . '.persistent-fix-backup';
file_put_contents($envBackupPath, $envContent);
echo "✅ สำรองไฟล์ .env ไว้ที่ " . basename($envBackupPath) . "\n";

// ตรวจสอบว่ามี APP_KEY หรือไม่
if (preg_match('/^APP_KEY=(.*)$/m', $envContent, $matches)) {
    $appKeyValue = trim($matches[1]);
    echo "พบ APP_KEY: " . (empty($appKeyValue) ? "[ว่างเปล่า]" : $appKeyValue) . "\n";
    
    // ถ้า APP_KEY ไม่มีค่า หรือไม่ได้เริ่มต้นด้วย base64: หรือความยาวไม่ถูกต้อง
    $needNewKey = empty($appKeyValue) || $appKeyValue === "base64:" || strpos($appKeyValue, "base64:") !== 0;
    
    // ตรวจสอบความยาวของ key
    if (!$needNewKey && strpos($appKeyValue, "base64:") === 0) {
        $decodedKey = base64_decode(substr($appKeyValue, 7));
        if (strlen($decodedKey) != 32) {
            $needNewKey = true;
            echo "⚠️ ความยาวของ key ไม่ถูกต้อง: " . strlen($decodedKey) . " bytes (ควรเป็น 32 bytes)\n";
        }
    }
    
    if ($needNewKey) {
        echo "⚠️ APP_KEY ไม่ถูกต้อง จำเป็นต้องสร้างใหม่\n";
        
        // ลบบรรทัด APP_KEY เดิม
        $envContent = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=', $envContent);
        file_put_contents($envPath, $envContent);
        
        // สร้าง APP_KEY ใหม่
        echo "กำลังสร้าง APP_KEY ใหม่...\n";
        exec('php artisan key:generate --ansi');
        echo "✅ สร้าง APP_KEY ใหม่เรียบร้อยแล้ว\n";
        
        // อ่านค่า APP_KEY ใหม่
        $newEnvContent = file_get_contents($envPath);
        if (preg_match('/^APP_KEY=(.*)$/m', $newEnvContent, $newMatches)) {
            echo "APP_KEY ใหม่: " . $newMatches[1] . "\n";
        } else {
            echo "⚠️ ไม่พบ APP_KEY หลังจากการสร้างใหม่ อาจมีปัญหากับคำสั่ง key:generate\n";
        }
    } else {
        echo "✅ APP_KEY มีค่าถูกต้องแล้ว\n";
    }
} else {
    echo "⚠️ ไม่พบบรรทัด APP_KEY ในไฟล์ .env\n";
    
    // เพิ่มบรรทัด APP_KEY ลงในไฟล์ .env
    $envContent .= "\nAPP_KEY=\n";
    file_put_contents($envPath, $envContent);
    
    // สร้าง APP_KEY
    echo "กำลังสร้าง APP_KEY...\n";
    exec('php artisan key:generate --ansi');
    echo "✅ เพิ่มและสร้าง APP_KEY เรียบร้อยแล้ว\n";
}

// 4. เคลียร์แคช
echo "\n4. กำลังเคลียร์แคชและ autoload...\n";

// Clear Laravel cache
exec('php artisan config:clear');
exec('php artisan cache:clear');
exec('php artisan route:clear');
exec('php artisan view:clear');
exec('php artisan optimize:clear');

// Regenerate autoload
exec('composer dump-autoload -o');

echo "✅ เคลียร์แคชและ regenerate autoload เรียบร้อยแล้ว\n";

// 5. ทดสอบการเข้ารหัส
echo "\n5. กำลังทดสอบการเข้ารหัส...\n";

// ต้อง bootstrap Laravel app
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // ทดสอบการเข้ารหัส
    $testString = "ทดสอบการเข้ารหัส";
    $encrypted = \Illuminate\Support\Facades\Crypt::encrypt($testString);
    $decrypted = \Illuminate\Support\Facades\Crypt::decrypt($encrypted);
    
    if ($decrypted === $testString) {
        echo "✅ ทดสอบการเข้ารหัสสำเร็จ! ระบบเข้ารหัสทำงานได้ถูกต้อง\n";
        
        // แสดงการตั้งค่าปัจจุบัน
        echo "\nการตั้งค่าปัจจุบัน:\n";
        echo "- Cipher: " . config('app.cipher') . "\n";
        echo "- APP_KEY: [ถูกตั้งค่าไว้แล้ว]\n";
    } else {
        echo "❌ ทดสอบการเข้ารหัสล้มเหลว ข้อความที่ถอดรหัสไม่ตรงกับข้อความต้นฉบับ\n";
    }
} catch (\Exception $e) {
    echo "❌ เกิดข้อผิดพลาดขณะทดสอบการเข้ารหัส: " . $e->getMessage() . "\n";
    
    // แสดงคำแนะนำ
    echo "\nคำแนะนำ:\n";
    echo "1. รันคำสั่ง: php artisan key:generate --ansi\n";
    echo "2. ตรวจสอบไฟล์ config/app.php และแก้ไข cipher เป็น 'aes-256-cbc'\n";
    echo "3. เคลียร์แคช: php artisan optimize:clear\n";
}

echo "\n✅ การแก้ไขการตั้งค่าแบบถาวรเสร็จสมบูรณ์\n";
echo "โปรดรีสตาร์ท PHP server และทดสอบแอปพลิเคชันอีกครั้ง\n";
echo "คำสั่งรีสตาร์ท: php artisan serve\n";
