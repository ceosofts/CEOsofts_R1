<?php

/**
 * ทดสอบ routing โดยตรง
 * ไฟล์นี้เป็น entry point แยกต่างหาก ไม่ขึ้นกับ artisan หรือ public/index.php
 */

// กำหนดเวลาเริ่ม
define('LARAVEL_START', microtime(true));

// โหลด autoloader
require __DIR__ . '/vendor/autoload.php';

echo "===== Direct Laravel Test =====\n\n";

try {
    // ย้อนกลับกระบวนการของ Laravel เพื่อรวบรวมข้อมูล
    echo "1. Loading bootstrap/app.php...\n";
    
    // โหลด bootstrap/app.php และตรวจสอบสิ่งที่ return
    $builder = require_once __DIR__ . '/bootstrap/app.php';
    $builderClass = get_class($builder);
    echo "   ✓ Return value: $builderClass\n\n";
    
    // ตรวจสอบ methods ที่มีใน builder
    echo "2. Methods available in $builderClass:\n";
    $methods = get_class_methods($builder);
    foreach ($methods as $method) {
        echo "   - $method()\n";
    }
    
    echo "\n3. Trying to convert to Application instance...\n";
    
    // ลองเรียก method ต่างๆ ที่อาจจะแปลง builder เป็น Application
    $possibleMethods = ['build', 'create', 'get', 'getInstance', 'make', 'resolve'];
    $appInstance = null;
    $workingMethod = null;
    
    foreach ($possibleMethods as $method) {
        if (in_array($method, $methods)) {
            try {
                echo "   Testing $method()... ";
                $appInstance = $builder->$method();
                $appClass = get_class($appInstance);
                echo "SUCCESS! Returns: $appClass\n";
                $workingMethod = $method;
                break;
            } catch (\Exception $e) {
                echo "FAILED: " . $e->getMessage() . "\n";
            }
        }
    }
    
    if (!$appInstance) {
        throw new Exception("ไม่สามารถสร้าง Application instance ได้");
    }
    
    echo "\n4. Loading kernel and processing request...\n";
    
    // สร้าง fake HTTP request
    echo "   Creating fake request... ";
    $request = new \Illuminate\Http\Request();
    $request->server->set('REQUEST_URI', '/');
    $request->server->set('REQUEST_METHOD', 'GET');
    echo "OK\n";
    
    // โหลด HTTP kernel
    echo "   Loading HTTP kernel... ";
    $kernel = $appInstance->make(\Illuminate\Contracts\Http\Kernel::class);
    echo "OK\n";
    
    // ประมวลผล request
    echo "   Processing request... ";
    $response = $kernel->handle($request);
    echo "OK\n";
    
    // แสดงข้อมูล response
    echo "\n5. Response information:\n";
    echo "   Status code: " . $response->getStatusCode() . "\n";
    echo "   Headers: " . count($response->headers->all()) . " headers\n";
    echo "   Content length: " . strlen($response->getContent()) . " bytes\n";
    
    // แสดง content (เฉพาะส่วนแรก)
    $content = $response->getContent();
    $previewLength = min(strlen($content), 100);
    $preview = substr($content, 0, $previewLength) . (strlen($content) > $previewLength ? '...' : '');
    echo "   Content preview: $preview\n";
    
    echo "\n===== Test Completed Successfully =====\n";
    echo "Laravel works! The correct method to convert ApplicationBuilder to Application is: $workingMethod()\n";
    echo "See auto-fix-laravel.php to fix your files automatically\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
