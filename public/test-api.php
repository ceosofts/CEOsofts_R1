<?php
/**
 * ไฟล์สำหรับทดสอบ API โดยตรงไม่ผ่าน Laravel Router
 * วิธีใช้: เปิดในเบราว์เซอร์ที่ /test-api.php?id=1
 */

define('LARAVEL_START', microtime(true));
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$id = $_GET['id'] ?? null;
$controller = new \App\Http\Controllers\OrderController();
$request = \Illuminate\Http\Request::capture();

echo '<h1>API Test</h1>';
echo '<pre>';

if (!$id) {
    echo 'กรุณาระบุ ID เช่น ?id=1';
    exit;
}

echo "Testing API with ID: $id\n\n";
$response = $controller->getOrderProducts($request, $id);
$content = $response->getContent();

echo "Status Code: " . $response->getStatusCode() . "\n\n";
echo "Headers: \n";
print_r($response->headers->all());
echo "\n\nContent:\n";
echo json_encode(json_decode($content), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
