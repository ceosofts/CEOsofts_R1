<?php

/**
 * แก้ไขปัญหาเรื่อง Session และ Cache Driver 
 * ในกรณีที่มีการตั้งค่าเป็น database แต่ไม่มี database
 */

echo "===== Session & Cache Driver Fix =====\n\n";

// 1. แก้ไขไฟล์ .env
echo "1. กำลังตรวจสอบและแก้ไขไฟล์ .env\n";
$envPath = __DIR__ . '/.env';

if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    $modified = false;
    
    // เปลี่ยน SESSION_DRIVER=database เป็น SESSION_DRIVER=file
    if (preg_match('/^SESSION_DRIVER=database/m', $envContent)) {
        $envContent = preg_replace('/^SESSION_DRIVER=database/m', 'SESSION_DRIVER=file', $envContent);
        $modified = true;
        echo "   ✅ เปลี่ยน SESSION_DRIVER จาก database เป็น file\n";
    }
    
    // เปลี่ยน CACHE_STORE=database เป็น CACHE_STORE=file
    if (preg_match('/^CACHE_STORE=database/m', $envContent)) {
        $envContent = preg_replace('/^CACHE_STORE=database/m', 'CACHE_STORE=file', $envContent);
        $modified = true;
        echo "   ✅ เปลี่ยน CACHE_STORE จาก database เป็น file\n";
    }
    
    if ($modified) {
        // สำรองไฟล์เดิมก่อนบันทึกทับ
        $timestamp = date('YmdHis');
        $backupDir = __DIR__ . '/backups';
        
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        copy($envPath, "$backupDir/.env.$timestamp");
        echo "   ✅ สำรองไฟล์ .env เดิมไว้ที่ backups/.env.$timestamp\n";
        
        // บันทึกไฟล์ .env ที่แก้ไขแล้ว
        file_put_contents($envPath, $envContent);
        echo "   ✅ บันทึกไฟล์ .env ที่แก้ไขแล้ว\n";
    } else {
        echo "   ✓ ไม่จำเป็นต้องแก้ไข SESSION_DRIVER และ CACHE_STORE\n";
    }
} else {
    echo "   ❌ ไม่พบไฟล์ .env\n";
}

// 2. เตรียมโฟลเดอร์สำหรับ cache และ session
echo "\n2. กำลังเตรียมโฟลเดอร์สำหรับ cache และ session\n";

$storageDirs = [
    'framework/sessions',
    'framework/views',
    'framework/cache',
    'framework/cache/data',
    'logs'
];

foreach ($storageDirs as $dir) {
    $path = __DIR__ . '/storage/' . $dir;
    if (!file_exists($path)) {
        mkdir($path, 0755, true);
        echo "   ✅ สร้างโฟลเดอร์ $path\n";
    }
}

// ตรวจสอบและตั้งค่าสิทธิ์
if (function_exists('chmod')) {
    chmod(__DIR__ . '/storage', 0755);
    chmod(__DIR__ . '/storage/framework', 0755);
    echo "   ✅ ตั้งค่าสิทธิ์โฟลเดอร์ storage\n";
}

// 3. ล้าง cache
echo "\n3. กำลังล้าง cache...\n";
$cacheCommands = [
    'php artisan cache:clear',
    'php artisan config:clear',
    'php artisan view:clear',
    'php artisan route:clear',
];

foreach ($cacheCommands as $command) {
    echo "   $ $command\n";
    // ไม่แสดงผลลัพธ์คำสั่ง แค่รายงานสถานะ
    $output = [];
    $returnVar = 0;
    exec($command, $output, $returnVar);
    echo "   " . ($returnVar === 0 ? "✅ สำเร็จ" : "❌ ล้มเหลว") . "\n";
}

echo "\n===== การแก้ไขเสร็จสมบูรณ์ =====\n";
echo "ลองรัน PHP Server อีกครั้ง:\n";
echo "php artisan serve --port=8004\n";
