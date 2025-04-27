<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Order;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class DeliveryOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "เริ่มสร้างข้อมูลใบส่งสินค้า...\n";
        
        try {
            // ตรวจสอบว่าตาราง delivery_orders มีคอลัมน์ที่จำเป็นหรือไม่
            $hasDeliveryStatus = Schema::hasColumn('delivery_orders', 'delivery_status');
            $hasStatus = Schema::hasColumn('delivery_orders', 'status');
            $hasShippingMethod = Schema::hasColumn('delivery_orders', 'shipping_method');
            
            if (!$hasDeliveryStatus && !$hasStatus) {
                echo "ตาราง delivery_orders ไม่มีคอลัมน์สถานะ (status หรือ delivery_status)\n";
                return;
            }
            
            if (!$hasShippingMethod) {
                echo "ตาราง delivery_orders ไม่มีคอลัมน์ shipping_method\n";
            }
            
            // ค้นหาใบสั่งขายที่มีสถานะ shipped หรือ processing หรือ delivered
            $validOrders = Order::whereIn('status', ['shipped', 'processing', 'delivered'])
                              ->take(20) // จำกัดจำนวนเพื่อทดสอบ
                              ->get();
                              
            echo "พบใบสั่งขายที่สามารถใช้สร้างใบส่งสินค้า: " . $validOrders->count() . " รายการ\n";
            
            // สร้างใบส่งสินค้าสำหรับแต่ละใบสั่งขาย
            $count = 0;
            $faker = \Faker\Factory::create('th_TH');
            
            foreach ($validOrders as $order) {
                try {
                    // เริ่ม transaction
                    DB::beginTransaction();

                    // สร้างเลขที่ใบส่งสินค้า
                    $year = date('Y');
                    $month = date('m');
                    $deliveryNumber = 'DO' . $year . $month . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
                    
                    // สุ่มข้อมูลต่างๆ
                    $deliveryDate = $faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d');
                    $creator = User::inRandomOrder()->first();
                    $deliveryStatus = $faker->randomElement(['pending', 'partial_delivered', 'delivered']);
                    $trackingNumber = 'TRK' . $faker->numerify('######');
                    $shippingMethod = $faker->randomElement(['Kerry Express', 'Flash Express', 'ไปรษณีย์ไทย', 'ขนส่งบริษัท']);
                    $notes = $faker->boolean(30) ? $faker->sentence() : ($faker->boolean(50) ? 'ส่งในเวลาทำการ 9:00-17:00 น.' : null);
                    
                    // เตรียมข้อมูลสำหรับสร้าง DeliveryOrder
                    $deliveryData = [
                        'company_id' => $order->company_id,
                        'order_id' => $order->id,
                        'customer_id' => $order->customer_id,
                        'delivery_number' => $deliveryNumber,
                        'delivery_date' => $deliveryDate,
                        'tracking_number' => $trackingNumber,
                        'notes' => $notes,
                        'created_by' => $creator->id
                    ];
                    
                    // ใส่ข้อมูล status หรือ delivery_status ตามที่มีในฐานข้อมูล
                    if ($hasStatus) {
                        $deliveryData['status'] = $deliveryStatus;
                    }
                    
                    if ($hasDeliveryStatus) {
                        $deliveryData['delivery_status'] = $deliveryStatus;
                    }
                    
                    // ใส่ shipping_method ถ้ามีคอลัมน์นี้
                    if ($hasShippingMethod) {
                        $deliveryData['shipping_method'] = $shippingMethod;
                    }
                    
                    // ใส่ shipping_address ถ้ามี
                    if (Schema::hasColumn('delivery_orders', 'shipping_address')) {
                        $deliveryData['shipping_address'] = $order->shipping_address;
                    } else if (Schema::hasColumn('delivery_orders', 'delivery_address')) {
                        $deliveryData['delivery_address'] = $order->shipping_address;
                    }
                    
                    // สร้าง DeliveryOrder
                    $delivery = DeliveryOrder::create($deliveryData);
                    
                    // สร้าง DeliveryOrderItem สำหรับทุก OrderItem
                    foreach ($order->items as $orderItem) {
                        DeliveryOrderItem::create([
                            'delivery_order_id' => $delivery->id,
                            'order_item_id' => $orderItem->id,
                            'product_id' => $orderItem->product_id,
                            'quantity' => $orderItem->quantity,
                            'description' => $orderItem->description
                        ]);
                    }
                    
                    // ยืนยัน transaction
                    DB::commit();
                    $count++;
                } catch (\Exception $e) {
                    // กรณีเกิด error ให้ rollback transaction
                    DB::rollBack();
                    
                    echo "  เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
                    Log::error('เกิดข้อผิดพลาดในการสร้างใบส่งสินค้า: ' . $e->getMessage(), [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number
                    ]);
                }
            }
            
            echo "สร้างข้อมูลใบส่งสินค้าเสร็จสิ้น: {$count} รายการ\n";
            
        } catch (\Exception $e) {
            echo "เกิดข้อผิดพลาดในการสร้างข้อมูลใบส่งสินค้า: " . $e->getMessage() . "\n";
            Log::error('เกิดข้อผิดพลาดในการสร้างข้อมูลใบส่งสินค้า: ' . $e->getMessage());
        }
    }
}
