<?php

/**
 * ติดตั้งไฟล์ Laravel กลับเข้าไปใหม่โดยไม่กระทบข้อมูลที่มีอยู่แล้ว
 * ใช้สำหรับซ่อมแซมไฟล์หลักของ Laravel เมื่อมีปัญหา
 */

echo "===== Laravel Core Files Reinstaller =====\n\n";

// 1. สร้างไดเรกทอรีสำรองข้อมูล
$backupDir = __DIR__ . '/backups/before_reinstall_' . date('YmdHis');
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

echo "1. Backing up important files to $backupDir...\n";

// รายการไฟล์และไดเรกทอรีที่ต้องการสำรอง
$filesToBackup = [
    '.env',
    'app',
    'database',
    'resources/views',
    'routes',
    'config',
    'storage',
    'public/css',
    'public/js',
    'public/images'
];

foreach ($filesToBackup as $path) {
    $fullPath = __DIR__ . '/' . $path;
    if (file_exists($fullPath)) {
        $backupPath = $backupDir . '/' . $path;
        
        // สร้างไดเรกทอรีสำรอง
        if (!is_dir(dirname($backupPath))) {
            mkdir(dirname($backupPath), 0755, true);
        }
        
        // สำรองข้อมูล
        if (is_dir($fullPath)) {
            recursiveCopy($fullPath, $backupPath);
            echo "  ✓ Backed up directory: $path\n";
        } else {
            copy($fullPath, $backupPath);
            echo "  ✓ Backed up file: $path\n";
        }
    }
}

// 2. ติดตั้ง Laravel ใหม่ในโฟลเดอร์ชั่วคราว
$tempDir = __DIR__ . '/temp_laravel';
echo "\n2. Installing fresh Laravel in temporary directory...\n";

// ลบไดเรกทอรีเดิมถ้ามี
if (is_dir($tempDir)) {
    recursiveDelete($tempDir);
}

// สร้างไดเรกทอรีใหม่
mkdir($tempDir, 0755, true);

// ใช้ Composer ติดตั้ง Laravel ใหม่
$command = "cd $tempDir && composer create-project --prefer-dist laravel/laravel . && chmod -R 755 .";
echo "  Running: $command\n";
exec($command, $output, $returnVar);

if ($returnVar !== 0) {
    echo "❌ Failed to install Laravel\n";
    exit(1);
}

echo "  ✓ Fresh Laravel installed successfully\n";

// 3. คัดลอกไฟล์หลักจาก Laravel ใหม่
echo "\n3. Copying core files from fresh Laravel...\n";

$corePath = [
    'artisan',
    'bootstrap',
    'public/index.php',
    'composer.json'
];

foreach ($corePath as $path) {
    $sourcePath = $tempDir . '/' . $path;
    $destPath = __DIR__ . '/' . $path;
    
    // สำรองไฟล์เดิมก่อนแทนที่
    if (file_exists($destPath)) {
        $backupPath = $backupDir . '/' . $path;
        
        // สร้างไดเรกทอรีสำรองถ้ายังไม่มี
        if (!is_dir(dirname($backupPath))) {
            mkdir(dirname($backupPath), 0755, true);
        }
        
        if (is_dir($destPath)) {
            recursiveCopy($destPath, $backupPath);
        } else {
            copy($destPath, $backupPath);
        }
    }
    
    // คัดลอกไฟล์ใหม่
    if (is_dir($sourcePath)) {
        recursiveCopy($sourcePath, $destPath);
        echo "  ✓ Restored directory: $path\n";
    } else {
        copy($sourcePath, $destPath);
        echo "  ✓ Restored file: $path\n";
        
        // ตั้งค่าสิทธิ์พิเศษสำหรับไฟล์ artisan
        if ($path === 'artisan') {
            chmod($destPath, 0755);
        }
    }
}

// 4. คืนค่าข้อมูลจากไฟล์สำรอง
echo "\n4. Restoring your data from backup...\n";

foreach ($filesToBackup as $path) {
    $backupPath = $backupDir . '/' . $path;
    $destPath = __DIR__ . '/' . $path;
    
    if (file_exists($backupPath)) {
        // ลบไฟล์ที่มาจาก Laravel ใหม่ก่อน
        if (file_exists($destPath)) {
            if (is_dir($destPath)) {
                // ไม่ลบทั้งไดเรกทอรี เพราะจะมีไฟล์ที่เพิ่งคัดลอกมา
                // จะทำการ merge แทน
            } else {
                unlink($destPath);
            }
        }
        
        // คัดลอกข้อมูลกลับ
        if (is_dir($backupPath)) {
            recursiveCopy($backupPath, $destPath);
            echo "  ✓ Restored directory: $path\n";
        } else {
            copy($backupPath, $destPath);
            echo "  ✓ Restored file: $path\n";
        }
    }
}

// 5. เคลียร์ Cache
echo "\n5. Clearing cache...\n";

$cacheCommands = [
    'php artisan config:clear',
    'php artisan cache:clear',
    'php artisan view:clear',
    'php artisan route:clear',
    'composer dump-autoload -o'
];

foreach ($cacheCommands as $command) {
    echo "  Running: $command\n";
    exec($command, $output, $returnVar);
}

// 6. ลบไดเรกทอรีชั่วคราว
echo "\n6. Cleaning up...\n";
recursiveDelete($tempDir);
echo "  ✓ Temporary directory removed\n";

echo "\n===== Laravel Core Files Reinstalled Successfully =====\n";
echo "You should now be able to run:\n";
echo "php artisan serve\n";
echo "Laravel version: ";
exec("php artisan --version", $output);
echo implode("\n", $output) . "\n";

// Helper function สำหรับคัดลอกไดเรกทอรีแบบ recursive
function recursiveCopy($source, $dest) {
    // สร้างไดเรกทอรีปลายทางถ้ายังไม่มี
    if (!is_dir($dest)) {
        mkdir($dest, 0755, true);
    }
    
    // เปิด source directory
    $handle = opendir($source);
    
    // คัดลอกไฟล์และไดเรกทอรีย่อย
    while ($file = readdir($handle)) {
        if ($file != "." && $file != "..") {
            $path = "$source/$file";
            
            if (is_dir($path)) {
                // คัดลอกไดเรกทอรีย่อย
                recursiveCopy($path, "$dest/$file");
            } else {
                // คัดลอกไฟล์
                copy($path, "$dest/$file");
            }
        }
    }
    
    closedir($handle);
}

// Helper function สำหรับลบไดเรกทอรีแบบ recursive
function recursiveDelete($dir) {
    if (!file_exists($dir)) {
        return;
    }
    
    if (!is_dir($dir)) {
        unlink($dir);
        return;
    }
    
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        
        if (is_dir($path)) {
            recursiveDelete($path);
        } else {
            unlink($path);
        }
    }
    
    rmdir($dir);
}
