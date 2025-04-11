<?php

/**
 * Script kiểm tra chi tiết hệ thống encryption của Laravel
 */

echo "Laravel Encryption System Checker\n";
echo "================================\n\n";

// Kiểm tra OpenSSL
echo "1. Kiểm tra OpenSSL\n";
echo "----------------\n";

$opensslVersion = OPENSSL_VERSION_TEXT;
$opensslModules = openssl_get_cipher_methods();

echo "OpenSSL Version: $opensslVersion\n";
echo "Số lượng cipher methods được hỗ trợ: " . count($opensslModules) . "\n";

// Kiểm tra aes-256-cbc có được hỗ trợ không
if (in_array('aes-256-cbc', $opensslModules)) {
    echo "✅ aes-256-cbc được hỗ trợ bởi OpenSSL\n";
} else {
    echo "❌ aes-256-cbc KHÔNG được hỗ trợ bởi OpenSSL\n";
    
    // Hiển thị các phương thức mã hóa tương tự
    $similar = array_filter($opensslModules, function($method) {
        return strpos($method, 'aes') !== false;
    });
    
    if (!empty($similar)) {
        echo "Phương thức mã hóa AES được hỗ trợ:\n";
        foreach ($similar as $method) {
            echo "- $method\n";
        }
    }
}

// Kiểm tra phiên bản Laravel
echo "\n2. Kiểm tra Laravel\n";
echo "----------------\n";

// Phải bootstrap Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$laravelVersion = app()->version();
echo "Laravel Version: $laravelVersion\n";

// Kiểm tra file config đã được load
echo "\n3. Kiểm tra config\n";
echo "----------------\n";

// Kiểm tra xem config có được load không
if (function_exists('config')) {
    $appKey = config('app.key');
    $cipher = config('app.cipher');
    
    echo "app.key: " . ($appKey ? "[SET]" : "[NOT SET]") . "\n";
    echo "app.cipher: " . ($cipher ?: "[NOT SET]") . "\n";
} else {
    echo "❌ Function config() không tồn tại\n";
}

// Kiểm tra environment thực tế
echo "\n4. Kiểm tra environment\n";
echo "--------------------\n";

$envKey = getenv('APP_KEY');
echo "Environment APP_KEY: " . ($envKey ? "[SET]" : "[NOT SET]") . "\n";

// Kiểm tra sâu hơn về encrypter
echo "\n5. Kiểm tra Encrypter\n";
echo "------------------\n";

try {
    $reflector = new ReflectionClass(Illuminate\Encryption\Encrypter::class);
    echo "Encrypter class exists: Yes\n";
    
    // Kiểm tra constructor
    $constructor = $reflector->getConstructor();
    if ($constructor) {
        echo "Constructor parameters:\n";
        foreach ($constructor->getParameters() as $param) {
            echo "- " . $param->getName() . "\n";
        }
    }
    
    // Kiểm tra các thuộc tính
    $supportedCiphersProperty = null;
    try {
        $supportedCiphersProperty = $reflector->getProperty('supportedCiphers');
        $supportedCiphersProperty->setAccessible(true);
        
        // Tạo instance của encrypter
        try {
            // Tạo một key ngẫu nhiên để test
            $key = random_bytes(32);
            $encrypter = new Illuminate\Encryption\Encrypter($key, 'aes-256-cbc');
            
            $supportedCiphers = $supportedCiphersProperty->getValue($encrypter);
            echo "Supported ciphers by Encrypter class: " . json_encode($supportedCiphers) . "\n";
        } catch (Exception $e) {
            echo "Cannot create Encrypter instance: " . $e->getMessage() . "\n";
        }
    } catch (Exception $e) {
        echo "Cannot access supportedCiphers property: " . $e->getMessage() . "\n";
    }
    
    // Kiểm tra app instance của encrypter
    try {
        $encrypter = app('encrypter');
        echo "App encrypter instance class: " . get_class($encrypter) . "\n";
        
        // Thử encrypt và decrypt
        $string = 'Test string';
        $encrypted = $encrypter->encrypt($string);
        echo "Encryption test: " . ($encrypted ? "Success" : "Failed") . "\n";
        
        $decrypted = $encrypter->decrypt($encrypted);
        echo "Decryption test: " . ($decrypted === $string ? "Success" : "Failed") . "\n";
    } catch (Exception $e) {
        echo "Error with app encrypter: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "Error inspecting Encrypter class: " . $e->getMessage() . "\n";
}

// Kiểm tra use cases
echo "\n6. Kiểm tra các use case\n";
echo "---------------------\n";

try {
    // Test encrypt/decrypt helper function
    $original = 'Test message';
    $encrypted = encrypt($original);
    echo "encrypt() function: " . ($encrypted ? "Working" : "Not working") . "\n";
    
    $decrypted = decrypt($encrypted);
    echo "decrypt() function: " . ($decrypted === $original ? "Working" : "Not working") . "\n";
} catch (Exception $e) {
    echo "Error with encryption helpers: " . $e->getMessage() . "\n";
}

// Kiểm tra file .env
echo "\n7. Kiểm tra file .env\n";
echo "------------------\n";

$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    
    preg_match('/APP_KEY=([^\s\n]+)/', $envContent, $matches);
    if (isset($matches[1])) {
        $appKey = $matches[1];
        echo "APP_KEY in .env: " . $appKey . "\n";
        
        if (strpos($appKey, 'base64:') === 0) {
            $keyData = base64_decode(substr($appKey, 7));
            $keyLength = strlen($keyData);
            
            echo "Key length after base64 decode: " . $keyLength . " bytes\n";
            
            if ($keyLength === 32) {
                echo "✅ Key length is correct (32 bytes)\n";
            } else {
                echo "❌ Key length is incorrect, should be 32 bytes\n";
            }
        } else {
            echo "❌ APP_KEY không có tiền tố 'base64:'\n";
        }
    } else {
        echo "❌ Không tìm thấy APP_KEY trong file .env\n";
    }
} else {
    echo "❌ Không tìm thấy file .env\n";
}

echo "\n8. Thông tin chi tiết về env và APP_KEY\n";
echo "-----------------------------\n";

// Hiển thị thông tin chi tiết từ file .env
if (file_exists($envPath)) {
    $envLines = file($envPath, FILE_IGNORE_NEW_LINES);
    foreach ($envLines as $line) {
        if (empty(trim($line)) || strpos($line, '#') === 0) continue;
        
        if (strpos($line, 'APP_') === 0 || strpos($line, 'ENCRYPT_') === 0) {
            echo $line . "\n";
        }
    }
}

echo "\n9. Thông tin PHP\n";
echo "-------------\n";

echo "PHP Version: " . PHP_VERSION . "\n";
echo "Zend Version: " . zend_version() . "\n";
echo "OS: " . PHP_OS . "\n";

// Kiểm tra debug trong .env
if (file_exists($envPath)) {
    if (preg_match('/APP_DEBUG=(.*)/', file_get_contents($envPath), $matches)) {
        echo "APP_DEBUG: " . $matches[1] . "\n";
    }
}

echo "\nĐề xuất khắc phục:\n";
echo "------------------\n";
echo "1. Chạy lại php artisan key:generate --ansi\n";
echo "2. Kiểm tra lại cài đặt cipher trong config/app.php\n";
echo "3. Phiên bản Laravel, OpenSSL và PHP có thể không tương thích hoàn toàn\n";
echo "4. Nếu vẫn gặp lỗi, thử tạo mới dự án Laravel và so sánh các cài đặt\n";
