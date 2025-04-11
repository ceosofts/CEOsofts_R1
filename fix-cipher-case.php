<?php

/**
 * สคริปต์แก้ไขปัญหา cipher ใน Laravel 
 * เปลี่ยน AES-256-CBC เป็น aes-256-cbc เนื่องจาก Laravel ต้องการตัวพิมพ์เล็ก
 */

$appConfigPath = __DIR__ . '/config/app.php';

if (!file_exists($appConfigPath)) {
    echo "ไม่พบไฟล์ config/app.php\n";
    exit(1);
}

// อ่านไฟล์
$configContent = file_get_contents($appConfigPath);
echo "อ่านไฟล์ config/app.php สำเร็จ\n";

// สำรองไฟล์
file_put_contents($appConfigPath . '.bak', $configContent);
echo "สำรองไฟล์ไว้ที่ config/app.php.bak\n";

// ค้นหาบรรทัดที่มีการกำหนดค่า cipher
$pattern = "/'cipher'\s*=>\s*'([^']*)'/";
if (preg_match($pattern, $configContent, $matches)) {
    $currentCipher = $matches[1];
    echo "พบ cipher ปัจจุบัน: {$currentCipher}\n";

    // เปลี่ยนเป็นตัวพิมพ์เล็ก
    $newCipher = strtolower($currentCipher);
    if ($currentCipher !== $newCipher) {
        $configContent = str_replace(
            "'cipher' => '{$currentCipher}'",
            "'cipher' => '{$newCipher}'",
            $configContent
        );
        echo "แก้ไขจาก {$currentCipher} เป็น {$newCipher}\n";

        // บันทึกไฟล์
        file_put_contents($appConfigPath, $configContent);
        echo "บันทึกไฟล์สำเร็จ\n";
    } else {
        echo "cipher เป็นตัวพิมพ์เล็กอยู่แล้ว\n";
    }
} else {
    echo "ไม่พบการกำหนดค่า cipher ในไฟล์ config/app.php\n";
}

echo "\nทดลองแก้ไขโดยตรง...\n";

// แก้ไขโดยตรงด้วยการทดแทนทุกรูปแบบที่เป็นไปได้
$patterns = [
    "/'cipher'\s*=>\s*'AES-256-CBC'/i" => "'cipher' => 'aes-256-cbc'",
    "/'cipher'\s*=>\s*'AES-128-CBC'/i" => "'cipher' => 'aes-128-cbc'",
    "/'cipher'\s*=>\s*'AES-256-GCM'/i" => "'cipher' => 'aes-256-gcm'",
    "/'cipher'\s*=>\s*'AES-128-GCM'/i" => "'cipher' => 'aes-128-gcm'"
];

$newContent = $configContent;
foreach ($patterns as $pattern => $replacement) {
    $newContent = preg_replace($pattern, $replacement, $newContent);
}

if ($newContent !== $configContent) {
    file_put_contents($appConfigPath, $newContent);
    echo "แก้ไขรูปแบบ cipher ทั้งหมดเสร็จสิ้น\n";
} else {
    echo "ไม่มีการเปลี่ยนแปลงจากการแก้ไขรูปแบบทั้งหมด\n";
}

// เคลียร์ cache
echo "\nกำลังเคลียร์ cache...\n";
passthru('php artisan config:clear');
passthru('php artisan cache:clear');
passthru('php artisan route:clear');
passthru('php artisan view:clear');

echo "\n\nการแก้ไขเสร็จสิ้น! กรุณาลองรีสตาร์ท PHP server และเข้าใช้งานแอปพลิเคชันอีกครั้ง\n";
echo "คุณสามารถรันคำสั่งต่อไปนี้เพื่อรีสตาร์ท:\n";
echo "php artisan serve\n";
