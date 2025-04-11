<?php

/**
 * สคริปต์ทดสอบการเข้ารหัสของ Laravel
 * เพื่อตรวจสอบว่าระบบเข้ารหัสทำงานได้ถูกต้อง
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Laravel Encryption Test\n";
echo "=====================\n\n";

try {
    // ทดสอบการเข้ารหัสและถอดรหัสข้อความ
    $original = "ทดสอบการเข้ารหัส";
    
    echo "ข้อความต้นฉบับ: $original\n";

    // เข้ารหัส
    $encrypted = \Illuminate\Support\Facades\Crypt::encrypt($original);
    echo "ข้อความที่เข้ารหัส: $encrypted\n";

    // ถอดรหัส
    $decrypted = \Illuminate\Support\Facades\Crypt::decrypt($encrypted);
    echo "ข้อความที่ถอดรหัส: $decrypted\n\n";

    // ตรวจสอบว่าข้อความที่ถอดรหัสตรงกับข้อความต้นฉบับ
    if ($decrypted === $original) {
        echo "✅ การเข้ารหัสและถอดรหัสทำงานได้ถูกต้อง\n";
    } else {
        echo "❌ การเข้ารหัสและถอดรหัสไม่ถูกต้อง\n";
    }

    // แสดงข้อมูลเพิ่มเติมเกี่ยวกับการตั้งค่าการเข้ารหัส
    $config = config('app.cipher');
    $key = config('app.key');
    
    echo "\nข้อมูลการตั้งค่าการเข้ารหัส:\n";
    echo "Cipher: $config\n";
    echo "Key: " . (empty($key) ? "ไม่มีค่า" : "[มีค่า]") . "\n";
    
    // แสดง key length
    if (!empty($key) && strpos($key, 'base64:') === 0) {
        $decodedKey = base64_decode(substr($key, 7));
        echo "Key length: " . strlen($decodedKey) . " bytes\n";
    } else if (!empty($key)) {
        echo "Key format: ไม่ถูกต้อง (ควรขึ้นต้นด้วย 'base64:')\n";
    }
    
} catch (\Exception $e) {
    echo "❌ เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
    echo "รายละเอียด: " . $e->getTraceAsString() . "\n";
    
    // ตรวจสอบสาเหตุของข้อผิดพลาด
    if (strpos($e->getMessage(), 'Unsupported cipher') !== false) {
        echo "\nสาเหตุ: ค่า cipher ไม่ถูกต้อง\n";
        echo "แนะนำให้ใช้คำสั่งต่อไปนี้:\n";
        echo "php reset-encryption-settings.php\n";
    } else if (strpos($e->getMessage(), 'incorrect key length') !== false) {
        echo "\nสาเหตุ: ความยาวของคีย์ไม่ถูกต้อง\n";
        echo "แนะนำให้ใช้คำสั่งต่อไปนี้:\n";
        echo "php artisan key:generate\n";
    } else if (strpos($e->getMessage(), 'APP_KEY') !== false) {
        echo "\nสาเหตุ: ไม่พบ APP_KEY หรือค่าไม่ถูกต้อง\n";
        echo "แนะนำให้ใช้คำสั่งต่อไปนี้:\n";
        echo "php artisan key:generate\n";
    }
}
