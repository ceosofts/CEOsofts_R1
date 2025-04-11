<?php

/**
 * Script khắc phục triệt để vấn đề encryption của Laravel
 * Giải quyết lỗi "Unsupported cipher or incorrect key length"
 */
 
echo "Laravel Encryption Final Fix\n";
echo "==========================\n\n";

// 1. Tạo backup
echo "1. Tạo backup các file quan trọng\n";
echo "------------------------------\n";

$timestamp = date('YmdHis');
$backupDir = __DIR__ . '/backups/final_fix_' . $timestamp;

if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
    echo "✅ Tạo thư mục backup: $backupDir\n";
}

// Backup file cấu hình quan trọng
$filesToBackup = [
    __DIR__ . '/config/app.php',
    __DIR__ . '/.env',
    __DIR__ . '/bootstrap/app.php',
];

foreach ($filesToBackup as $file) {
    if (file_exists($file)) {
        $destination = $backupDir . '/' . basename($file);
        copy($file, $destination);
        echo "✅ Đã backup: " . basename($file) . "\n";
    }
}

// 2. Tạo APP_KEY mới với PHP thuần
echo "\n2. Tạo APP_KEY mới\n";
echo "----------------\n";

try {
    // Tạo key mới (32 bytes)
    $key = random_bytes(32);
    $base64Key = 'base64:' . base64_encode($key);
    echo "Key mới: $base64Key\n";
    
    // Cập nhật file .env
    $envPath = __DIR__ . '/.env';
    if (file_exists($envPath)) {
        $envContent = file_get_contents($envPath);
        
        // Kiểm tra xem APP_KEY đã tồn tại chưa
        if (preg_match('/^APP_KEY=/m', $envContent)) {
            // Thay thế APP_KEY hiện có
            $envContent = preg_replace('/^APP_KEY=.*/m', "APP_KEY=$base64Key", $envContent);
        } else {
            // Thêm APP_KEY nếu chưa có
            $envContent .= "\nAPP_KEY=$base64Key\n";
        }
        
        file_put_contents($envPath, $envContent);
        echo "✅ Đã cập nhật APP_KEY trong file .env\n";
    } else {
        echo "❌ Không tìm thấy file .env\n";
        
        // Tạo file .env mới từ .env.example nếu có
        if (file_exists(__DIR__ . '/.env.example')) {
            $exampleEnv = file_get_contents(__DIR__ . '/.env.example');
            
            // Thay thế APP_KEY trong .env.example
            if (preg_match('/^APP_KEY=/m', $exampleEnv)) {
                $exampleEnv = preg_replace('/^APP_KEY=.*/m', "APP_KEY=$base64Key", $exampleEnv);
            } else {
                $exampleEnv .= "\nAPP_KEY=$base64Key\n";
            }
            
            file_put_contents($envPath, $exampleEnv);
            echo "✅ Đã tạo file .env mới từ .env.example và cập nhật APP_KEY\n";
        } else {
            // Tạo file .env tối thiểu
            $minimalEnv = "APP_NAME=CEOsofts\n" .
                       "APP_ENV=local\n" .
                       "APP_KEY=$base64Key\n" .
                       "APP_DEBUG=true\n" .
                       "APP_URL=http://localhost\n\n" .
                       "DB_CONNECTION=mysql\n" .
                       "DB_HOST=127.0.0.1\n" .
                       "DB_PORT=3306\n" .
                       "DB_DATABASE=ceosofts_db_r1\n" .
                       "DB_USERNAME=root\n" .
                       "DB_PASSWORD=\n\n" .
                       "CACHE_DRIVER=file\n" .
                       "SESSION_DRIVER=file\n" .
                       "QUEUE_CONNECTION=sync\n";
            
            file_put_contents($envPath, $minimalEnv);
            echo "✅ Đã tạo file .env tối thiểu với APP_KEY\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Lỗi khi tạo APP_KEY: " . $e->getMessage() . "\n";
}

// 3. Sửa đổi file config/app.php
echo "\n3. Cập nhật file config/app.php\n";
echo "----------------------------\n";

$appConfigPath = __DIR__ . '/config/app.php';
if (file_exists($appConfigPath)) {
    $appConfig = file_get_contents($appConfigPath);
    
    // Tìm và thay thế cipher
    $newConfig = preg_replace(
        "/'cipher'\s*=>\s*'[^']*'/",
        "'cipher' => 'aes-256-cbc'", 
        $appConfig
    );
    
    // Đảm bảo key lấy từ .env
    $newConfig = preg_replace(
        "/'key'\s*=>\s*[^,]*/",
        "'key' => env('APP_KEY')", 
        $newConfig
    );
    
    if ($newConfig != $appConfig) {
        file_put_contents($appConfigPath, $newConfig);
        echo "✅ Đã cập nhật cipher và key trong config/app.php\n";
    } else {
        echo "ℹ️ Không cần thay đổi config/app.php\n";
    }
} else {
    echo "❌ Không tìm thấy file config/app.php\n";
    
    // Tạo file cấu hình mới
    if (!is_dir(__DIR__ . '/config')) {
        mkdir(__DIR__ . '/config', 0755, true);
    }
    
    $minimalAppConfig = <<<'EOT'
<?php

return [
    'name' => env('APP_NAME', 'Laravel'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL'),
    'timezone' => 'UTC',
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),
    'key' => env('APP_KEY'),
    'cipher' => 'aes-256-cbc',
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE'),
    ],
    'providers' => [
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ],
    'aliases' => [],
];
EOT;

    file_put_contents($appConfigPath, $minimalAppConfig);
    echo "✅ Đã tạo file config/app.php mới với cấu hình chuẩn\n";
}

// 4. Xóa tất cả các cache
echo "\n4. Xóa tất cả các cache\n";
echo "--------------------\n";

// Xóa cache files
$cacheDirs = [
    __DIR__ . '/bootstrap/cache',
    __DIR__ . '/storage/framework/cache',
    __DIR__ . '/storage/framework/views',
    __DIR__ . '/storage/framework/sessions',
];

foreach ($cacheDirs as $dir) {
    if (is_dir($dir)) {
        $files = glob($dir . '/*.php');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "✅ Đã xóa cache trong: " . basename($dir) . "\n";
    }
}

// 5. Kiểm tra các Service Provider
echo "\n5. Kiểm tra Service Providers\n";
echo "--------------------------\n";

$providersDir = __DIR__ . '/app/Providers';
if (is_dir($providersDir)) {
    $files = glob($providersDir . '/*.php');
    $foundEncryption = false;
    
    foreach ($files as $file) {
        $content = file_get_contents($file);
        
        // Kiểm tra xem file có liên quan đến encryption không
        if (strpos($content, 'Encrypter') !== false || 
            strpos($content, 'Encryption') !== false || 
            strpos($content, 'cipher') !== false) {
            
            $foundEncryption = true;
            echo "⚠️ Tìm thấy mã liên quan đến encryption trong: " . basename($file) . "\n";
            
            // Backup file
            copy($file, $backupDir . '/' . basename($file));
            
            // Comment out các phần liên quan đến encryption
            $newContent = preg_replace(
                '/(.*(?:Encrypter|Encryption|cipher).*)/',
                '// $1 /* Commented by final-encryption-fix.php */', 
                $content
            );
            
            if ($newContent !== $content) {
                file_put_contents($file, $newContent);
                echo "✅ Đã comment mã liên quan đến encryption\n";
            }
        }
    }
    
    if (!$foundEncryption) {
        echo "✅ Không tìm thấy mã tùy chỉnh liên quan đến encryption\n";
    }
}

// 6. Kiểm tra và sửa EncryptionServiceProvider
echo "\n6. Kiểm tra EncryptionServiceProvider\n";
echo "--------------------------------\n";

$encryptionSPPath = __DIR__ . '/vendor/laravel/framework/src/Illuminate/Encryption/EncryptionServiceProvider.php';
if (file_exists($encryptionSPPath)) {
    echo "✅ Tìm thấy EncryptionServiceProvider\n";
    
    $content = file_get_contents($encryptionSPPath);
    
    // Kiểm tra supportedCiphers
    if (preg_match('/protected\s+\$supportedCiphers\s*=\s*\[(.*?)\]/s', $content, $matches)) {
        $ciphers = $matches[1];
        echo "Ciphers được hỗ trợ hiện tại: $ciphers\n";
        
        // Kiểm tra xem aes-256-cbc có trong danh sách không
        if (strpos($ciphers, 'aes-256-cbc') === false) {
            echo "⚠️ aes-256-cbc không có trong danh sách hỗ trợ\n";
            
            // Backup file
            copy($encryptionSPPath, $backupDir . '/EncryptionServiceProvider.php');
            
            // Thêm aes-256-cbc vào danh sách
            $newSupportedCiphers = "        'aes-128-cbc',
        'aes-256-cbc',
        'aes-128-gcm',
        'aes-256-gcm'";
            
            $newContent = preg_replace(
                '/protected\s+\$supportedCiphers\s*=\s*\[(.*?)\]/s',
                "protected \$supportedCiphers = [$newSupportedCiphers]",
                $content
            );
            
            if ($newContent !== $content) {
                file_put_contents($encryptionSPPath, $newContent);
                echo "✅ Đã sửa danh sách cipher được hỗ trợ\n";
            }
        } else {
            echo "✅ aes-256-cbc có trong danh sách hỗ trợ\n";
        }
    } else {
        echo "❌ Không tìm thấy danh sách supportedCiphers\n";
    }
} else {
    echo "❌ Không tìm thấy EncryptionServiceProvider\n";
}

// 7. Kiểm tra MCRYPT extension
echo "\n7. Kiểm tra PHP extensions\n";
echo "-----------------------\n";

if (function_exists('openssl_encrypt')) {
    echo "✅ OpenSSL extension đã được cài đặt\n";
} else {
    echo "❌ OpenSSL extension chưa được cài đặt\n";
}

// 8. Chạy lại một số lệnh Artisan
echo "\n8. Chạy lệnh Artisan\n";
echo "------------------\n";

$artisanCommands = [
    'config:clear',
    'cache:clear',
    'route:clear',
    'view:clear',
    'clear-compiled',
];

foreach ($artisanCommands as $command) {
    echo "Đang chạy: php artisan $command\n";
    passthru("php artisan $command");
}

// 9. Thử tạo instance Encrypter
echo "\n9. Kiểm tra instance Encrypter\n";
echo "--------------------------\n";

try {
    require __DIR__.'/vendor/autoload.php';
    $app = require_once __DIR__.'/bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    // Thử lấy encrypter từ service container
    try {
        $encrypter = app('encrypter');
        echo "✅ Lấy encrypter từ service container thành công\n";
        
        // Thử encrypt và decrypt một string
        $string = 'Test encryption';
        $encrypted = $encrypter->encrypt($string);
        $decrypted = $encrypter->decrypt($encrypted);
        
        if ($decrypted === $string) {
            echo "✅ Encrypt và decrypt hoạt động bình thường\n";
        } else {
            echo "❌ Encrypt và decrypt không hoạt động đúng\n";
        }
    } catch (Exception $e) {
        echo "❌ Lỗi khi lấy encrypter: " . $e->getMessage() . "\n";
        
        // Thử tạo instance mới của Encrypter
        try {
            // Lấy key từ .env
            $key = config('app.key');
            if (!$key && file_exists($envPath)) {
                preg_match('/APP_KEY=([^\s\n]+)/', file_get_contents($envPath), $matches);
                if (isset($matches[1]) && strpos($matches[1], 'base64:') === 0) {
                    $key = $matches[1];
                }
            }
            
            if ($key && strpos($key, 'base64:') === 0) {
                $keyData = base64_decode(substr($key, 7));
                $encrypter = new Illuminate\Encryption\Encrypter($keyData, 'aes-256-cbc');
                
                $string = 'Test encryption';
                $encrypted = $encrypter->encrypt($string);
                $decrypted = $encrypter->decrypt($encrypted);
                
                if ($decrypted === $string) {
                    echo "✅ Tạo instance Encrypter thủ công thành công\n";
                } else {
                    echo "❌ Encrypt và decrypt thủ công không hoạt động đúng\n";
                }
            } else {
                echo "❌ Không tìm thấy key hợp lệ\n";
            }
        } catch (Exception $e) {
            echo "❌ Lỗi khi tạo instance Encrypter: " . $e->getMessage() . "\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Lỗi khi bootstrap Laravel: " . $e->getMessage() . "\n";
}

echo "\nQuá trình sửa chữa đã hoàn tất. Vui lòng thử lại ứng dụng.\n";
echo "Nếu vẫn gặp lỗi, có thể bạn cần tạo mới dự án Laravel và copy các file source code qua.\n";
echo "Bạn có thể chạy 'php artisan serve' để khởi động lại máy chủ phát triển.\n";
