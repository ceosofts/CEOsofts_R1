<?php

/**
 * สคริปต์ตรวจสอบการ Override Config ใน Laravel
 * แสดงการตั้งค่าทั้งหมดที่เกี่ยวข้องกับ Encryption
 */

echo "Laravel Config Override Checker\n";
echo "=============================\n\n";

// ตรวจสอบว่าเป็นโปรเจค Laravel หรือไม่
if (!file_exists(__DIR__ . '/vendor/autoload.php') || !file_exists(__DIR__ . '/artisan')) {
    echo "❌ ไม่พบไฟล์หลักของ Laravel ตรวจสอบว่าคุณอยู่ในโฟลเดอร์โปรเจค Laravel หรือไม่\n";
    exit(1);
}

// โหลด Laravel Application
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. ตรวจสอบค่า Config จาก Laravel
echo "1. ค่าที่ Laravel ใช้จริง (จาก Config Repository)\n";
echo "------------------------------------------\n";
$config = app('config');

echo "app.cipher: " . $config->get('app.cipher') . "\n";
echo "app.key: " . ($config->get('app.key') ? "[ถูกตั้งค่า]" : "[ไม่ได้ตั้งค่า]") . "\n";
echo "app.providers: [" . count($config->get('app.providers')) . " providers]\n";

// 2. ตรวจสอบค่าจากไฟล์ .env
echo "\n2. ค่าที่ตั้งไว้ในไฟล์ .env\n";
echo "-------------------------\n";

if (file_exists(__DIR__ . '/.env')) {
    $envContent = file_get_contents(__DIR__ . '/.env');
    preg_match('/^APP_KEY=(.*)$/m', $envContent, $matches);
    
    if (isset($matches[1])) {
        $appKey = trim($matches[1]);
        echo "APP_KEY: " . $appKey . "\n";
        
        // ตรวจสอบความยาวของ key
        if (strpos($appKey, 'base64:') === 0) {
            $keyData = base64_decode(substr($appKey, 7));
            echo "Key length: " . strlen($keyData) . " bytes\n";
            
            if (strlen($keyData) !== 32) {
                echo "⚠️ ความยาวของ key ไม่ถูกต้อง (ควรเป็น 32 bytes)\n";
            }
        } else {
            echo "⚠️ APP_KEY ไม่ได้อยู่ในรูปแบบ base64:\n";
        }
    } else {
        echo "❌ ไม่พบ APP_KEY ในไฟล์ .env\n";
    }
} else {
    echo "❌ ไม่พบไฟล์ .env\n";
}

// 3. ตรวจสอบค่าจากไฟล์ config/app.php
echo "\n3. ค่าที่ตั้งไว้ในไฟล์ config/app.php\n";
echo "--------------------------------\n";

if (file_exists(__DIR__ . '/config/app.php')) {
    $appConfigContent = file_get_contents(__DIR__ . '/config/app.php');
    
    preg_match("/'cipher'\s*=>\s*'([^']*)'/", $appConfigContent, $cipherMatches);
    if (isset($cipherMatches[1])) {
        echo "cipher: " . $cipherMatches[1] . "\n";
    } else {
        echo "❌ ไม่พบการกำหนดค่า cipher ในไฟล์ config/app.php\n";
    }
    
    preg_match("/'key'\s*=>\s*([^,]*)/", $appConfigContent, $keyMatches);
    if (isset($keyMatches[1])) {
        echo "key: " . trim($keyMatches[1]) . "\n";
    } else {
        echo "❌ ไม่พบการกำหนดค่า key ในไฟล์ config/app.php\n";
    }
} else {
    echo "❌ ไม่พบไฟล์ config/app.php\n";
}

// 4. ตรวจสอบ Service Providers ที่อาจ Override Config
echo "\n4. Service Providers ที่อาจ Override Config\n";
echo "-----------------------------------------\n";

$appServiceProviderPath = __DIR__ . '/app/Providers/AppServiceProvider.php';
if (file_exists($appServiceProviderPath)) {
    $appServiceProviderContent = file_get_contents($appServiceProviderPath);
    if (strpos($appServiceProviderContent, 'config(') !== false || 
        strpos($appServiceProviderContent, 'Config::') !== false) {
        echo "⚠️ พบการใช้ config() หรือ Config:: ใน AppServiceProvider\n";
        
        // แสดงบรรทัดที่อาจมีการแก้ไข config
        preg_match_all('/(.*config\(.*\)|.*Config::.*)/m', $appServiceProviderContent, $configLines);
        if (!empty($configLines[0])) {
            echo "พบการใช้งาน config ในบรรทัดต่อไปนี้:\n";
            foreach ($configLines[0] as $line) {
                echo "- " . trim($line) . "\n";
            }
        }
    } else {
        echo "✅ ไม่พบการ override config ใน AppServiceProvider\n";
    }
} else {
    echo "⚠️ ไม่พบไฟล์ AppServiceProvider.php\n";
}

// 5. ตรวจสอบ Service Container ว่ามีการ Bind EncryptionServiceProvider หรือไม่
echo "\n5. การ Bind EncryptionServiceProvider\n";
echo "--------------------------------\n";

try {
    $encrypter = app('encrypter');
    $cipher = $encrypter->getConfig('cipher');
    echo "Encrypter cipher: " . $cipher . "\n";

    // แสดง class ของ encrypter
    echo "Encrypter class: " . get_class($encrypter) . "\n";
    
    // แสดงเมธอด getConfig
    if (method_exists($encrypter, 'getConfig')) {
        echo "Supported ciphers: " . json_encode($encrypter->getConfig('supportedCiphers')) . "\n";
    } else {
        $reflection = new ReflectionClass($encrypter);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE);
        
        foreach ($properties as $property) {
            $property->setAccessible(true);
            if ($property->getName() === 'cipher' || $property->getName() === 'supportedCiphers') {
                echo $property->getName() . ": " . json_encode($property->getValue($encrypter)) . "\n";
            }
        }
    }
} catch (Exception $e) {
    echo "❌ เกิดข้อผิดพลาดขณะตรวจสอบ Encrypter: " . $e->getMessage() . "\n";
}

// 6. สรุปและคำแนะนำ
echo "\n6. สรุปและคำแนะนำ\n";
echo "-------------------\n";

try {
    // ทดสอบการเข้ารหัส
    $testString = "ทดสอบการเข้ารหัส";
    $encrypted = encrypt($testString);
    $decrypted = decrypt($encrypted);
    
    if ($decrypted === $testString) {
        echo "✅ ทดสอบการเข้ารหัสสำเร็จ\n";
    } else {
        echo "❌ ทดสอบการเข้ารหัสล้มเหลว ข้อความที่ถอดรหัสไม่ตรงกับข้อความต้นฉบับ\n";
    }
} catch (Exception $e) {
    echo "❌ เกิดข้อผิดพลาดขณะทดสอบการเข้ารหัส: " . $e->getMessage() . "\n";
    
    echo "\nคำแนะนำในการแก้ไข:\n";
    echo "1. รันคำสั่ง: php artisan key:generate\n";
    echo "2. แก้ไขไฟล์ config/app.php ให้มีค่า cipher: 'aes-256-cbc'\n";
    echo "3. หรือรันสคริปต์แก้ไขอัตโนมัติ: php deep-cipher-fix.php\n";
    echo "4. รีสตาร์ท PHP server: php artisan serve\n";
}
