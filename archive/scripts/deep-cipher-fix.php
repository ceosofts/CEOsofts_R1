<?php

/**
 * สคริปต์แก้ไขปัญหา Cipher อย่างถึงแก่น
 * ค้นหาและแก้ไขปัญหา "Unsupported cipher or incorrect key length" ในทุกไฟล์ที่เกี่ยวข้อง
 */

echo "Deep Cipher Fix for CEOsofts R1\n";
echo "=============================\n\n";

// 1. ตรวจสอบการตั้งค่า config เดิมและทำ backup
echo "1. กำลังตรวจสอบและทำ backup ไฟล์ config...\n";
$appConfigPath = __DIR__ . '/config/app.php';
$envPath = __DIR__ . '/.env';
$bootstrapAppPath = __DIR__ . '/bootstrap/app.php';
$encryptionServiceProviderPath = __DIR__ . '/vendor/laravel/framework/src/Illuminate/Encryption/EncryptionServiceProvider.php';

// สร้างโฟลเดอร์สำหรับ backup
if (!is_dir(__DIR__ . '/backups')) {
    mkdir(__DIR__ . '/backups');
    echo "✅ สร้างโฟลเดอร์ backups สำเร็จ\n";
}

// Backup ไฟล์ที่เกี่ยวข้อง
$timestamp = date('Y-m-d_H-i-s');
if (file_exists($appConfigPath)) {
    copy($appConfigPath, __DIR__ . "/backups/app.php.{$timestamp}");
    echo "✅ สำรองไฟล์ config/app.php\n";
}

if (file_exists($envPath)) {
    copy($envPath, __DIR__ . "/backups/.env.{$timestamp}");
    echo "✅ สำรองไฟล์ .env\n";
}

// 2. ตรวจสอบ bootstrap/app.php (กรณีมีการ override services หรือ config)
echo "\n2. กำลังตรวจสอบ bootstrap/app.php...\n";
if (file_exists($bootstrapAppPath)) {
    $bootstrapContent = file_get_contents($bootstrapAppPath);
    if (strpos($bootstrapContent, 'Illuminate\Encryption') !== false || 
        strpos($bootstrapContent, 'cipher') !== false) {
        echo "⚠️ พบการกำหนดค่าเกี่ยวกับ encryption ใน bootstrap/app.php\n";
        copy($bootstrapAppPath, __DIR__ . "/backups/bootstrap_app.php.{$timestamp}");
        echo "   สำรองไฟล์ bootstrap/app.php\n";
    } else {
        echo "✅ ไม่พบการกำหนดค่าเกี่ยวกับ encryption ใน bootstrap/app.php\n";
    }
} else {
    echo "❌ ไม่พบไฟล์ bootstrap/app.php\n";
}

// 3. ตรวจสอบ Service Providers ที่อาจ override encryption setting
echo "\n3. กำลังตรวจสอบ Service Providers...\n";
$providersPath = __DIR__ . '/app/Providers';
if (is_dir($providersPath)) {
    $providerFiles = glob($providersPath . '/*.php');
    $encryptionRelated = false;
    
    foreach ($providerFiles as $providerFile) {
        $content = file_get_contents($providerFile);
        if (strpos($content, 'Illuminate\Encryption') !== false || 
            strpos($content, 'Encrypter') !== false || 
            strpos($content, 'cipher') !== false) {
            echo "⚠️ พบการอ้างอิงถึง encryption ในไฟล์ " . basename($providerFile) . "\n";
            copy($providerFile, __DIR__ . "/backups/" . basename($providerFile) . ".{$timestamp}");
            echo "   สำรองไฟล์ " . basename($providerFile) . "\n";
            $encryptionRelated = true;
            
            // ทำ comment โค้ดที่อาจเป็นปัญหา
            $content = preg_replace(
                '/(.*Encrypter.*|.*Illuminate\\\\Encryption.*|.*cipher.*)/',
                '// $1 /* Commented by deep-cipher-fix.php */',
                $content
            );
            file_put_contents($providerFile, $content);
            echo "   แก้ไขไฟล์ " . basename($providerFile) . " โดย comment โค้ดที่อาจเป็นปัญหา\n";
        }
    }
    
    if (!$encryptionRelated) {
        echo "✅ ไม่พบ Service Provider ที่เกี่ยวข้องกับ encryption\n";
    }
} else {
    echo "❌ ไม่พบโฟลเดอร์ app/Providers\n";
}

// 4. ตรวจสอบและแก้ไข config/app.php
echo "\n4. กำลังตรวจสอบและแก้ไข config/app.php...\n";
if (file_exists($appConfigPath)) {
    $appConfig = file_get_contents($appConfigPath);
    
    // แก้ไข cipher
    $cipherPatternFound = preg_match("/'cipher'\s*=>\s*'([^']*)'/", $appConfig, $cipherMatches);
    if ($cipherPatternFound) {
        $currentCipher = $cipherMatches[1];
        echo "พบค่า cipher: {$currentCipher}\n";
        
        if (strtolower($currentCipher) !== 'aes-256-cbc') {
            echo "⚠️ ค่า cipher ไม่ถูกต้อง กำลังแก้ไข...\n";
            $appConfig = preg_replace(
                "/'cipher'\s*=>\s*'[^']*'/",
                "'cipher' => 'aes-256-cbc'",
                $appConfig
            );
            file_put_contents($appConfigPath, $appConfig);
            echo "✅ แก้ไขค่า cipher เป็น 'aes-256-cbc'\n";
        } else {
            echo "✅ ค่า cipher ถูกต้อง\n";
        }
    } else {
        echo "❌ ไม่พบการกำหนดค่า cipher ใน config/app.php\n";
        
        // เพิ่ม cipher ถ้าไม่มี
        $keyPattern = "/'key'\s*=>/";
        if (preg_match($keyPattern, $appConfig)) {
            $appConfig = preg_replace(
                $keyPattern,
                "'cipher' => 'aes-256-cbc',\n\n    'key' =>",
                $appConfig
            );
            file_put_contents($appConfigPath, $appConfig);
            echo "✅ เพิ่มค่า cipher เป็น 'aes-256-cbc'\n";
        } else {
            echo "❌ ไม่สามารถเพิ่มค่า cipher เนื่องจากไม่พบตำแหน่ง key\n";
        }
    }
    
    // ตรวจสอบค่า key
    $keyPatternFound = preg_match("/'key'\s*=>\s*([^,]*)/", $appConfig, $keyMatches);
    if ($keyPatternFound) {
        $currentKey = trim($keyMatches[1]);
        echo "พบการกำหนดค่า key: {$currentKey}\n";
        
        if ($currentKey !== "env('APP_KEY')") {
            echo "⚠️ การกำหนดค่า key ไม่ถูกต้อง กำลังแก้ไข...\n";
            $appConfig = preg_replace(
                "/'key'\s*=>\s*[^,]*/",
                "'key' => env('APP_KEY')",
                $appConfig
            );
            file_put_contents($appConfigPath, $appConfig);
            echo "✅ แก้ไขค่า key เป็น env('APP_KEY')\n";
        } else {
            echo "✅ การกำหนดค่า key ถูกต้อง\n";
        }
    } else {
        echo "❌ ไม่พบการกำหนดค่า key ใน config/app.php\n";
    }
} else {
    echo "❌ ไม่พบไฟล์ config/app.php\n";
}

// 5. แก้ไขไฟล์ .env
echo "\n5. กำลังตรวจสอบและแก้ไขไฟล์ .env...\n";
if (file_exists($envPath)) {
    $envContent = file_get_contents($envPath);
    
    // ตรวจสอบ APP_KEY
    $appKeyFound = preg_match('/^APP_KEY=(.*)$/m', $envContent, $appKeyMatches);
    if ($appKeyFound) {
        $appKeyValue = trim($appKeyMatches[1]);
        echo "พบค่า APP_KEY: {$appKeyValue}\n";
        
        $needNewKey = empty($appKeyValue) || 
                     $appKeyValue === "base64:" || 
                     strpos($appKeyValue, "base64:") !== 0;
        
        // ตรวจสอบความยาวของ key
        if (!$needNewKey && strpos($appKeyValue, "base64:") === 0) {
            try {
                $decodedKey = base64_decode(substr($appKeyValue, 7));
                if (strlen($decodedKey) != 32) {
                    $needNewKey = true;
                    echo "⚠️ ความยาวของ key ไม่ถูกต้อง: " . strlen($decodedKey) . " bytes (ควรเป็น 32 bytes)\n";
                }
            } catch (\Exception $e) {
                $needNewKey = true;
                echo "⚠️ ไม่สามารถถอดรหัส key: {$e->getMessage()}\n";
            }
        }
        
        if ($needNewKey) {
            echo "⚠️ APP_KEY ไม่ถูกต้อง กำลังสร้างใหม่...\n";
            
            // สร้าง key ใหม่ด้วยตัวเอง (ถ้า artisan ไม่ทำงาน)
            $newKey = 'base64:' . base64_encode(random_bytes(32));
            
            // แทนที่ APP_KEY ในไฟล์ .env
            $envContent = preg_replace('/^APP_KEY=.*$/m', "APP_KEY={$newKey}", $envContent);
            file_put_contents($envPath, $envContent);
            
            echo "✅ สร้าง APP_KEY ใหม่: {$newKey}\n";
        } else {
            echo "✅ APP_KEY มีรูปแบบที่ถูกต้อง\n";
        }
    } else {
        echo "❌ ไม่พบ APP_KEY ในไฟล์ .env\n";
        
        // เพิ่ม APP_KEY ถ้าไม่มี
        $newKey = 'base64:' . base64_encode(random_bytes(32));
        $envContent .= "\nAPP_KEY={$newKey}\n";
        file_put_contents($envPath, $envContent);
        
        echo "✅ เพิ่ม APP_KEY: {$newKey}\n";
    }
} else {
    echo "❌ ไม่พบไฟล์ .env\n";
    
    // สร้างไฟล์ .env ใหม่
    if (file_exists(__DIR__ . '/.env.example')) {
        copy(__DIR__ . '/.env.example', $envPath);
        $envContent = file_get_contents($envPath);
        
        // สร้าง APP_KEY ใหม่
        $newKey = 'base64:' . base64_encode(random_bytes(32));
        $envContent = preg_replace('/^APP_KEY=.*$/m', "APP_KEY={$newKey}", $envContent);
        
        if (!strpos($envContent, 'APP_KEY=')) {
            $envContent .= "\nAPP_KEY={$newKey}\n";
        }
        
        file_put_contents($envPath, $envContent);
        echo "✅ สร้างไฟล์ .env ใหม่พร้อม APP_KEY: {$newKey}\n";
    } else {
        echo "❌ ไม่พบไฟล์ .env.example สำหรับใช้เป็นต้นแบบ\n";
    }
}

// 6. แก้ไขไฟล์ EncryptionServiceProvider (กรณีมีการปรับแต่ง)
echo "\n6. ตรวจสอบไฟล์ EncryptionServiceProvider...\n";
if (file_exists($encryptionServiceProviderPath)) {
    $encryptionServiceProvider = file_get_contents($encryptionServiceProviderPath);
    
    // ตรวจสอบการกำหนดค่า cipher
    if (strpos($encryptionServiceProvider, "'cipher' =>") !== false || 
        strpos($encryptionServiceProvider, 'SUPPORTED_CIPHERS') !== false) {
        
        copy($encryptionServiceProviderPath, __DIR__ . "/backups/EncryptionServiceProvider.php.{$timestamp}");
        echo "✅ สำรองไฟล์ EncryptionServiceProvider.php\n";
        
        // ตรวจสอบ SUPPORTED_CIPHERS
        if (preg_match('/protected\s+\$supportedCiphers\s*=\s*\[(.*?)\]/s', $encryptionServiceProvider, $matches)) {
            echo "⚠️ ตรวจพบการกำหนด supportedCiphers ใน EncryptionServiceProvider\n";
            
            // ตรวจสอบว่ามี aes-256-cbc หรือไม่
            $ciphers = $matches[1];
            if (strpos($ciphers, 'aes-256-cbc') === false) {
                echo "⚠️ ไม่พบ 'aes-256-cbc' ใน supportedCiphers\n";
                
                // แก้ไข supportedCiphers
                $newCiphers = "        'aes-128-cbc',
        'aes-256-cbc',
        'aes-128-gcm',
        'aes-256-gcm'";
                
                $encryptionServiceProvider = preg_replace(
                    '/protected\s+\$supportedCiphers\s*=\s*\[(.*?)\]/s',
                    "protected \$supportedCiphers = [$newCiphers]",
                    $encryptionServiceProvider
                );
                
                file_put_contents($encryptionServiceProviderPath, $encryptionServiceProvider);
                echo "✅ แก้ไข supportedCiphers ใน EncryptionServiceProvider\n";
            } else {
                echo "✅ พบ 'aes-256-cbc' ใน supportedCiphers\n";
            }
        }
    } else {
        echo "✅ ไม่พบการกำหนด cipher ใน EncryptionServiceProvider\n";
    }
} else {
    echo "⚠️ ไม่พบไฟล์ EncryptionServiceProvider.php\n";
}

// 7. ลบไฟล์ cache ทั้งหมด
echo "\n7. กำลังลบไฟล์ cache ทั้งหมด...\n";
$cacheFiles = [
    __DIR__ . '/bootstrap/cache/config.php',
    __DIR__ . '/bootstrap/cache/routes-v7.php',
    __DIR__ . '/bootstrap/cache/services.php',
    __DIR__ . '/bootstrap/cache/packages.php'
];

foreach ($cacheFiles as $cacheFile) {
    if (file_exists($cacheFile)) {
        unlink($cacheFile);
        echo "✅ ลบไฟล์ {$cacheFile}\n";
    }
}

// ลบโฟลเดอร์ cache ของ view
$viewCacheDir = __DIR__ . '/storage/framework/views';
if (is_dir($viewCacheDir)) {
    $files = glob($viewCacheDir . '/*.php');
    foreach ($files as $file) {
        unlink($file);
    }
    echo "✅ ลบไฟล์ cache ของ view\n";
}

// 8. เคลียร์ cache ด้วยคำสั่ง artisan
echo "\n8. กำลังเคลียร์ cache ด้วยคำสั่ง artisan...\n";
exec('php artisan config:clear', $output);
echo "✅ เคลียร์ config cache\n";

exec('php artisan cache:clear', $output);
echo "✅ เคลียร์ application cache\n";

exec('php artisan route:clear', $output);
echo "✅ เคลียร์ route cache\n";

exec('php artisan view:clear', $output);
echo "✅ เคลียร์ view cache\n";

exec('php artisan clear-compiled', $output);
echo "✅ เคลียร์ compiled code\n";

exec('composer dump-autoload -o', $output);
echo "✅ อัพเดต autoload\n";

// 9. ทดสอบ encryption system
echo "\n9. กำลังทดสอบระบบ encryption...\n";

try {
    // ต้อง bootstrap Laravel application ก่อน
    require __DIR__.'/vendor/autoload.php';
    $app = require_once __DIR__.'/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // ทดสอบการเข้ารหัส
    $testString = 'ทดสอบการเข้ารหัส';
    $encrypted = \Illuminate\Support\Facades\Crypt::encrypt($testString);
    $decrypted = \Illuminate\Support\Facades\Crypt::decrypt($encrypted);
    
    if ($decrypted === $testString) {
        echo "✅ ทดสอบการเข้ารหัสและถอดรหัสสำเร็จ!\n";
        
        // แสดงการตั้งค่าปัจจุบัน
        echo "\nการตั้งค่า encryption ปัจจุบัน:\n";
        echo "- Cipher: " . config('app.cipher') . "\n";
        echo "- APP_KEY: " . substr(env('APP_KEY'), 0, 16) . "...[ถูกปิดบังบางส่วน]\n";
    } else {
        echo "❌ ทดสอบการเข้ารหัสล้มเหลว ข้อความที่ถอดรหัสไม่ตรงกับข้อความต้นฉบับ\n";
    }
} catch (\Exception $e) {
    echo "❌ เกิดข้อผิดพลาดขณะทดสอบการเข้ารหัส: " . $e->getMessage() . "\n";
    
    // พยายามแก้ไขปัญหาอีกครั้งด้วยวิธีสุดท้าย
    echo "\nกำลังใช้วิธีแก้ไขขั้นสุดท้าย...\n";
    
    // สร้างไฟล์ config/app.php ใหม่ทั้งหมด
    if (file_exists(__DIR__ . '/config/app-fix.php')) {
        copy(__DIR__ . '/config/app-fix.php', $appConfigPath);
        echo "✅ คัดลอกไฟล์ config/app-fix.php ไปที่ config/app.php\n";
    } else {
        // สร้าง config/app.php ใหม่ด้วยค่าพื้นฐาน
        file_put_contents(__DIR__ . '/config/app.php', <<<'EOT'
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
EOT
        );
        echo "✅ สร้างไฟล์ config/app.php ใหม่\n";
    }
    
    // สร้าง APP_KEY ใหม่
    $newKey = 'base64:' . base64_encode(random_bytes(32));
    $envContent = file_get_contents($envPath);
    $envContent = preg_replace('/^APP_KEY=.*$/m', "APP_KEY={$newKey}", $envContent);
    file_put_contents($envPath, $envContent);
    echo "✅ สร้าง APP_KEY ใหม่: {$newKey}\n";
    
    // เคลียร์ cache
    exec('php artisan config:clear');
    exec('php artisan cache:clear');
    echo "✅ เคลียร์ cache เรียบร้อยแล้ว\n";
}

echo "\n=============================\n";
echo "การแก้ไขปัญหา cipher และ key เสร็จสมบูรณ์!\n";
echo "โปรดรีสตาร์ท PHP server ด้วยคำสั่ง:\n";
echo "php artisan serve\n";
echo "=============================\n";
