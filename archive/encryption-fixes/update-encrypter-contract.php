<?php

/**
 * ไฟล์นี้ใช้สำหรับปรับปรุงไฟล์ interface ของ Encrypter
 * เพื่อให้ interface สอดคล้องกับคลาส Encrypter ที่แก้ไข
 */

echo "Update Encrypter Contract Interface\n";
echo "================================\n\n";

// พาธของ interface ที่ต้องตรวจสอบ
$encrypterContractPath = __DIR__ . '/vendor/laravel/framework/src/Illuminate/Contracts/Encryption/Encrypter.php';

if (!file_exists($encrypterContractPath)) {
    die("❌ ไม่พบไฟล์ Encrypter Contract Interface\n");
}

// สำรองไฟล์
$timestamp = date('YmdHis');
$backupDir = __DIR__ . '/backups';

if (!file_exists($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$backupPath = "$backupDir/Encrypter.interface.$timestamp";
copy($encrypterContractPath, $backupPath);
echo "✅ สำรอง interface ไว้ที่: $backupPath\n\n";

// อ่านเนื้อหาของไฟล์
$content = file_get_contents($encrypterContractPath);

// ตรวจสอบว่ามีเมธอด getAllKeys() และ getPreviousKeys() หรือไม่
$hasGetAllKeys = strpos($content, 'getAllKeys') !== false;
$hasGetPreviousKeys = strpos($content, 'getPreviousKeys') !== false;

echo "สถานะปัจจุบันของ interface:\n";
echo "- เมธอด getAllKeys(): " . ($hasGetAllKeys ? "มี" : "ไม่มี") . "\n";
echo "- เมธอด getPreviousKeys(): " . ($hasGetPreviousKeys ? "มี" : "ไม่มี") . "\n\n";

// ถ้าไม่มีเมธอดทั้งสอง ให้ปรับปรุง interface
if (!$hasGetAllKeys || !$hasGetPreviousKeys) {
    echo "กำลังปรับปรุง interface...\n";
    
    // เวอร์ชันใหม่ของ interface
    $newContent = <<<'EOT'
<?php

namespace Illuminate\Contracts\Encryption;

interface Encrypter
{
    /**
     * Encrypt the given value.
     *
     * @param  mixed  $value
     * @param  bool  $serialize
     * @return string
     */
    public function encrypt($value, $serialize = true);

    /**
     * Decrypt the given value.
     *
     * @param  string  $payload
     * @param  bool  $unserialize
     * @return mixed
     */
    public function decrypt($payload, $unserialize = true);

    /**
     * Get the encryption key that the encrypter is currently using.
     *
     * @return string
     */
    public function getKey();

    /**
     * Get all encryption keys.
     *
     * @return array
     */
    public function getAllKeys();

    /**
     * Get previous encryption keys.
     *
     * @return array
     */
    public function getPreviousKeys();
}
EOT;
    
    file_put_contents($encrypterContractPath, $newContent);
    echo "✅ อัปเดต interface เรียบร้อยแล้ว\n";
    
    // ตรวจสอบว่าต้องมีไฟล์ StringEncrypter interface ด้วย
    $stringEncrypterPath = __DIR__ . '/vendor/laravel/framework/src/Illuminate/Contracts/Encryption/StringEncrypter.php';
    if (file_exists($stringEncrypterPath)) {
        echo "\nตรวจสอบ StringEncrypter interface...\n";
        $stringEncrypterContent = file_get_contents($stringEncrypterPath);
        
        // ไม่ต้องแก้ไขอะไร เพราะ interface นี้ไม่ได้มีเมธอดเพิ่มเติมที่เกี่ยวข้อง
        echo "✓ StringEncrypter interface ไม่ต้องแก้ไข\n";
    }
} else {
    echo "✅ ไม่จำเป็นต้องปรับปรุง interface เนื่องจากมีเมธอดที่ต้องการครบถ้วนแล้ว\n";
}

// ล้าง cache เพื่อให้การเปลี่ยนแปลงมีผล
echo "\nกำลังล้าง cache เพื่อให้การเปลี่ยนแปลงมีผล...\n";
$clearCommands = [
    'php artisan clear-compiled',
    'composer dump-autoload -o'
];

foreach ($clearCommands as $command) {
    echo "$ $command\n";
    passthru($command);
}

echo "\n================================\n";
echo "การปรับปรุง interface เสร็จสมบูรณ์\n";
echo "ทดสอบการทำงานของระบบด้วยคำสั่ง:\n";
echo "php artisan serve --port=8002\n";
echo "================================\n";
