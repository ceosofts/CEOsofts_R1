<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class TestOrderCreation extends Command
{
    protected $signature = 'test:create-order';
    protected $description = 'ทดสอบการสร้างใบสั่งขาย';

    public function handle()
    {
        $this->info('เริ่มการทดสอบสร้างใบสั่งขาย...');

        try {
            // ดึงลูกค้าคนแรกและสินค้าชิ้นแรก
            $customer = Customer::first();
            $product = Product::first();

            if (!$customer) {
                $this->error('ไม่พบข้อมูลลูกค้า');
                return 1;
            }

            if (!$product) {
                $this->error('ไม่พบข้อมูลสินค้า');
                return 1;
            }

            $this->info("ลูกค้า: {$customer->name} (ID: {$customer->id})");
            $this->info("สินค้า: {$product->name} (ID: {$product->id})");

            // ทดสอบบันทึกแบบที่ 1 - ใช้ Query Builder โดยตรง
            $this->info('วิธีที่ 1: ใช้ Query Builder');
            $orderData = [
                'company_id' => 1,
                'customer_id' => $customer->id,
                'order_number' => 'TESTCLI-' . time(),
                'order_date' => now(),
                'status' => 'draft',
                'total_amount' => 100,
                'subtotal' => 100,
                'discount_type' => 'fixed',
                'discount_amount' => 0,
                'tax_rate' => 7,
                'tax_amount' => 7,
                'shipping_cost' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ];

            DB::beginTransaction();
            $orderId = DB::table('orders')->insertGetId($orderData);
            
            // สร้างรายการสินค้า
            DB::table('order_items')->insert([
                'order_id' => $orderId,
                'product_id' => $product->id,
                'description' => $product->name,
                'quantity' => 1,
                'unit_price' => 100,
                'unit_id' => $product->unit_id,
                'total' => 100,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            DB::commit();

            $this->info("สร้างใบสั่งขายสำเร็จ ด้วย Query Builder - ID: {$orderId}");
            
            // ทดสอบวิธีที่ 2 - ใช้ Model
            $this->info('วิธีที่ 2: ใช้ Model Eloquent');
            
            try {
                $order = new Order();
                $order->company_id = 1;
                $order->customer_id = $customer->id;
                $order->order_number = 'TESTMDL-' . time();
                $order->order_date = now();
                $order->status = 'draft';
                $order->total_amount = 100;
                $order->subtotal = 100;
                $order->discount_type = 'fixed';
                $order->discount_amount = 0;
                $order->tax_rate = 7;
                $order->tax_amount = 7;
                $order->shipping_cost = 0;
                $order->save();
                
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $product->id;
                $orderItem->description = $product->name;
                $orderItem->quantity = 1;
                $orderItem->unit_price = 100;
                $orderItem->unit_id = $product->unit_id;
                $orderItem->total = 100;
                $orderItem->save();
                
                $this->info("สร้างใบสั่งขายสำเร็จ ด้วย Model - ID: {$order->id}");
            } catch (\Exception $e) {
                $this->error("เกิดข้อผิดพลาดในวิธีที่ 2: " . $e->getMessage());
                Log::error('เกิดข้อผิดพลาดในการสร้างด้วย Model', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
            
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->error("เกิดข้อผิดพลาด: {$e->getMessage()}");
            $this->error("ที่ไฟล์: {$e->getFile()}:{$e->getLine()}");
            
            Log::error('เกิดข้อผิดพลาดในการทดสอบสร้างใบสั่งขาย', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return 1;
        }
    }
}
