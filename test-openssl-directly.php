<?php

/**
 * ทดสอบ OpenSSL โดยตรง เพื่อตรวจสอบว่า PHP OpenSSL extension ทำงานได้ถูกต้องหรือไม่
 */

echo "===== OpenSSL Direct Test =====\n\n";

// 1. ตรวจสอบ OpenSSL Extension
echo "1. ตรวจสอบ OpenSSL Extension\n";
if (!extension_loaded('openssl')) {
    echo "❌ OpenSSL extension ไม่ได้เปิดใช้งาน\n";
    exit(1);
} else {
    echo "✅ OpenSSL extension เปิดใช้งานอยู่\n";
    echo "   OpenSSL Version: " . OPENSSL_VERSION_TEXT . "\n";
}

// 2. สร้าง key และดูความยาว
echo "\n2. สร้าง Key และตรวจสอบความยาว\n";
echo "   สร้าง key ขนาด 32 bytes สำหรับ AES-256-CBC...\n";
$key = random_bytes(32);
echo "   ✅ สร้างเรียบร้อยแล้ว - ความยาว: " . strlen($key) . " bytes\n";
echo "   Key (hex): " . bin2hex($key) . "\n";

// 3. สร้าง Data สำหรับทดสอบ
$data = "ทดสอบการเข้ารหัสด้วย OpenSSL โดยตรง";
echo "\n3. ข้อมูลทดสอบ: '$data'\n";
echo "   ความยาว: " . strlen($data) . " bytes\n";

// 4. ทดสอบ openssl_encrypt โดยตรง (ไม่ผ่าน Laravel)
echo "\n4. ทดสอบ openssl_encrypt โดยตรง\n";
try {
    $iv = random_bytes(16);
    echo "   IV (hex): " . bin2hex($iv) . "\n";

    echo "   กำลังเข้ารหัสด้วย aes-256-cbc...\n";
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
    
    if ($encrypted === false) {
        echo "❌ เข้ารหัสไม่สำเร็จ: " . openssl_error_string() . "\n";
    } else {
        echo "✅ เข้ารหัสสำเร็จ: " . $encrypted . "\n";
        
        echo "\n5. ทดสอบ openssl_decrypt โดยตรง\n";
        $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
        
        if ($decrypted === false) {
            echo "❌ ถอดรหัสไม่สำเร็จ: " . openssl_error_string() . "\n";
        } else {
            echo "✅ ถอดรหัสสำเร็จ: " . $decrypted . "\n";
            
            if ($decrypted === $data) {
                echo "✅ ข้อมูลหลังถอดรหัสตรงกับข้อมูลต้นฉบับ\n";
            } else {
                echo "❌ ข้อมูลหลังถอดรหัสไม่ตรงกับข้อมูลต้นฉบับ\n";
            }
        }
    }
} catch (Exception $e) {
    echo "❌ เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
}

// 5. รองรับ cipher ทั้งหมด
echo "\n6. รายการ cipher ที่ OpenSSL รองรับ\n";
$ciphers = openssl_get_cipher_methods();
echo "   OpenSSL รองรับ cipher ทั้งหมด " . count($ciphers) . " ชนิด\n";
echo "   aes-256-cbc รองรับหรือไม่? " . (in_array('aes-256-cbc', $ciphers) ? "✅ รองรับ" : "❌ ไม่รองรับ") . "\n";

// 7. ทดสอบด้วย key ของ Laravel
echo "\n7. ทดสอบด้วย key จาก .env ของ Laravel\n";
if (file_exists(__DIR__ . '/.env')) {
    $envContent = file_get_contents(__DIR__ . '/.env');
    if (preg_match('/APP_KEY=base64:([^\s\n]+)/', $envContent, $matches)) {
        $appKey = base64_decode($matches[1]);
        echo "   พบ APP_KEY ใน .env - ความยาวหลัง decode: " . strlen($appKey) . " bytes\n";
        echo "   Key (hex): " . bin2hex($appKey) . "\n";
        
        try {
            echo "   กำลังเข้ารหัสด้วย APP_KEY จาก .env...\n";
            $iv = random_bytes(16);
            $encrypted = openssl_encrypt($data, 'aes-256-cbc', $appKey, 0, $iv);
            
            if ($encrypted === false) {
                echo "❌ เข้ารหัสไม่สำเร็จ: " . openssl_error_string() . "\n";
            } else {
                echo "✅ เข้ารหัสสำเร็จ: " . $encrypted . "\n";
                
                echo "   กำลังถอดรหัส...\n";
                $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', $appKey, 0, $iv);
                
                if ($decrypted === false) {
                    echo "❌ ถอดรหัสไม่สำเร็จ: " . openssl_error_string() . "\n";
                } else {
                    echo "✅ ถอดรหัสสำเร็จ: " . $decrypted . "\n";
                    
                    if ($decrypted === $data) {
                        echo "✅ ข้อมูลหลังถอดรหัสตรงกับข้อมูลต้นฉบับ\n";
                    } else {
                        echo "❌ ข้อมูลหลังถอดรหัสไม่ตรงกับข้อมูลต้นฉบับ\n";
                    }
                }
            }
        } catch (Exception $e) {
            echo "❌ เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
        }
    }
}

// 8. ทดสอบเฉพาะการสร้าง IV
echo "\n8. ทดสอบการสร้าง IV\n";
try {
    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
    echo "   ความยาว IV สำหรับ aes-256-cbc: $ivLength bytes\n";
    
    $iv = openssl_random_pseudo_bytes($ivLength);
    echo "   ✅ สามารถสร้าง IV ได้: " . bin2hex($iv) . "\n";
} catch (Exception $e) {
    echo "❌ เกิดข้อผิดพลาดในการสร้าง IV: " . $e->getMessage() . "\n";
}

echo "\n===== วิเคราะห์ปัญหา =====\n";
echo "หากทุกขั้นตอนด้านบนผ่าน แต่ Laravel ยังมีปัญหา แสดงว่าปัญหาอยู่ที่:\n";
echo "1. ✓ OpenSSL ทำงานได้ดี\n";
echo "2. ✓ Key และ Cipher ถูกต้อง\n"; 
echo "3. ❌ แต่ Laravel Encrypter มีปัญหาที่โค้ด หรือการเรียกใช้งาน\n";

echo "\n===== แนวทางแก้ไข =====\n";
echo "1. สร้าง Custom Encrypter ที่ใช้ OpenSSL โดยตรง:\n";
echo "   $ php create-simple-encrypter.php\n";
echo "2. หรือ แก้ไข Controller เพื่อใช้ custom_encrypt() แทน encrypt():\n";
echo "   - ค้นหาการใช้ encrypt() ในโค้ดแล้วแทนที่ด้วย custom_encrypt()\n";
