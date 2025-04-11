<?php

/**
 * แก้ไขปัญหาไวยากรณ์ในไฟล์ app/helpers.php
 * ที่มีข้อผิดพลาด "syntax error, unexpected token "<", expecting end of file"
 */

echo "===== Fix Helpers.php Syntax Error =====\n\n";

$helpersPath = __DIR__ . '/app/helpers.php';

if (!file_exists($helpersPath)) {
    echo "❌ ไม่พบไฟล์ app/helpers.php\n";
    exit(1);
}

// สำรองไฟล์ก่อนแก้ไข
$backupDir = __DIR__ . '/backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
    echo "✅ สร้างโฟลเดอร์ backups\n";
}

$timestamp = date('YmdHis');
$backupPath = "{$backupDir}/helpers.php.{$timestamp}.bak";
copy($helpersPath, $backupPath);
echo "✅ สำรองไฟล์ app/helpers.php ไว้ที่ {$backupPath}\n";

// อ่านเนื้อหาไฟล์
$content = file_get_contents($helpersPath);

// แสดงส่วนที่มีปัญหา
$lines = explode("\n", $content);
$problematicLine = 87 - 1; // เนื่องจาก array เริ่มที่ 0

echo "\nข้อมูลเกี่ยวกับไฟล์:\n";
echo "จำนวนบรรทัดทั้งหมด: " . count($lines) . "\n";

if (isset($lines[$problematicLine])) {
    echo "บรรทัดที่มีปัญหา (บรรทัดที่ 87): \n";
    echo "{$lines[$problematicLine]}\n";
} else {
    echo "ไม่พบบรรทัดที่ 87 (ไฟล์มีแค่ " . count($lines) . " บรรทัด)\n";
}

echo "\nกำลังแก้ไขปัญหา...\n";

// แก้ไขปัญหาโดยการสร้างไฟล์ใหม่ที่ถูกต้อง
$correctContent = <<<'EOT'
<?php

use App\Encryption\SimpleEncrypter;

if (!function_exists('simple_encrypt')) {
    /**
     * เข้ารหัสข้อมูลด้วย SimpleEncrypter
     */
    function simple_encrypt($value, $serialize = true) {
        if (!class_exists('App\Encryption\SimpleEncrypter')) {
            throw new RuntimeException('SimpleEncrypter class is not available.');
        }
        
        static $encrypter = null;
        if ($encrypter === null) {
            $encrypter = new SimpleEncrypter();
        }
        
        return $encrypter->encrypt($value, $serialize);
    }
}

if (!function_exists('simple_decrypt')) {
    /**
     * ถอดรหัสข้อมูลด้วย SimpleEncrypter
     */
    function simple_decrypt($payload, $unserialize = true) {
        if (!class_exists('App\Encryption\SimpleEncrypter')) {
            throw new RuntimeException('SimpleEncrypter class is not available.');
        }
        
        static $encrypter = null;
        if ($encrypter === null) {
            $encrypter = new SimpleEncrypter();
        }
        
        return $encrypter->decrypt($payload, $unserialize);
    }
}

// แทนที่ฟังก์ชัน encrypt และ decrypt ของ Laravel
if (!function_exists('encrypt')) {
    /**
     * เข้ารหัสข้อมูล (แทนที่ฟังก์ชัน encrypt เดิม)
     */
    function encrypt($value, $serialize = true) {
        return simple_encrypt($value, $serialize);
    }
}

if (!function_exists('decrypt')) {
    /**
     * ถอดรหัสข้อมูล (แทนที่ฟังก์ชัน decrypt เดิม)
     */
    function decrypt($payload, $unserialize = true) {
        return simple_decrypt($payload, $unserialize);
    }
}
EOT;

file_put_contents($helpersPath, $correctContent);
echo "✅ แก้ไขไฟล์ app/helpers.php เรียบร้อยแล้ว\n";

// ตรวจสอบว่าไฟล์ถูกต้องหรือไม่
$checkSyntax = shell_exec("php -l {$helpersPath}");
if (strpos($checkSyntax, 'No syntax errors detected') !== false) {
    echo "✅ ตรวจสอบไวยากรณ์แล้ว: ไม่พบข้อผิดพลาด\n";
} else {
    echo "❌ ยังพบข้อผิดพลาดในไฟล์: {$checkSyntax}\n";
}

// 2. อัปเดต composer
echo "\nกำลังอัปเดต autoload...\n";
passthru('composer dump-autoload -o');

// 3. ล้าง cache เพื่อให้ Laravel ใช้ไฟล์ใหม่
echo "\nกำลังล้าง cache...\n";
passthru('php artisan config:clear');
passthru('php artisan cache:clear');
passthru('php artisan view:clear');

echo "\n===== คำแนะนำ =====\n";
echo "1. รันเซิร์ฟเวอร์อีกครั้งเพื่อทดสอบ:\n";
echo "   php artisan serve --port=8006\n";
echo "2. หากยังมีปัญหา ให้ทดสอบด้วย:\n";
echo "   php test-simple-encrypter.php\n";
echo "3. หรือแก้ไขไฟล์ bootstrap/app.php ด้วย:\n";
echo "   php bootstrap-fix-minimal.php\n";
