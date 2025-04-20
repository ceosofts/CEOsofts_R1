<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\User;
use App\Models\Customer;
use Carbon\Carbon;

class DeliveryOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "เริ่มสร้างข้อมูลใบส่งสินค้า...\n";
        
        // ปรับเงื่อนไขการค้นหา - ยอมรับสถานะมากขึ้น
        $orders = Order::where(function($query) {
            $query->where('status', 'confirmed')
                ->orWhere('status', 'processing')
                ->orWhere('status', 'pending')
                ->orWhere('status', 'draft')
                ->orWhereNull('status');
        })->get();
        
        if ($orders->isEmpty()) {
            // ถ้ายังไม่เจอใบสั่งขาย ให้ดึงมาทั้งหมดเลย
            $orders = Order::limit(5)->get();
            
            if ($orders->isEmpty()) {
                echo "ไม่พบใบสั่งขายในระบบสำหรับการสร้างใบส่งสินค้า\n";
                return;
            }
        }
        
        echo "พบใบสั่งขายที่สามารถใช้สร้างใบส่งสินค้า: {$orders->count()} รายการ\n";
        
        // ดึงข้อมูลผู้ใช้สำหรับ created_by และ approved_by
        $users = User::all();
        if ($users->isEmpty()) {
            echo "ไม่พบข้อมูลผู้ใช้ที่จำเป็นต่อการสร้างใบส่งสินค้า\n";
            return;
        }
        
        $createdCount = 0;
        
        // สร้างใบส่งสินค้าสำหรับใบสั่งขายแต่ละรายการ
        foreach ($orders as $index => $order) {
            try {
                // โหลดข้อมูลลูกค้าให้ครบถ้วน
                $customer = Customer::find($order->customer_id);
                if (!$customer) {
                    echo "  ข้ามใบสั่งขาย ID: {$order->id} เนื่องจากไม่พบข้อมูลลูกค้า\n";
                    continue;
                }

                // สร้างเลขที่เอกสารใบส่งสินค้าตามรูปแบบใหม่ DO2025040001
                $currentDate = Carbon::now();
                $year = $currentDate->format('Y');
                $month = $currentDate->format('m');
                $number = str_pad($index + 1, 4, '0', STR_PAD_LEFT); // เริ่มที่ 0001
                $deliveryNumber = 'DO' . $year . $month . $number;
                
                // สุ่มเลือกผู้ใช้ที่เป็นผู้สร้าง
                $createdBy = $users->random()->id;
                
                // สุ่มวันที่ส่งสินค้า (อยู่ในช่วง 1-7 วันหลังจากวันที่สร้างใบสั่งขาย)
                $orderDate = $order->order_date ?? now()->format('Y-m-d');
                $deliveryDate = Carbon::parse($orderDate)->addDays(rand(1, 7))->format('Y-m-d');
                
                // สถานะการจัดส่ง
                $statuses = ['pending', 'delivered', 'partial_delivered'];
                $deliveryStatus = $statuses[array_rand($statuses)];
                
                // กำหนดค่า shipping_address ที่ไม่เป็น null
                $shippingAddress = $order->shipping_address ?? $customer->address ?? 'ที่อยู่ตามเอกสาร';
                
                // สร้างข้อมูลใบส่งสินค้า - ลบ shipping_contact ออกเพราะไม่มีในตาราง
                $deliveryOrderId = DB::table('delivery_orders')->insertGetId([
                    'company_id' => $order->company_id,
                    'order_id' => $order->id,
                    'customer_id' => $order->customer_id,
                    'delivery_number' => $deliveryNumber,
                    'delivery_date' => $deliveryDate,
                    'delivery_status' => $deliveryStatus,
                    'shipping_address' => $shippingAddress,
                    'shipping_method' => ['ขนส่งบริษัท', 'ไปรษณีย์ไทย', 'Kerry Express', 'Flash Express'][rand(0, 3)],
                    'tracking_number' => 'TRK' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
                    'notes' => rand(0, 1) ? 'ส่งในเวลาทำการ 9:00-17:00 น.' : null,
                    'created_by' => $createdBy,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                // สร้างรายการสินค้าในใบส่งสินค้า
                $orderItems = $order->orderItems ?? [];
                if (count($orderItems) > 0) {
                    foreach ($orderItems as $orderItem) {
                        DB::table('delivery_order_items')->insert([
                            'delivery_order_id' => $deliveryOrderId,
                            'order_item_id' => $orderItem->id,
                            'product_id' => $orderItem->product_id,
                            'description' => $orderItem->description ?? 'รายการสินค้า',
                            'quantity' => $orderItem->quantity,
                            'unit' => $orderItem->unit ?? 'ชิ้น',
                            'status' => $deliveryStatus === 'delivered' ? 'delivered' : 
                                      ($deliveryStatus === 'partial_delivered' ? ['delivered', 'pending'][rand(0, 1)] : 'pending'),
                            'notes' => null,
                            'metadata' => json_encode([
                                'source' => 'seeder',
                                'timestamp' => now()->timestamp
                            ]),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                } else {
                    // กรณีไม่มีรายการสินค้า ให้สร้างรายการตัวอย่าง
                    DB::table('delivery_order_items')->insert([
                        'delivery_order_id' => $deliveryOrderId,
                        'order_item_id' => null,
                        'product_id' => null,
                        'description' => 'สินค้าตัวอย่าง',
                        'quantity' => rand(1, 10),
                        'unit' => 'ชิ้น',
                        'status' => $deliveryStatus,
                        'notes' => null,
                        'metadata' => json_encode([
                            'source' => 'seeder',
                            'timestamp' => now()->timestamp
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                
                echo "  สร้างใบส่งสินค้า: $deliveryNumber สำเร็จ\n";
                $createdCount++;
                
                // อัพเดทสถานะใบสั่งขาย
                if ($deliveryStatus === 'delivered') {
                    $order->update(['status' => 'delivered', 'delivery_date' => $deliveryDate]);
                } elseif ($deliveryStatus === 'partial_delivered') {
                    $order->update(['status' => 'partial_delivered']);
                }
                
                // สร้างข้อมูลตัวอย่างเพียง 5 รายการ
                if ($createdCount >= 5) {
                    break;
                }
            } catch (\Exception $e) {
                echo "  เกิดข้อผิดพลาด: {$e->getMessage()}\n";
                continue;
            }
        }
        
        echo "สร้างข้อมูลใบส่งสินค้าเสร็จสิ้น: $createdCount รายการ\n";
    }
}
