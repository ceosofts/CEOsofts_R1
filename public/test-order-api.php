<?php
/**
 * ไฟล์ทดสอบ API สำหรับดึงข้อมูล Order
 * วิธีใช้: เรียก /test-order-api.php?id=1 ในเบราว์เซอร์
 */
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Order;
use Illuminate\Support\Facades\DB;

header('Content-Type: text/html; charset=utf-8');

echo "<html><head><title>ทดสอบ API</title>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; line-height: 1.6; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow: auto; }
    .success { color: green; }
    .error { color: red; }
    .info { color: blue; }
    .section { margin-bottom: 20px; border-bottom: 1px solid #ccc; padding-bottom: 10px; }
</style>";
echo "</head><body>";

echo "<h1>ทดสอบ API ดึงข้อมูลใบสั่งขาย</h1>";

// รับค่า ID จาก query string
$id = isset($_GET['id']) ? $_GET['id'] : null;

// เตรียมฟอร์มเพื่อทดสอบ
echo "<div class='section'>";
echo "<h2>เลือก Order ID เพื่อทดสอบ</h2>";
echo "<form method='get'>";
echo "Order ID: <input type='text' name='id' value='$id'> ";
echo "<input type='submit' value='ทดสอบ'>";
echo "</form>";
echo "</div>";

if ($id) {
    try {
        echo "<div class='section'>";
        echo "<h2>ผลการทดสอบ</h2>";
        
        // แสดง ID ที่ได้รับ
        echo "<p><strong>ID ที่รับมา:</strong> <span class='info'>" . htmlspecialchars($id) . "</span> (Type: " . gettype($id) . ")</p>";
        
        // ทดสอบการเรียกใช้ Model
        echo "<h3>1. ทดสอบดึงข้อมูลจาก Database</h3>";
        $order = Order::find($id);
        
        if ($order) {
            echo "<p class='success'>✓ พบข้อมูลใบสั่งขาย ID: $id</p>";
            echo "<pre>";
            echo "Order Number: " . $order->order_number . "\n";
            echo "Customer ID: " . $order->customer_id . "\n";
            echo "Status: " . $order->status . "\n";
            echo "</pre>";
            
            // ลองโหลด relationships
            echo "<h3>2. ทดสอบโหลด Relationships</h3>";
            
            // ทดสอบ customer
            $customer = $order->customer;
            if ($customer) {
                echo "<p class='success'>✓ โหลดข้อมูลลูกค้าสำเร็จ</p>";
                echo "<pre>";
                echo "Customer Name: " . $customer->name . "\n";
                echo "Customer Email: " . $customer->email . "\n";
                echo "</pre>";
            } else {
                echo "<p class='error'>✗ ไม่พบข้อมูลลูกค้า</p>";
            }
            
            // ทดสอบ items
            $items = $order->items;
            if ($items->count() > 0) {
                echo "<p class='success'>✓ โหลดรายการสินค้าสำเร็จ (" . $items->count() . " รายการ)</p>";
                echo "<pre>";
                foreach ($items as $index => $item) {
                    echo "รายการที่ " . ($index + 1) . ":\n";
                    echo "  Product ID: " . $item->product_id . "\n";
                    echo "  Description: " . $item->description . "\n";
                    echo "  Quantity: " . $item->quantity . "\n";
                    echo "  Unit Price: " . $item->unit_price . "\n";
                    
                    // ตรวจสอบสินค้า
                    if ($item->product) {
                        echo "  Product Name: " . $item->product->name . "\n";
                    } else {
                        echo "  Product: Not found\n";
                    }
                    echo "\n";
                }
                echo "</pre>";
            } else {
                echo "<p class='error'>✗ ไม่พบรายการสินค้าในใบสั่งขาย</p>";
            }
            
            // ทดสอบสร้างข้อมูลแบบเดียวกับ API
            echo "<h3>3. ทดสอบสร้างข้อมูล API Response</h3>";
            
            $order->load(['customer', 'items.product']);
            
            $responseData = [
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'customer_id' => $order->customer_id,
                    'shipping_address' => $order->shipping_address ?? '',
                    'shipping_method' => $order->shipping_method ?? '',
                    'delivery_date' => $order->delivery_date ? $order->delivery_date->format('Y-m-d') : null,
                ],
                'customer' => $order->customer,
                'items' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'description' => $item->description ?? '',
                        'quantity' => $item->quantity,
                        'unit_name' => $item->product ? $item->product->unit : '',
                    ];
                }),
            ];
            
            echo "<p class='success'>✓ สร้างข้อมูล API Response สำเร็จ</p>";
            echo "<pre>";
            echo json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            echo "</pre>";
            
            // ทดสอบ API URL
            echo "<h3>4. ทดสอบเรียก API โดยตรง</h3>";
            $apiUrl = url("/order-products/{$id}");
            echo "<p><a href='$apiUrl' target='_blank'>เปิด API URL: $apiUrl</a></p>";
            
        } else {
            echo "<p class='error'>✗ ไม่พบใบสั่งขาย ID: $id</p>";
        }
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<p class='error'>เกิดข้อผิดพลาด: " . $e->getMessage() . "</p>";
        echo "<pre>";
        echo "File: " . $e->getFile() . "\n";
        echo "Line: " . $e->getLine() . "\n";
        echo "</pre>";
    }
}

echo "</body></html>";
