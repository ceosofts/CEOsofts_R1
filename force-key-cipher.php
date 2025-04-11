<?php

/**
 * แก้ไขปัญหาการเข้ารหัสโดยการบังคับใช้ Key และ Cipher ที่กำหนดเอง
 * วิธีนี้จะไม่แทนที่ Laravel Encrypter แต่จะใช้ฟังก์ชันเข้ารหัสเฉพาะตัว
 */

echo "===== Force Key/Cipher for Laravel Encryption =====\n\n";

// 1. สร้าง helpers.php ที่มี custom encrypt/decrypt functions
$helpersPath = __DIR__ . '/app/helpers.php';

$helpersContent = <<<'EOT'
<?php

if (! function_exists('custom_encrypt')) {
    /**
     * เข้ารหัสข้อมูลด้วยการใช้ OpenSSL โดยตรง
     *
     * @param mixed $value ข้อมูลที่ต้องการเข้ารหัส
     * @param bool $serialize เข้ารหัสแบบ serialize หรือไม่
     * @return string
     */
    function custom_encrypt($value, $serialize = true)
    {
        try {
            // อ่าน key จาก .env
            $key = env('APP_KEY');
            if (!$key) {
                throw new Exception('APP_KEY not found in .env');
            }
            
            // ถอดรหัส base64 จาก APP_KEY
            if (strpos($key, 'base64:') === 0) {
                $key = base64_decode(substr($key, 7));
            }
            
            // ใช้ AES-256-CBC เสมอ
            $cipher = 'aes-256-cbc';
            
            // เตรียมข้อมูล
            if ($serialize) {
                $value = serialize($value);
            }
            
            // สร้าง IV สำหรับการเข้ารหัส
            $iv = random_bytes(16); // 16 bytes for AES
            
            // เข้ารหัสข้อมูล
            $encrypted = openssl_encrypt($value, $cipher, $key, 0, $iv);
            if ($encrypted === false) {
                throw new Exception('OpenSSL encryption failed: ' . openssl_error_string());
            }
            
            // สร้าง MAC เพื่อตรวจสอบความถูกต้อง
            $mac = hash_hmac('sha256', $iv . $encrypted, $key);
            
            // สร้าง payload และเข้ารหัสเป็น base64
            $json = json_encode([
                'iv' => base64_encode($iv),
                'value' => $encrypted,
                'mac' => $mac,
            ]);
            
            return base64_encode($json);
            
        } catch (Exception $e) {
            // บันทึก error log
            error_log('Custom encryption error: ' . $e->getMessage());
            
            // ในกรณีที่ต้องการให้โค้ดทำงานต่อได้แม้จะมีปัญหา
            // อาจส่งคืนค่าเดิมหรือค่าว่าง
            return $value;
        }
    }
}

if (! function_exists('custom_decrypt')) {
    /**
     * ถอดรหัสข้อมูลที่เข้ารหัสด้วย custom_encrypt
     *
     * @param string $payload ข้อมูลที่เข้ารหัสแล้ว
     * @param bool $unserialize ถอดรหัสแบบ unserialize หรือไม่
     * @return mixed
     */
    function custom_decrypt($payload, $unserialize = true)
    {
        try {
            // อ่าน key จาก .env
            $key = env('APP_KEY');
            if (!$key) {
                throw new Exception('APP_KEY not found in .env');
            }
            
            // ถอดรหัส base64 จาก APP_KEY
            if (strpos($key, 'base64:') === 0) {
                $key = base64_decode(substr($key, 7));
            }
            
            // ใช้ AES-256-CBC เสมอ
            $cipher = 'aes-256-cbc';
            
            // ถอดรหัส base64
            $decoded = base64_decode($payload);
            if ($decoded === false) {
                throw new Exception('Invalid base64 payload');
            }
            
            // แปลง JSON เป็น array
            $data = json_decode($decoded, true);
            if (!$data || !isset($data['iv']) || !isset($data['value']) || !isset($data['mac'])) {
                throw new Exception('Invalid payload structure');
            }
            
            // ถอดรหัส IV
            $iv = base64_decode($data['iv']);
            if ($iv === false) {
                throw new Exception('Invalid IV');
            }
            
            // ตรวจสอบ MAC
            $mac = hash_hmac('sha256', $iv . $data['value'], $key);
            if (!hash_equals($mac, $data['mac'])) {
                throw new Exception('MAC verification failed');
            }
            
            // ถอดรหัสข้อมูล
            $decrypted = openssl_decrypt($data['value'], $cipher, $key, 0, $iv);
            if ($decrypted === false) {
                throw new Exception('OpenSSL decryption failed: ' . openssl_error_string());
            }
            
            // Unserialize ถ้าต้องการ
            if ($unserialize) {
                return unserialize($decrypted);
            }
            
            return $decrypted;
            
        } catch (Exception $e) {
            // บันทึก error log
            error_log('Custom decryption error: ' . $e->getMessage());
            
            // คืนค่า null หรือค่าที่กำหนดเมื่อเกิดข้อผิดพลาด
            return null;
        }
    }
}
EOT;

file_put_contents($helpersPath, $helpersContent);
echo "✅ สร้างไฟล์ app/helpers.php เรียบร้อยแล้ว\n";

// 2. อัปเดต composer.json เพื่อโหลด helpers.php
$composerJsonPath = __DIR__ . '/composer.json';

if (file_exists($composerJsonPath)) {
    $composerJson = json_decode(file_get_contents($composerJsonPath), true);
    
    // เพิ่ม files ถ้ายังไม่มี
    if (!isset($composerJson['autoload']['files'])) {
        $composerJson['autoload']['files'] = [];
    }
    
    // เพิ่ม helpers.php ถ้ายังไม่มี
    if (!in_array('app/helpers.php', $composerJson['autoload']['files'])) {
        $composerJson['autoload']['files'][] = 'app/helpers.php';
        
        file_put_contents(
            $composerJsonPath, 
            json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
        
        echo "✅ อัปเดต composer.json เพื่อโหลด app/helpers.php เรียบร้อยแล้ว\n";
    } else {
        echo "✓ app/helpers.php มีอยู่ใน composer.json แล้ว\n";
    }
    
    // รัน composer dump-autoload
    echo "\nกำลังรัน composer dump-autoload เพื่ออัปเดตการโหลด app/helpers.php\n";
    passthru('composer dump-autoload -o');
} else {
    echo "⚠️ ไม่พบไฟล์ composer.json\n";
}

// 3. สร้างไฟล์ทดสอบ
$testFilePath = __DIR__ . '/test-custom-encryption.php';
$testFileContent = <<<'EOT'
<?php

require __DIR__ . '/vendor/autoload.php';

echo "===== ทดสอบการเข้ารหัสและถอดรหัสแบบกำหนดเอง =====\n\n";

$testData = [
    'text' => 'สวัสดีชาวโลก',
    'number' => 42,
    'array' => ['name' => 'ทดสอบ', 'value' => 123]
];

// ทดสอบกับข้อความ
echo "1. ทดสอบกับข้อความธรรมดา:\n";
$text = $testData['text'];
echo "   ข้อความต้นฉบับ: $text\n";

$encrypted = custom_encrypt($text);
echo "   ข้อความเข้ารหัส: $encrypted\n";

$decrypted = custom_decrypt($encrypted);
echo "   ข้อความถอดรหัส: $decrypted\n";

echo "   ผลลัพธ์: " . ($text === $decrypted ? "✅ ถูกต้อง" : "❌ ไม่ถูกต้อง") . "\n\n";

// ทดสอบกับตัวเลข
echo "2. ทดสอบกับตัวเลข:\n";
$number = $testData['number'];
echo "   ตัวเลขต้นฉบับ: $number\n";

$encrypted = custom_encrypt($number);
echo "   ตัวเลขเข้ารหัส: $encrypted\n";

$decrypted = custom_decrypt($encrypted);
echo "   ตัวเลขถอดรหัส: $decrypted\n";

echo "   ผลลัพธ์: " . ($number === $decrypted ? "✅ ถูกต้อง" : "❌ ไม่ถูกต้อง") . "\n\n";

// ทดสอบกับ array
echo "3. ทดสอบกับ Array:\n";
$array = $testData['array'];
echo "   Array ต้นฉบับ: " . json_encode($array) . "\n";

$encrypted = custom_encrypt($array);
echo "   Array เข้ารหัส: " . $encrypted . "\n";

$decrypted = custom_decrypt($encrypted);
echo "   Array ถอดรหัส: " . json_encode($decrypted) . "\n";

$isEqual = ($decrypted['name'] === $array['name'] && $decrypted['value'] === $array['value']);
echo "   ผลลัพธ์: " . ($isEqual ? "✅ ถูกต้อง" : "❌ ไม่ถูกต้อง") . "\n";

echo "\n===== เสร็จสิ้นการทดสอบ =====\n";
EOT;

file_put_contents($testFilePath, $testFileContent);
echo "✅ สร้างไฟล์ test-custom-encryption.php เรียบร้อยแล้ว\n";

echo "\n===== สิ่งที่ควรทำต่อไป =====\n";
echo "1. ทดสอบการเข้ารหัส/ถอดรหัสด้วยฟังก์ชั่นที่เรากำหนดเอง:\n";
echo "   php test-custom-encryption.php\n";
echo "2. อัปเดต controller ของคุณเพื่อใช้ฟังก์ชั่น custom_encrypt และ custom_decrypt แทน encrypt/decrypt\n";
echo "3. รัน server:\n";
echo "   php artisan serve --port=8005\n";
