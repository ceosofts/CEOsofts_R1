<?php

/**
 * เครื่องมือสร้างไฟล์ .env ใหม่เพื่อแก้ไขปัญหาการเข้ารหัสของ Laravel
 * สคริปต์นี้จะสร้างไฟล์ .env ใหม่ทั้งหมดและตั้งค่า APP_KEY ใหม่
 */

echo "Laravel Clean .env Generator\n";
echo "===========================\n\n";

// 1. สำรองไฟล์เดิม
$timestamp = date('YmdHis');
$backupDir = __DIR__ . '/backups';

if (!file_exists($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    copy($envPath, $backupDir . '/.env.' . $timestamp);
    echo "✅ สำรองไฟล์ .env เดิมไปที่ backups/.env.$timestamp\n";
}

// 2. อ่านค่าจาก .env.example หรือสร้างค่าเริ่มต้นใหม่
$envExample = __DIR__ . '/.env.example';
$envContent = '';

if (file_exists($envExample)) {
    $envContent = file_get_contents($envExample);
    echo "✅ อ่านค่าเริ่มต้นจาก .env.example\n";
} else {
    // สร้างค่าเริ่มต้นถ้าไม่มีไฟล์ .env.example
    $envContent = <<<EOT
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ceosofts_db_r1
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
EOT;
    echo "✅ สร้างค่าเริ่มต้นสำหรับ .env ใหม่\n";
}

// 3. สร้าง APP_KEY ใหม่
$key = base64_encode(random_bytes(32));
$appKey = 'base64:' . $key;

// 4. อัปเดตหรือเพิ่ม APP_KEY ใน env content
if (preg_match('/^APP_KEY=/m', $envContent)) {
    $envContent = preg_replace('/^APP_KEY=.*$/m', "APP_KEY=$appKey", $envContent);
} else {
    // เพิ่ม APP_KEY ถ้าไม่มี
    $envContent = "APP_KEY=$appKey\n" . $envContent;
}

// 5. ตรวจสอบแน่ใจว่ามีการตั้งค่า cipher ที่ถูกต้องในไฟล์ config/app.php
$configAppPath = __DIR__ . '/config/app.php';
if (file_exists($configAppPath)) {
    $appConfig = file_get_contents($configAppPath);
    
    // ตรวจสอบการตั้งค่า cipher
    if (!preg_match("/'cipher'\s*=>\s*'aes-256-cbc'/i", $appConfig)) {
        echo "⚠️ ไม่พบการตั้งค่า cipher เป็น aes-256-cbc ในไฟล์ config/app.php\n";
        echo "   กรุณาแก้ไขไฟล์ config/app.php ให้มีบรรทัดต่อไปนี้:\n";
        echo "   'cipher' => 'aes-256-cbc',\n\n";
    }
}

// 6. บันทึกไฟล์ .env ใหม่
file_put_contents($envPath, $envContent);
echo "✅ สร้างไฟล์ .env ใหม่เรียบร้อยแล้ว\n";
echo "✅ APP_KEY ใหม่: $appKey\n";

// 7. ล้าง config cache
echo "\nกำลังล้าง cache...\n";
$clearCommands = [
    'php artisan config:clear',
    'php artisan cache:clear',
    'php artisan view:clear',
    'php artisan route:clear'
];

foreach ($clearCommands as $command) {
    echo "รันคำสั่ง: $command\n";
    passthru($command);
}

echo "\n===========================\n";
echo "การตั้งค่าเสร็จสมบูรณ์!\n";
echo "โปรดรันคำสั่งต่อไปนี้เพื่อเริ่มเซิร์ฟเวอร์:\n";
echo "php artisan serve\n";
echo "===========================\n";
