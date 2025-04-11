<?php

/**
 * สคริปต์รีเซ็ตการตั้งค่าการเข้ารหัสของ Laravel
 * เพื่อแก้ไขปัญหา "Unsupported cipher or incorrect key length"
 */

echo "Laravel Encryption Settings Reset\n";
echo "================================\n\n";

// ตรวจสอบว่าเป็นโปรเจกต์ Laravel หรือไม่
if (!file_exists(__DIR__ . '/artisan')) {
    echo "❌ ไม่พบไฟล์ artisan - ไม่ใช่โปรเจกต์ Laravel\n";
    exit(1);
}

// 1. แก้ไขไฟล์ config/app.php
echo "1. กำลังสำรองและแก้ไขไฟล์ config/app.php\n";

$appConfigPath = __DIR__ . '/config/app.php';
if (!file_exists($appConfigPath)) {
    echo "❌ ไม่พบไฟล์ config/app.php\n";
    exit(1);
}

// สำรองไฟล์
copy($appConfigPath, $appConfigPath . '.bak');
echo "✅ สำรองไฟล์ config/app.php ไว้ที่ config/app.php.bak\n";

// อ่านไฟล์
$appConfig = file_get_contents($appConfigPath);

// แก้ไข cipher
$appConfig = preg_replace(
    "/'cipher'\s*=>\s*'[^']*'/",
    "'cipher' => 'aes-256-cbc'",
    $appConfig
);

file_put_contents($appConfigPath, $appConfig);
echo "✅ แก้ไข cipher เป็น 'aes-256-cbc' เรียบร้อยแล้ว\n";

// 2. รีเซ็ต APP_KEY ใน .env
echo "\n2. กำลังตรวจสอบและแก้ไขไฟล์ .env\n";

$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    echo "ไม่พบไฟล์ .env กำลังสร้างจาก .env.example...\n";
    
    if (!file_exists(__DIR__ . '/.env.example')) {
        echo "❌ ไม่พบไฟล์ .env.example\n";
        exit(1);
    }
    
    copy(__DIR__ . '/.env.example', $envPath);
    echo "✅ สร้างไฟล์ .env จาก .env.example\n";
} else {
    // สำรองไฟล์ .env
    copy($envPath, $envPath . '.bak');
    echo "✅ สำรองไฟล์ .env ไว้ที่ .env.bak\n";
}

// อ่านไฟล์ .env
$envContent = file_get_contents($envPath);

// ลบ APP_KEY เดิม และเตรียมสำหรับการสร้างใหม่
$envContent = preg_replace('/APP_KEY=.*/', 'APP_KEY=', $envContent);
file_put_contents($envPath, $envContent);
echo "✅ รีเซ็ต APP_KEY ใน .env เรียบร้อยแล้ว\n";

// 3. เคลียร์ cache
echo "\n3. กำลังเคลียร์ cache ทั้งหมด\n";

// เคลียร์ cache โดยเรียกใช้คำสั่ง artisan โดยตรง
passthru('php artisan config:clear');
passthru('php artisan cache:clear');
passthru('php artisan route:clear');
passthru('php artisan view:clear');

echo "\n4. กำลังสร้าง APP_KEY ใหม่\n";
passthru('php artisan key:generate --ansi');

echo "\n✅ รีเซ็ตการตั้งค่าการเข้ารหัสเสร็จสมบูรณ์\n";
echo "โปรดรีสตาร์ท PHP server และทดสอบแอปพลิเคชันอีกครั้ง\n";
echo "คำสั่งรีสตาร์ท: php artisan serve\n";
