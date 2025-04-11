<?php

/**
 * สคริปต์แก้ไขปัญหาเมธอด previousKeys() ที่ขาดหายจากคลาส Encrypter
 */

echo "Fix Missing previousKeys() Method in Encrypter\n";
echo "=========================================\n\n";

// ไฟล์ Encrypter.php ของ Laravel
$encrypterPath = __DIR__ . '/vendor/laravel/framework/src/Illuminate/Encryption/Encrypter.php';

// ตรวจสอบว่าไฟล์มีอยู่จริงหรือไม่
if (!file_exists($encrypterPath)) {
    die("ไม่พบไฟล์ Encrypter.php ที่ $encrypterPath\n");
}

// สำรองไฟล์ก่อน
$timestamp = date('YmdHis');
$backupPath = __DIR__ . '/backups';

if (!is_dir($backupPath)) {
    mkdir($backupPath, 0755, true);
}

$encrypterBackupPath = "$backupPath/Encrypter.php.$timestamp";
copy($encrypterPath, $encrypterBackupPath);
echo "✅ สำรองไฟล์ Encrypter.php เรียบร้อยแล้วที่ $encrypterBackupPath\n\n";

// อ่านเนื้อหาไฟล์ Encrypter.php
$content = file_get_contents($encrypterPath);

// ตรวจสอบว่ามีเมธอด previousKeys() อยู่แล้วหรือไม่
if (strpos($content, 'function previousKeys(') === false) {
    echo "ไม่พบเมธอด previousKeys() กำลังเพิ่มเข้าไป...\n";
    
    // แรกทาง ลองค้นหาเมธอด getPreviousKeys() เพื่อเพิ่ม previousKeys() ไว้ใกล้ๆ กัน
    if (strpos($content, 'function getPreviousKeys()') !== false) {
        // เพิ่ม previousKeys() หลังจาก getPreviousKeys()
        $content = str_replace(
            'function getPreviousKeys()
    {
        return [];
    }',
            'function getPreviousKeys()
    {
        return [];
    }
    
    /**
     * Get the encryption keys that were previously used.
     *
     * @return array
     */
    public function previousKeys()
    {
        return $this->getPreviousKeys();
    }',
            $content
        );
        echo "✅ เพิ่มเมธอด previousKeys() เรียบร้อยแล้ว\n";
    } else {
        // ถ้าไม่เจอ getPreviousKeys() ให้เพิ่มไว้ในส่วนท้ายของคลาส
        $content = preg_replace(
            '/}(\s*)$/',
            "    /**
     * Get the encryption keys that were previously used.
     *
     * @return array
     */
    public function previousKeys()
    {
        return [];
    }
}\$1",
            $content
        );
        echo "✅ เพิ่มเมธอด previousKeys() ที่ท้ายคลาสเรียบร้อยแล้ว\n";
    }
} else {
    echo "✅ เมธอด previousKeys() มีอยู่แล้ว\n";
}

// บันทึกไฟล์
file_put_contents($encrypterPath, $content);
echo "✅ บันทึกการเปลี่ยนแปลงเรียบร้อยแล้ว\n\n";

// ตรวจสอบว่ามีไฟล์ EncryptionServiceProvider.php หรือไม่
$serviceProviderPath = __DIR__ . '/vendor/laravel/framework/src/Illuminate/Encryption/EncryptionServiceProvider.php';

if (file_exists($serviceProviderPath)) {
    echo "กำลังตรวจสอบ EncryptionServiceProvider.php...\n";
    $serviceProviderContent = file_get_contents($serviceProviderPath);
    
    // ตรวจสอบว่ามีการเรียกใช้ previousKeys() หรือไม่
    if (strpos($serviceProviderContent, 'previousKeys()') !== false) {
        echo "✅ พบการใช้งานเมธอด previousKeys() ในไฟล์ EncryptionServiceProvider.php\n";
    } else {
        echo "❓ ไม่พบการเรียกใช้เมธอด previousKeys() ในไฟล์ EncryptionServiceProvider.php\n";
        echo "   อาจมีการเรียกใช้ในไฟล์อื่น ซึ่งเราได้เพิ่มเมธอดนี้แล้วจึงไม่ควรมีปัญหา\n";
    }
}

// เคลียร์แคช
echo "\nกำลังเคลียร์แคช...\n";
passthru('php artisan config:clear');
passthru('php artisan cache:clear');
passthru('php artisan view:clear');
passthru('composer dump-autoload -o');

echo "\n=========================================\n";
echo "การแก้ไขเสร็จสมบูรณ์! กรุณารีสตาร์ทเซิร์ฟเวอร์:\n";
echo "php artisan serve\n";
echo "=========================================\n";
