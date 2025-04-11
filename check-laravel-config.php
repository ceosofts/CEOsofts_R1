<?php

/**
 * สคริปต์ตรวจสอบการตั้งค่า Laravel
 * รันด้วยคำสั่ง: php check-laravel-config.php
 */

echo "Laravel Environment Checker\n";
echo "=========================\n\n";

// ตรวจสอบไฟล์สำคัญ
$requiredFiles = [
    'artisan',
    '.env',
    'config/app.php',
    'bootstrap/app.php'
];

echo "1. ตรวจสอบไฟล์สำคัญ\n";
$allFilesExist = true;
foreach ($requiredFiles as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "✅ พบไฟล์ {$file}\n";
    } else {
        echo "❌ ไม่พบไฟล์ {$file}\n";
        $allFilesExist = false;
    }
}

if (!$allFilesExist) {
    echo "\n⚠️ ไม่พบไฟล์สำคัญบางไฟล์ โปรดตรวจสอบว่านี่คือโปรเจกต์ Laravel\n";
    exit(1);
}

// ตรวจสอบ .env และ APP_KEY
echo "\n2. ตรวจสอบไฟล์ .env และ APP_KEY\n";
$envContent = file_get_contents(__DIR__ . '/.env');
$appKey = null;

if (preg_match('/APP_KEY=(.*)/', $envContent, $matches)) {
    $appKey = trim($matches[1]);
    echo "🔑 APP_KEY: {$appKey}\n";
    
    if (empty($appKey) || $appKey == 'base64:') {
        echo "⚠️ APP_KEY ว่างเปล่าหรือไม่สมบูรณ์\n";
    } elseif (strpos($appKey, 'base64:') !== 0) {
        echo "⚠️ APP_KEY ไม่ได้ขึ้นต้นด้วย 'base64:'\n";
    } else {
        $keyLength = strlen(base64_decode(substr($appKey, 7)));
        echo "📏 ความยาวของ key: {$keyLength} bytes\n";
        
        if ($keyLength != 32) {
            echo "⚠️ ความยาว key ไม่ถูกต้อง (ควรเป็น 32 bytes)\n";
        }
    }
} else {
    echo "❌ ไม่พบ APP_KEY ในไฟล์ .env\n";
}

// ตรวจสอบ cipher ใน config/app.php
echo "\n3. ตรวจสอบ cipher ใน config/app.php\n";
$appConfigContent = file_get_contents(__DIR__ . '/config/app.php');
$cipher = null;

if (preg_match("/'cipher'\s*=>\s*'([^']*)'/", $appConfigContent, $matches)) {
    $cipher = $matches[1];
    echo "🔒 Cipher setting: {$cipher}\n";
    
    // ตรวจสอบว่า cipher เป็นรูปแบบที่รองรับหรือไม่
    $supportedCiphers = ['aes-128-cbc', 'aes-256-cbc', 'aes-128-gcm', 'aes-256-gcm'];
    if (!in_array(strtolower($cipher), $supportedCiphers)) {
        echo "❌ ไม่รองรับ cipher นี้\n";
        echo "💡 ต้องใช้หนึ่งในตัวเลือกต่อไปนี้: " . implode(', ', $supportedCiphers) . "\n";
    } elseif ($cipher !== strtolower($cipher)) {
        echo "⚠️ cipher ควรใช้ตัวพิมพ์เล็กทั้งหมด (เช่น 'aes-256-cbc' แทน '{$cipher}')\n";
    } else {
        echo "✅ cipher ถูกต้อง\n";
    }
} else {
    echo "❌ ไม่พบการตั้งค่า cipher ในไฟล์ config/app.php\n";
}

// สรุปปัญหาและแนวทางแก้ไข
echo "\n4. สรุปและคำแนะนำ\n";

$problems = [];

if (empty($appKey) || $appKey == 'base64:' || strpos($appKey, 'base64:') !== 0) {
    $problems[] = "APP_KEY ไม่ถูกต้อง";
}

if (!in_array(strtolower($cipher), ['aes-128-cbc', 'aes-256-cbc', 'aes-128-gcm', 'aes-256-gcm'])) {
    $problems[] = "cipher ไม่รองรับ";
} elseif ($cipher !== strtolower($cipher)) {
    $problems[] = "cipher ไม่ใช่ตัวพิมพ์เล็กทั้งหมด";
}

if (!empty($problems)) {
    echo "❌ พบปัญหา: " . implode(", ", $problems) . "\n";
    echo "\nคำแนะนำในการแก้ไข:\n";
    
    if (empty($appKey) || $appKey == 'base64:' || strpos($appKey, 'base64:') !== 0) {
        echo "1. สร้าง APP_KEY ใหม่:\n";
        echo "   php artisan key:generate --ansi\n";
    }
    
    if (!in_array(strtolower($cipher), ['aes-128-cbc', 'aes-256-cbc', 'aes-128-gcm', 'aes-256-gcm']) || $cipher !== strtolower($cipher)) {
        echo "2. แก้ไข cipher ในไฟล์ config/app.php ให้เป็น 'aes-256-cbc':\n";
        echo "   'cipher' => 'aes-256-cbc',\n";
    }
    
    echo "\nหรือรันสคริปต์แก้ไขอัตโนมัติ:\n";
    echo "php fix-cipher-case.php\n";
    echo "หรือ\nbash fix-all-issues.sh\n";
} else {
    echo "✅ การตั้งค่า encryption ปกติ\n";
    echo "\nหากยังพบปัญหา 'Unsupported cipher or incorrect key length' อาจเกิดจาก:\n";
    echo "1. มีการแก้ไขค่าในหน่วยความจำระหว่างรัน\n";
    echo "2. มีการกำหนดค่า cipher ในที่อื่นนอกเหนือจาก config/app.php\n";
    echo "3. มีปัญหาเกี่ยวกับ service provider\n";
    
    echo "\nลองรันคำสั่งเหล่านี้:\n";
    echo "composer dump-autoload -o\n";
    echo "php artisan config:clear\n";
    echo "php artisan cache:clear\n";
    echo "php artisan config:cache\n";
}
