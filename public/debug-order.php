<?php

// ตั้งค่าให้แสดง error ทั้งหมด
error_reporting(E_ALL);
ini_set('display_errors', '1');

// โหลด Laravel bootstrap
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// ทดสอบสร้างใบสั่งขาย
try {
    echo "<h1>ทดสอบการทำงานและข้อมูลระบบ</h1>";
    
    // เช็คเวอร์ชัน PHP
    echo "<p>PHP Version: " . phpversion() . "</p>";
    
    // เช็คค่า max_execution_time
    echo "<p>Max Execution Time: " . ini_get('max_execution_time') . " seconds</p>";
    
    // เช็คค่า memory_limit
    echo "<p>Memory Limit: " . ini_get('memory_limit') . "</p>";
    
    // เช็คการเชื่อมต่อฐานข้อมูล
    echo "<h2>การเชื่อมต่อฐานข้อมูล</h2>";
    try {
        $connection = \Illuminate\Support\Facades\DB::connection()->getPdo();
        echo "<p>Database: " . \Illuminate\Support\Facades\DB::connection()->getDatabaseName() . "</p>";
        echo "<p>Database Type: " . $connection->getAttribute(\PDO::ATTR_DRIVER_NAME) . "</p>";
        
        // แสดงจำนวน record ในตาราง orders
        $orderCount = \App\Models\Order::count();
        echo "<p>จำนวนใบสั่งขาย: " . $orderCount . "</p>";
        
        // ทดสอบสร้าง record ด้วย Eloquent
        echo "<h2>ทดสอบสร้าง Record ด้วย Eloquent</h2>";
        
        // ทดสอบอ่านข้อมูล Customer คนแรก
        $customer = \App\Models\Customer::first();
        echo "<p>ข้อมูลลูกค้าคนแรก: " . ($customer ? $customer->name : 'ไม่พบข้อมูลลูกค้า') . "</p>";
        
        // ทดสอบอ่านข้อมูล Product ชิ้นแรก
        $product = \App\Models\Product::first();
        echo "<p>ข้อมูลสินค้าชิ้นแรก: " . ($product ? $product->name : 'ไม่พบข้อมูลสินค้า') . "</p>";
        
        // ตรวจสอบคอลัมน์ในตาราง orders
        echo "<h2>โครงสร้างตาราง orders</h2>";
        $columns = \Illuminate\Support\Facades\Schema::getColumnListing('orders');
        echo "<ul>";
        foreach ($columns as $column) {
            echo "<li>" . $column . "</li>";
        }
        echo "</ul>";
        
    } catch (\Exception $e) {
        echo "<p style='color: red;'>เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: " . $e->getMessage() . "</p>";
        echo "<p>ที่ไฟล์: " . $e->getFile() . " บรรทัด: " . $e->getLine() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
    
} catch (\Throwable $e) {
    echo "<h1>เกิดข้อผิดพลาด</h1>";
    echo "<p>ข้อความ: " . $e->getMessage() . "</p>";
    echo "<p>ที่ไฟล์: " . $e->getFile() . " บรรทัด: " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
