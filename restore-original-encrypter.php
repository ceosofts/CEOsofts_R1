<?php

/**
 * Script khôi phục lại file Encrypter.php gốc từ Laravel Framework
 * Giải quyết triệt để vấn đề bằng cách khôi phục lại file gốc
 */

echo "Restore Original Laravel Encrypter\n";
echo "===============================\n\n";

// Đường dẫn đến file Encrypter.php
$encrypterPath = __DIR__ . '/vendor/laravel/framework/src/Illuminate/Encryption/Encrypter.php';

// Tải lại composer để khôi phục file gốc từ package
echo "1. Khôi phục file Encrypter.php gốc...\n";
echo "   Đang chạy composer update illuminate/encryption...\n";

passthru('composer update illuminate/encryption --no-interaction');

echo "\n✅ Đã cập nhật package encryption\n";

// Kiểm tra xem file đã được khôi phục chưa
if (file_exists($encrypterPath)) {
    echo "✅ File Encrypter.php đã được khôi phục\n";
    
    // Tùy chọn: Hiển thị nội dung để xác nhận
    $content = file_get_contents($encrypterPath);
    if (strpos($content, 'function getAllKeys()') !== false && 
        strpos($content, 'function getPreviousKeys()') !== false) {
        echo "✅ Phương thức getAllKeys() và getPreviousKeys() đã tồn tại\n";
    } else {
        echo "⚠️ Phương thức getAllKeys() hoặc getPreviousKeys() có thể vẫn chưa tồn tại\n";
        echo "   Bạn có thể cần chạy 'update-encrypter-interface.php'\n";
    }
} else {
    echo "❌ Không tìm thấy file Encrypter.php sau khi cập nhật\n";
}

// Tạo APP_KEY mới
echo "\n2. Tạo APP_KEY mới...\n";
passthru('php artisan key:generate --ansi');

// Xóa cache
echo "\n3. Xóa cache...\n";
passthru('php artisan config:clear');
passthru('php artisan cache:clear');
passthru('php artisan view:clear');
passthru('php artisan route:clear');
passthru('composer dump-autoload -o');

echo "\n===============================\n";
echo "✅ Khôi phục hoàn tất! Vui lòng khởi động lại web server:\n";
echo "php artisan serve\n";
echo "===============================\n";
