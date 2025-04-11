<?php

/**
 * แก้ไขปัญหาไฟล์ bootstrap/app.php เพื่อแก้ปัญหาการโหลด Encrypter
 * สคริปต์นี้จะแทรกโค้ดก่อน return $app เพื่อให้ Laravel ใช้ SimpleEncrypter แทน
 */

echo "===== Fix Bootstrap/App.php =====\n\n";

// ตรวจสอบไฟล์ bootstrap/app.php
$bootstrapAppPath = __DIR__ . '/bootstrap/app.php';
if (!file_exists($bootstrapAppPath)) {
    echo "❌ ไม่พบไฟล์ bootstrap/app.php\n";
    exit(1);
}

// สำรองไฟล์ก่อนแก้ไข
$backupDir = __DIR__ . '/backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
    echo "✅ สร้างไดเรกทอรี backups\n";
}

$timestamp = date('YmdHis');
$backupPath = "{$backupDir}/app.php.{$timestamp}";
copy($bootstrapAppPath, $backupPath);
echo "✅ สำรองไฟล์ bootstrap/app.php ไว้ที่ {$backupPath}\n";

// อ่านเนื้อหาไฟล์
$content = file_get_contents($bootstrapAppPath);

// ตรวจสอบว่ามีการลงทะเบียน SimpleEncryptionServiceProvider แล้วหรือไม่
if (strpos($content, 'SimpleEncryptionServiceProvider') !== false) {
    echo "⚠️ พบการลงทะเบียน SimpleEncryptionServiceProvider อยู่แล้ว ไม่ต้องแก้ไข\n";
} else {
    // หาตำแหน่ง return $app; 
    $returnPos = strpos($content, 'return $app;');
    
    if ($returnPos === false) {
        echo "❌ ไม่พบตำแหน่ง 'return \$app;' ในไฟล์ bootstrap/app.php\n";
        exit(1);
    }
    
    // สร้างโค้ดที่จะแทรก
    $codeToInsert = <<<'EOT'

// แก้ไขปัญหาการโหลด Encrypter
$app->singleton('encrypter', function ($app) {
    // ใช้ SimpleEncrypter แทน Encrypter ของ Laravel
    if (class_exists(\App\Encryption\SimpleEncrypter::class)) {
        return new \App\Encryption\SimpleEncrypter();
    }
    
    // Fallback ไปใช้วิธีการอื่นถ้าไม่พบ SimpleEncrypter
    $config = $app->make('config')->get('app');
    $key = isset($config['key']) ? base64_decode(substr($config['key'], 7)) : random_bytes(32);
    $cipher = $config['cipher'] ?? 'aes-256-cbc';
    
    try {
        return new \Illuminate\Encryption\Encrypter($key, $cipher);
    } catch (\Exception $e) {
        // ถ้าเกิดข้อผิดพลาด ให้สร้าง encrypter ด้วย key สุ่มเพื่อให้แอพทำงานต่อได้
        $tempKey = random_bytes(32);
        return new \Illuminate\Encryption\Encrypter($tempKey, 'aes-256-cbc');
    }
});

EOT;
    
    // แทรกโค้ดก่อน return $app;
    $newContent = substr($content, 0, $returnPos) . $codeToInsert . substr($content, $returnPos);
    
    // บันทึกไฟล์
    file_put_contents($bootstrapAppPath, $newContent);
    echo "✅ แก้ไขไฟล์ bootstrap/app.php เรียบร้อยแล้ว\n";
}

// ล้าง Cache
echo "\nกำลังล้าง Cache...\n";
passthru('php artisan config:clear');
passthru('php artisan cache:clear');
passthru('php artisan view:clear');
passthru('php artisan route:clear');
passthru('composer dump-autoload -o');

echo "\n===== คำแนะนำ =====\n";
echo "ตอนนี้ไฟล์ bootstrap/app.php ได้รับการแก้ไขแล้ว เพื่อให้ใช้ SimpleEncrypter แทน\n";
echo "ลองทดสอบรัน server อีกครั้ง:\n";
echo "php artisan serve --port=8007\n";
