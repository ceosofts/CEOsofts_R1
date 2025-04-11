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