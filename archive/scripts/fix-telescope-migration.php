<?php

/**
 * Script to fix Telescope migration issues
 * This script will help run pending migrations only for Telescope
 */

// ตรวจสอบว่ากำลังรันจาก CLI
if (php_sapi_name() !== 'cli') {
    echo "This script can only be run from the command line.";
    exit(1);
}

// เริ่มต้น output ด้วยข้อมูลเบื้องต้น
echo "\n";
echo "======================================================\n";
echo "          Fix Telescope Migration Script              \n";
echo "======================================================\n";
echo "\n";
echo "กำลังแก้ไขปัญหาการติดตั้ง Telescope...\n";

// คำสั่งที่ต้องการรัน
$commands = [
    // Step 1: ตรวจสอบสถานะ migration
    'php artisan migrate:status',
    
    // Step 2: รัน migration เฉพาะไฟล์ของ Telescope
    'php artisan migrate --path=vendor/laravel/telescope/database/migrations',
    
    // Step 3: ทดสอบว่า Telescope ทำงานได้
    'php artisan telescope:publish',
];

// รันแต่ละคำสั่ง
foreach ($commands as $command) {
    echo "\n> กำลังรันคำสั่ง: $command\n";
    echo "------------------------------------------------------\n";
    passthru($command, $returnCode);
    
    if ($returnCode !== 0) {
        echo "\nเกิดข้อผิดพลาดในคำสั่ง: $command\n";
        echo "รหัสข้อผิดพลาด: $returnCode\n";
        echo "\nลองวิธีแก้ไขแบบอื่น...\n";
    }
}

// ขั้นตอนสุดท้าย - แนะนำการแก้ไขด้วยตนเอง
echo "\n\n";
echo "======================================================\n";
echo "                     ขั้นตอนต่อไป                     \n";
echo "======================================================\n";
echo "1. หากยังมีปัญหา ลองรันคำสั่งต่อไปนี้:\n";
echo "   php artisan migrate --pretend\n";
echo "   (เพื่อดูว่า migration ใดที่ยังไม่ถูกรัน)\n\n";
echo "2. เพื่อข้าม migration ที่มีปัญหา:\n";
echo "   php artisan migrate:skip [migration_name]\n\n";
echo "3. หรือแก้ไขไฟล์ migration ที่มีปัญหาโดยเพิ่ม check เช่น:\n";
echo "   if (!Schema::hasTable('companies')) { ... }\n\n";
echo "4. ทดสอบการทำงานของ Telescope:\n";
echo "   เข้าไปที่ http://your-app-url/telescope\n";
echo "======================================================\n";
