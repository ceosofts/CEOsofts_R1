<?php

/**
 * สคริปต์แก้ไขปัญหา encryption key
 * โดยการสร้าง key ใหม่และทดสอบว่าใช้ได้หรือไม่
 */

echo "===== Laravel App Key Fixer =====\n\n";

// 1. ตรวจสอบและสำรองไฟล์ .env
echo "1. ตรวจสอบและสำรอง .env...\n";

$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    // ถ้าไม่มีไฟล์ .env ให้ลองคัดลอกจาก .env.example
    if (file_exists(__DIR__ . '/.env.example')) {
        copy(__DIR__ . '/.env.example', $envPath);
        echo "   ✓ คัดลอกไฟล์ .env จาก .env.example\n";
    } else {
        // สร้างไฟล์ .env ขั้นต่ำ
        file_put_contents($envPath, "APP_NAME=Laravel\nAPP_ENV=local\nAPP_DEBUG=true\nAPP_URL=http://localhost\n\nDB_CONNECTION=sqlite\n\nSESSION_DRIVER=file\nCACHE_STORE=file\n");
        echo "   ✓ สร้างไฟล์ .env ขั้นต่ำ\n";
    }
} else {
    // สำรองไฟล์ .env
    $backupDir = __DIR__ . '/backups';
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0755, true);
    }
    
    $timestamp = date('YmdHis');
    $backupPath = "{$backupDir}/.env.{$timestamp}.bak";
    copy($envPath, $backupPath);
    echo "   ✓ สำรองไฟล์ .env ไว้ที่ {$backupPath}\n";
}

// 2. สร้าง key ใหม่
echo "\n2. สร้าง App Key ใหม่...\n";

// สร้าง key ใหม่
$key = 'base64:' . base64_encode(random_bytes(32));
echo "   ✓ สร้าง key ใหม่: {$key}\n";

// อ่านไฟล์ .env เดิม
$envContent = file_get_contents($envPath);

// ตรวจสอบว่ามี APP_KEY หรือไม่
if (preg_match('/^APP_KEY=/m', $envContent)) {
    // มี APP_KEY อยู่แล้ว ให้แทนที่
    $envContent = preg_replace('/^APP_KEY=.*$/m', "APP_KEY={$key}", $envContent);
    echo "   ✓ แทนที่ค่า APP_KEY เดิมด้วยค่าใหม่\n";
} else {
    // ไม่มี APP_KEY ให้เพิ่มเข้าไป
    $envContent .= "\nAPP_KEY={$key}\n";
    echo "   ✓ เพิ่มค่า APP_KEY ใหม่\n";
}

// รับรองว่ามี cipher ที่ถูกต้อง
if (!preg_match('/^CIPHER=/m', $envContent)) {
    $envContent .= "\nCIPHER=aes-256-cbc\n";
    echo "   ✓ เพิ่มค่า CIPHER=aes-256-cbc\n";
}

// บันทึกไฟล์ .env
file_put_contents($envPath, $envContent);
echo "   ✓ บันทึกไฟล์ .env เรียบร้อยแล้ว\n";

// 3. ทดสอบ encryption
echo "\n3. ทดสอบ encryption ด้วย OpenSSL โดยตรง...\n";

// ดึง key จาก .env
$keyMatches = [];
preg_match('/^APP_KEY=base64:(.+)$/m', $envContent, $keyMatches);
if (isset($keyMatches[1])) {
    $keyBase64 = $keyMatches[1];
    $binaryKey = base64_decode($keyBase64);
    
    echo "   Decoded key length: " . strlen($binaryKey) . " bytes\n";
    
    // ทดสอบ encryption ด้วย OpenSSL โดยตรง
    $data = "Test encryption data";
    $iv = random_bytes(16); // 16 bytes for AES
    
    echo "   Testing OpenSSL encryption... ";
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $binaryKey, 0, $iv);
    
    if ($encrypted === false) {
        echo "FAILED\n   Error: " . openssl_error_string() . "\n";
    } else {
        echo "SUCCESS\n";
        
        // ทดสอบ decryption
        echo "   Testing OpenSSL decryption... ";
        $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $binaryKey, 0, $iv);
        
        if ($decrypted === false) {
            echo "FAILED\n   Error: " . openssl_error_string() . "\n";
        } else {
            echo "SUCCESS\n";
            
            if ($decrypted === $data) {
                echo "   ✓ การเข้ารหัสและถอดรหัสทำงานได้ถูกต้อง\n";
            } else {
                echo "   ❌ ข้อมูลที่ถอดรหัสได้ไม่ตรงกับข้อมูลเดิม\n";
            }
        }
    }
} else {
    echo "   ❌ ไม่สามารถดึงค่า APP_KEY จากไฟล์ .env ได้\n";
}

// 4. สร้างไฟล์ทดสอบ encrypt/decrypt
echo "\n4. สร้างไฟล์ทดสอบ encrypt/decrypt...\n";

$testFilePath = __DIR__ . '/test-encryption.php';
$testFileContent = <<<'EOT'
<?php

/**
 * ทดสอบการเข้ารหัสและถอดรหัสด้วย APP_KEY ปัจจุบัน
 */

require __DIR__ . '/vendor/autoload.php';

echo "===== Laravel Encryption Test =====\n\n";

// 1. ทดสอบด้วย OpenSSL โดยตรง
echo "1. Testing with OpenSSL directly:\n";

// ดึงค่า key จาก .env
$envContent = file_get_contents(__DIR__ . '/.env');
$keyMatches = [];
preg_match('/^APP_KEY=base64:(.+)$/m', $envContent, $keyMatches);

if (isset($keyMatches[1])) {
    $keyBase64 = $keyMatches[1];
    $binaryKey = base64_decode($keyBase64);
    
    echo "   Key length: " . strlen($binaryKey) . " bytes\n";
    
    // ทดสอบ encryption
    $data = "Test encryption data";
    $iv = random_bytes(16); // 16 bytes for AES
    
    echo "   Testing encryption... ";
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $binaryKey, 0, $iv);
    
    if ($encrypted === false) {
        echo "FAILED\n   Error: " . openssl_error_string() . "\n";
    } else {
        echo "SUCCESS\n";
        
        // ทดสอบ decryption
        echo "   Testing decryption... ";
        $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $binaryKey, 0, $iv);
        
        if ($decrypted === false) {
            echo "FAILED\n   Error: " . openssl_error_string() . "\n";
        } else {
            echo "SUCCESS\n";
            
            if ($decrypted === $data) {
                echo "   ✅ OpenSSL encryption/decryption works correctly\n";
            } else {
                echo "   ❌ Decrypted data doesn't match original\n";
            }
        }
    }
} else {
    echo "   ❌ Couldn't find APP_KEY in .env file\n";
}

// 2. ทดสอบผ่าน Laravel Encrypter class โดยตรง
echo "\n2. Testing with Laravel Encrypter class directly:\n";

try {
    // ดึงค่า key จาก .env
    $envContent = file_get_contents(__DIR__ . '/.env');
    $keyMatches = [];
    preg_match('/^APP_KEY=base64:(.+)$/m', $envContent, $keyMatches);
    
    if (isset($keyMatches[1])) {
        $keyBase64 = $keyMatches[1];
        $binaryKey = base64_decode($keyBase64);
        
        echo "   Creating Encrypter instance... ";
        $encrypter = new \Illuminate\Encryption\Encrypter($binaryKey, 'aes-256-cbc');
        echo "SUCCESS\n";
        
        $data = "Test Laravel encryption";
        
        echo "   Testing encryption... ";
        $encrypted = $encrypter->encrypt($data);
        echo "SUCCESS\n";
        
        echo "   Testing decryption... ";
        $decrypted = $encrypter->decrypt($encrypted);
        echo "SUCCESS\n";
        
        if ($decrypted === $data) {
            echo "   ✅ Laravel Encrypter works correctly\n";
        } else {
            echo "   ❌ Decrypted data doesn't match original\n";
        }
    }
} catch (\Exception $e) {
    echo "   ❌ Exception: " . $e->getMessage() . "\n";
}

// 3. ตรวจสอบว่า Laravel สามารถเข้าถึงค่า APP_KEY ได้หรือไม่
echo "\n3. Checking if Laravel can access APP_KEY:\n";

try {
    // Simulate Laravel's dotenv loading
    echo "   Loading Laravel environment... \n";
    
    $app = new \Illuminate\Foundation\Application(__DIR__);
    
    // Load environment
    (new \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables($app))->bootstrap($app);
    
    echo "   APP_KEY from env(): " . substr(env('APP_KEY', 'NOT FOUND'), 0, 15) . "...\n";
    
    if (env('APP_KEY') === null) {
        echo "   ❌ Laravel cannot access APP_KEY\n";
    } else {
        echo "   ✅ Laravel can access APP_KEY\n";
    }
} catch (\Exception $e) {
    echo "   ❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n===== Test Complete =====\n";
echo "If all tests passed, your encryption setup should be working correctly.\n";
EOT;

file_put_contents($testFilePath, $testFileContent);
echo "   ✓ สร้างไฟล์ test-encryption.php เรียบร้อยแล้ว\n";

// 5. เคลียร์ cache ของ Laravel
echo "\n5. เคลียร์ cache ของ Laravel...\n";

$commands = [
    'php artisan config:clear',
    'php artisan cache:clear',
    'php artisan route:clear',
    'php artisan view:clear'
];

foreach ($commands as $command) {
    echo "   Running: $command\n";
    $output = [];
    exec($command, $output, $returnVar);
    
    if ($returnVar === 0) {
        echo "   ✓ สำเร็จ\n";
    } else {
        echo "   ⚠️ มีปัญหา: " . implode("\n", $output) . "\n";
    }
}

echo "\n===== การแก้ไขเสร็จสมบูรณ์! =====\n";
echo "คุณสามารถทดสอบการเข้ารหัส/ถอดรหัสด้วยคำสั่ง:\n";
echo "php test-encryption.php\n\n";
echo "หรือรันเซิร์ฟเวอร์เพื่อทดสอบ:\n";
echo "php artisan serve --port=8040\n";
