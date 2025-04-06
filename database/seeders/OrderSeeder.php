<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Sales\Models\Order;
use App\Domain\Sales\Models\OrderItem;
use App\Domain\Sales\Models\Customer;
use App\Domain\Inventory\Models\Product;
use App\Domain\Inventory\Models\Unit;
use App\Domain\Organization\Models\Company;
use Illuminate\Support\Facades\Schema;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        foreach ($companies as $company) {
            $this->createOrdersForCompany($company);
        }
    }

    private function createOrdersForCompany($company)
    {
        // ตรวจสอบว่ามีลูกค้าสำหรับบริษัทนี้หรือไม่
        $customers = Customer::where('company_id', $company->id)->get();
        if ($customers->isEmpty()) {
            return;
        }

        // เลือกสินค้าสำหรับสร้าง order items
        $products = Product::where('company_id', $company->id)->limit(5)->get();
        if ($products->isEmpty()) {
            return;
        }

        // ตรวจสอบคอลัมน์ที่มีในตาราง orders และ order_items
        $orderColumns = Schema::getColumnListing('orders');
        $orderItemColumns = Schema::getColumnListing('order_items');
        
        // สร้าง Orders
        foreach ($customers as $index => $customer) {
            // สร้างเลขที่เอกสารไม่ซ้ำกัน
            $uniqueTimestamp = (int)(microtime(true) * 1000);
            $randomSuffix1 = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT) . $index;
            $randomSuffix2 = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT) . ($index + 100);
            
            // สร้างข้อมูลพื้นฐาน
            $baseOrderData = [
                'company_id' => $company->id,
                'customer_id' => $customer->id,
                'order_number' => 'PO' . date('Ym') . $randomSuffix1,
                'order_date' => now(),
                'delivery_date' => now()->addDays(7),
                'status' => 'confirmed',
                'subtotal' => 50000,
                'total_amount' => 52430,
                'notes' => 'จัดส่งในเวลาทำการ',
                'terms' => 'ชำระเงินภายใน 30 วัน',
                'shipping_address' => '123/45 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110',
                'shipping_method' => 'standard',
                'payment_method' => 'credit',
                'payment_terms' => '30',
                'prepared_by' => 1,
                'approved_by' => 1,
                'approved_at' => now(),
                'metadata' => json_encode([
                    'source' => 'direct',
                    'priority' => 'normal',
                    'department' => 'sales',
                    'currency' => 'THB',
                    'discount_type' => 'fixed',
                    'discount_amount' => 1000,
                    'tax_inclusive' => false,
                    'tax_rate' => 7,
                    'tax_amount' => 3430,
                    'total_discount' => 1000,
                ])
            ];
            
            $secondOrderData = [
                'company_id' => $company->id,
                'customer_id' => $customer->id,
                'order_number' => 'PO' . date('Ym') . $randomSuffix2,
                'order_date' => now()->subDays(5),
                'delivery_date' => now()->addDays(2),
                'status' => 'delivered',
                'subtotal' => 75000,
                'total_amount' => 71250,
                'notes' => 'จัดส่งด่วน',
                'terms' => 'ชำระเงินล่วงหน้า',
                'shipping_address' => '456/78 ถนนพระราม 9 แขวงบางกะปิ เขตห้วยขวาง กรุงเทพฯ 10310',
                'shipping_method' => 'express',
                'payment_method' => 'prepaid',
                'payment_terms' => 'prepaid',
                'prepared_by' => 1,
                'approved_by' => 1,
                'approved_at' => now()->subDays(5),
                'delivered_at' => now(),
                'metadata' => json_encode([
                    'source' => 'website',
                    'priority' => 'high',
                    'department' => 'marketing',
                    'currency' => 'THB',
                    'discount_type' => 'percentage',
                    'discount_amount' => 5,
                    'tax_inclusive' => true,
                    'tax_rate' => 7,
                    'tax_amount' => 4747.50,
                    'total_discount' => 3750,
                ])
            ];

            $orderDataArray = [$baseOrderData, $secondOrderData];
            
            // Process each order
            foreach ($orderDataArray as $orderData) {
                // กรองเฉพาะคอลัมน์ที่มีอยู่ในตาราง orders
                $filteredOrderData = array_intersect_key($orderData, array_flip($orderColumns));
                
                // สร้าง order
                try {
                    $order = Order::create($filteredOrderData);
                } catch (\Exception $e) {
                    // หากเกิดข้อผิดพลาด UniqueConstraintViolation ให้สร้างเลขที่ใหม่และลองอีกครั้ง
                    if ($e instanceof \Illuminate\Database\UniqueConstraintViolationException) {
                        // สร้างเลขที่ใหม่ที่มั่นใจว่าไม่ซ้ำ
                        $uniqueID = uniqid('', true);
                        $filteredOrderData['order_number'] = 'PO' . date('Ym') . substr(md5($uniqueID . $index), 0, 6);
                        
                        // ลองบันทึกอีกครั้ง
                        try {
                            $order = Order::create($filteredOrderData);
                        } catch (\Exception $innerEx) {
                            // บันทึกข้อผิดพลาดถ้ายังไม่สำเร็จ
                            \Log::error("Failed to create order after retry: " . $innerEx->getMessage());
                            continue; // ข้ามไปยังรายการถัดไป
                        }
                    } else {
                        // บันทึกข้อผิดพลาดประเภทอื่น
                        \Log::error("Error creating order: " . $e->getMessage());
                        continue; // ข้ามไปยังรายการถัดไป
                    }
                }

                // สร้าง order items เมื่อสร้าง order สำเร็จ
                if (isset($order) && $order) {
                    // แก้ไขการใช้งาน random() เพื่อรองรับกรณีที่มีสินค้าน้อย
                    $itemCount = min(mt_rand(1, 3), count($products)); 
                    $selectedProducts = $itemCount > 0 ? $products->random($itemCount) : collect([$products->first()]);
                    
                    foreach ($selectedProducts as $product) {
                        if (!$product) {
                            continue;
                        }
                        
                        $quantity = mt_rand(1, 5);
                        $unitPrice = $product->price ?? 100;
                        $subtotal = $quantity * $unitPrice;
                        
                        // สร้างข้อมูลพื้นฐานสำหรับ OrderItem
                        $orderItemData = [
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'description' => $product->name ?? 'สินค้า',
                            'quantity' => $quantity,
                            'unit' => 'ชิ้น',
                            'unit_price' => $unitPrice,
                            'price' => $unitPrice,
                            'subtotal' => $subtotal,
                            'tax_rate' => 7,
                            'tax_amount' => $subtotal * 0.07,
                            'total' => $subtotal * 1.07,
                            'metadata' => json_encode([
                                'unit_name' => 'ชิ้น',
                                'unit_id' => $product->unit_id,
                                'product_price' => $unitPrice,
                                'product_subtotal' => $subtotal,
                            ])
                        ];
                        
                        // กรองเฉพาะคอลัมน์ที่มีอยู่ในตาราง order_items
                        $filteredOrderItemData = array_intersect_key($orderItemData, array_flip($orderItemColumns));
                        
                        // ตรวจสอบคอลัมน์ที่จำเป็นต้องมีข้อมูล
                        if (in_array('price', $orderItemColumns) && !isset($filteredOrderItemData['price'])) {
                            $filteredOrderItemData['price'] = $unitPrice;
                        }
                        
                        if (in_array('unit_price', $orderItemColumns) && !isset($filteredOrderItemData['unit_price'])) {
                            $filteredOrderItemData['unit_price'] = $unitPrice;
                        }
                        
                        // สร้าง order item
                        try {
                            OrderItem::create($filteredOrderItemData);
                        } catch (\Exception $e) {
                            \Log::error("Error creating order item: " . $e->getMessage(), [
                                'data' => $filteredOrderItemData
                            ]);
                        }
                    }
                }
            }
        }
    }
}
