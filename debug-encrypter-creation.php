<?php

/**
 * สคริปต์สำหรับตรวจสอบการสร้าง Encrypter ใน Laravel
 * โดยตามรอยการสร้าง instance จาก EncryptionServiceProvider
 */

echo "===== Debug Laravel Encrypter Creation =====\n\n";

// โหลด autoloader
require __DIR__ . '/vendor/autoload.php';

$encrypterPath = __DIR__ . '/vendor/laravel/framework/src/Illuminate/Encryption/Encrypter.php';
$serviceProviderPath = __DIR__ . '/vendor/laravel/framework/src/Illuminate/Encryption/EncryptionServiceProvider.php';

echo "1. ตรวจสอบไฟล์ที่เกี่ยวข้อง\n";
echo "   Encrypter.php: " . (file_exists($encrypterPath) ? "พบ" : "ไม่พบ") . "\n";
echo "   EncryptionServiceProvider.php: " . (file_exists($serviceProviderPath) ? "พบ" : "ไม่พบ") . "\n";

if (file_exists($encrypterPath) && file_exists($serviceProviderPath)) {
    // อ่านและวิเคราะห์โค้ด ServiceProvider
    $serviceProviderCode = file_get_contents($serviceProviderPath);
    echo "\n2. วิเคราะห์โค้ดใน EncryptionServiceProvider\n";
    
    if (preg_match('/register.*?function.*?\{(.*?)register(Encrypter|encrypter)/is', $serviceProviderCode, $matches)) {
        echo "   พบเมธอด register:\n";
        $registerMethod = $matches[1];
        $lines = array_map('trim', explode("\n", $registerMethod));
        foreach ($lines as $line) {
            if (!empty($line)) echo "   > $line\n";
        }
    }
    
    if (preg_match('/registerEncrypter.*?function.*?\{(.*?)\}/is', $serviceProviderCode, $matches)) {
        echo "\n   พบเมธอด registerEncrypter:\n";
        $registerEncrypterMethod = $matches[1];
        $lines = array_map('trim', explode("\n", $registerEncrypterMethod));
        foreach ($lines as $line) {
            if (!empty($line)) echo "   > $line\n";
        }
    }

    // อ่านและวิเคราะห์โค้ด Encrypter
    $encrypterCode = file_get_contents($encrypterPath);
    echo "\n3. วิเคราะห์โค้ดใน Encrypter class\n";
    
    // ดูส่วน constructor
    if (preg_match('/public\s+function\s+__construct.*?\{(.*?)\}/is', $encrypterCode, $matches)) {
        echo "   พบ constructor:\n";
        $constructorMethod = $matches[1];
        $lines = array_map('trim', explode("\n", $constructorMethod));
        foreach ($lines as $line) {
            if (!empty($line)) echo "   > $line\n";
        }
    }
    
    // ดูเมธอด supported
    if (preg_match('/public\s+static\s+function\s+supported.*?\{(.*?)\}/is', $encrypterCode, $matches)) {
        echo "\n   พบเมธอด supported:\n";
        $supportedMethod = $matches[1];
        $lines = array_map('trim', explode("\n", $supportedMethod));
        foreach ($lines as $line) {
            if (!empty($line)) echo "   > $line\n";
        }
    }
}

echo "\n4. ทดลองสร้าง Encrypter ด้วยวิธีเดียวกับที่ Laravel ใช้\n";

// อ่านค่า key จาก .env
if (file_exists(__DIR__ . '/.env')) {
    $envContent = file_get_contents(__DIR__ . '/.env');
    if (preg_match('/APP_KEY=(.+?)(\s|$)/m', $envContent, $matches)) {
        $appKey = $matches[1];
        echo "   APP_KEY: $appKey\n";
        
        // แปลงค่า key
        if (strpos($appKey, 'base64:') === 0) {
            $key = base64_decode(substr($appKey, 7));
            echo "   Key length: " . strlen($key) . " bytes\n";
            
            try {
                echo "   กำลังสร้าง Encrypter ด้วย key ความยาว " . strlen($key) . " bytes และ cipher 'aes-256-cbc'...\n";
                
                // พิมพ์ค่า key ในรูปแบบ hex เพื่อตรวจสอบ
                echo "   Key (hex): " . bin2hex($key) . "\n";
                
                $encrypter = new \Illuminate\Encryption\Encrypter($key, 'aes-256-cbc');
                echo "   ✅ สร้าง Encrypter instance สำเร็จ!\n";
                
                try {
                    $encrypted = $encrypter->encrypt("test");
                    echo "   ✅ ทดสอบเข้ารหัส: สำเร็จ!\n";
                    
                    $decrypted = $encrypter->decrypt($encrypted);
                    echo "   ✅ ทดสอบถอดรหัส: สำเร็จ! ผลลัพธ์: $decrypted\n";
                } catch (\Exception $e) {
                    echo "   ❌ เกิดข้อผิดพลาดในการเข้ารหัส/ถอดรหัส: " . $e->getMessage() . "\n";
                }
            } catch (\Exception $e) {
                echo "   ❌ เกิดข้อผิดพลาดในการสร้าง Encrypter: " . $e->getMessage() . "\n";
            }
            
            // ทดสอบ supported (เพื่อดูว่าจะผ่านหรือไม่)
            $supported = \Illuminate\Encryption\Encrypter::supported($key, 'aes-256-cbc');
            echo "   Encrypter::supported() ผลลัพธ์: " . ($supported ? "ผ่าน" : "ไม่ผ่าน") . "\n";
        }
    }
}

// ตรวจสอบว่ามีการเปลี่ยนแปลง Encrypter ในระบบหรือไม่ (เช่น มีไฟล์ในโปรเจค)
echo "\n5. ตรวจสอบการ override Encrypter class\n";
$customFiles = [
    'app/Encryption/Encrypter.php',
    'app/Providers/CustomEncryptionServiceProvider.php',
    'app/Providers/EncryptionServiceProvider.php',
];

foreach ($customFiles as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        echo "   ⚠️ พบไฟล์ที่อาจ override การทำงาน: $file\n";
        
        // อ่านเนื้อหาไฟล์และแสดงบางส่วน
        $content = file_get_contents($fullPath);
        echo "   เนื้อหาบางส่วน: " . substr($content, 0, 200) . "...\n";
    }
}

echo "\n===== คำแนะนำต่อไป =====\n";
echo "จากข้อมูลที่ได้ คุณควรทำสิ่งต่อไปนี้:\n";
echo "1. ใช้ script replace-encryption-provider.php เพื่อแทนที่ Encryption Provider\n";
echo "   * จะช่วยให้เราสามารถควบคุมวิธีการสร้าง Encrypter instance ได้\n";
echo "2. หรือใช้ force-key-cipher.php เพื่อบังคับใช้ key/cipher ที่ต้องการ\n";
echo "   * จะกำหนดฟังก์ชั่น custom_encrypt/custom_decrypt ให้ใช้แทน\n";
