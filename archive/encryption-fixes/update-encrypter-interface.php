<?php

/**
 * Script cập nhật lớp Encrypter.php để triển khai các phương thức mới trong interface
 * Giải quyết lỗi: Class Illuminate\Encryption\Encrypter contains 2 abstract methods
 */

echo "Update Encrypter for Laravel 12.8.1\n";
echo "=================================\n\n";

// Đường dẫn đến file Encrypter.php
$encrypterPath = __DIR__ . '/vendor/laravel/framework/src/Illuminate/Encryption/Encrypter.php';

if (!file_exists($encrypterPath)) {
    die("Không tìm thấy file Encrypter.php tại $encrypterPath\n");
}

// Backup file hiện tại
$timestamp = date('YmdHis');
$backupPath = __DIR__ . '/backups';

if (!is_dir($backupPath)) {
    mkdir($backupPath, 0755, true);
}

$encrypterBackupPath = "$backupPath/Encrypter.php.$timestamp";
copy($encrypterPath, $encrypterBackupPath);
echo "✅ Đã backup file Encrypter.php tại $encrypterBackupPath\n\n";

// Đọc nội dung file hiện tại
$content = file_get_contents($encrypterPath);

// Kiểm tra xem file đã có phương thức getAllKeys() và getPreviousKeys() chưa
if (strpos($content, 'function getAllKeys()') === false) {
    echo "Đang thêm phương thức getAllKeys()...\n";
    
    // Thêm phương thức getAllKeys() vào cuối lớp, trước dấu ngoặc đóng cuối cùng
    $content = preg_replace('/}(\s*)$/', 
        "    /**
     * Get all encryption keys.
     *
     * @return array
     */
    public function getAllKeys()
    {
        return ['current' => \$this->key];
    }
    
    /**
     * Get previous encryption keys.
     *
     * @return array
     */
    public function getPreviousKeys()
    {
        return [];
    }
}\$1", 
        $content);
    
    echo "✅ Đã thêm phương thức getAllKeys() và getPreviousKeys()\n";
} else {
    echo "✓ Phương thức getAllKeys() đã tồn tại\n";
}

// Kiểm tra interface được implement
if (strpos($content, 'implements EncrypterContract, StringEncrypter') !== false) {
    echo "Interface hiện tại đang được triển khai đúng\n";
} else {
    echo "Đang cập nhật interface...\n";
    $content = preg_replace('/class Encrypter implements ([^\{]+)/', 
        'class Encrypter implements EncrypterContract, StringEncrypter', 
        $content);
    echo "✅ Đã cập nhật interface\n";
}

// Lưu file đã cập nhật
file_put_contents($encrypterPath, $content);
echo "✅ Đã lưu file Encrypter.php với các phương thức mới\n\n";

// Tạo hoặc cập nhật interface file nếu cần
$encrypterInterfacePath = __DIR__ . '/vendor/laravel/framework/src/Illuminate/Contracts/Encryption/Encrypter.php';
if (file_exists($encrypterInterfacePath)) {
    echo "Đang kiểm tra interface file...\n";
    $interfaceContent = file_get_contents($encrypterInterfacePath);
    
    // Nếu interface không có phương thức getAllKeys() và getPreviousKeys()
    // và chúng ta đã thêm các phương thức này vào lớp Encrypter, 
    // chúng ta cũng nên thêm chúng vào interface
    if (strpos($interfaceContent, 'function getAllKeys()') === false) {
        echo "⚠️ Interface không chứa phương thức getAllKeys()\n";
        echo "Đây có thể là một vấn đề nếu phiên bản Laravel của bạn không nhất quán.\n";
    } else {
        echo "✓ Interface đã có phương thức getAllKeys()\n";
    }
} else {
    echo "⚠️ Không tìm thấy file interface Encrypter.php\n";
}

// Xóa cache
echo "\nĐang xóa cache...\n";
passthru('php artisan config:clear');
passthru('php artisan cache:clear');
passthru('composer dump-autoload');

echo "\n=================================\n";
echo "✅ Cập nhật hoàn tất! Vui lòng khởi động lại web server:\n";
echo "php artisan serve\n";
echo "=================================\n";
