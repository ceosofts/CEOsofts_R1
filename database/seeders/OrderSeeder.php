<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ตรวจสอบโครงสร้างตาราง order_items
        $orderItemColumns = Schema::getColumnListing('order_items');
        
        // สร้าง column mapping สำหรับตรวจสอบ
        $hasOrderItemColumns = [];
        foreach ($orderItemColumns as $column) {
            $hasOrderItemColumns[$column] = true;
        }
        
        // ตรวจสอบคอลัมน์ในตาราง orders
        $orderColumns = Schema::getColumnListing('orders');
        $hasOrderColumns = [];
        foreach ($orderColumns as $column) {
            $hasOrderColumns[$column] = true;
        }
        
        // ตรวจสอบว่ามีข้อมูลที่จำเป็นสำหรับการสร้างใบสั่งขาย
        $customers = Customer::all();
        $products = Product::all();
        $users = User::all();

        if ($customers->isEmpty() || $products->isEmpty() || $users->isEmpty()) {
            $this->command->info('ไม่สามารถสร้างข้อมูลใบสั่งขายเนื่องจากไม่มีข้อมูลลูกค้า สินค้า หรือผู้ใช้งาน');
            return;
        }

        // สถานะที่เป็นไปได้ของใบสั่งขาย
        $statuses = ['draft', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
        
        $this->command->info('เริ่มสร้างข้อมูลใบสั่งขาย...');
        
        // ใช้ Transaction เพื่อความปลอดภัย
        DB::beginTransaction();
        
        try {
            // สร้างใบสั่งขาย 10 รายการ
            for ($i = 0; $i < 10; $i++) {
                $orderDate = Carbon::now()->subDays(rand(1, 30));
                $status = Arr::random($statuses);
                $customer = $customers->random();
                $creator = $users->random();
                
                // คำนวณตัวเลขทางการเงินเบื้องต้น
                $randomProducts = $products->random(rand(1, 5));
                $tempSubtotal = 0;
                
                foreach ($randomProducts as $product) {
                    $quantity = rand(1, 10);
                    $unitPrice = $product->price;
                    $tempSubtotal += $quantity * $unitPrice;
                }
                
                $tempDiscountType = Arr::random(['fixed', 'percentage', null]);
                $tempDiscountAmount = 0;
                
                if ($tempDiscountType === 'fixed') {
                    $tempDiscountAmount = min(rand(100, 1000), $tempSubtotal * 0.2);
                } elseif ($tempDiscountType === 'percentage') {
                    $tempDiscountRate = rand(5, 15);
                    $tempDiscountAmount = $tempSubtotal * ($tempDiscountRate / 100);
                }
                
                $tempNetTotal = $tempSubtotal - $tempDiscountAmount;
                $tempTaxRate = rand(0, 1) ? 7 : 0;
                $tempTaxAmount = $tempTaxRate ? $tempNetTotal * ($tempTaxRate / 100) : 0;
                $tempShippingCost = rand(0, 5) * 100;
                $tempTotalAmount = $tempNetTotal + $tempTaxAmount + $tempShippingCost;

                // สร้างข้อมูลเบื้องต้นสำหรับใบสั่งขาย
                $orderData = [
                    'company_id' => 1,
                    'customer_id' => $customer->id,
                    'quotation_id' => null,
                    'order_number' => 'SO' . date('Ym') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                    'order_date' => $orderDate,
                    'delivery_date' => $orderDate->copy()->addDays(rand(7, 14)),
                    'status' => $status,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ];
                
                // เพิ่มคอลัมน์ที่มีในฐานข้อมูล
                if (isset($hasOrderColumns['customer_po_number'])) {
                    $orderData['customer_po_number'] = 'PO' . date('Ym') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
                }
                
                if (isset($hasOrderColumns['created_by'])) {
                    $orderData['created_by'] = $creator->id;
                }
                
                if (isset($hasOrderColumns['notes'])) {
                    $orderData['notes'] = Arr::random(['กรุณาจัดส่งด่วน', 'ติดต่อก่อนจัดส่ง', 'ลูกค้า VIP', null]);
                }
                
                if (isset($hasOrderColumns['payment_terms'])) {
                    $orderData['payment_terms'] = Arr::random(['30 วัน', '15 วัน', '7 วัน', 'ชำระเงินทันที', null]);
                }
                
                if (isset($hasOrderColumns['shipping_address'])) {
                    $orderData['shipping_address'] = $customer->address;
                }
                
                if (isset($hasOrderColumns['shipping_method'])) {
                    $orderData['shipping_method'] = Arr::random(['รถบริษัท', 'Kerry', 'Flash', 'ไปรษณีย์ไทย', null]);
                }
                
                if (isset($hasOrderColumns['shipping_cost'])) {
                    $orderData['shipping_cost'] = $tempShippingCost;
                }
                
                // เพิ่มข้อมูลการเงิน
                if (isset($hasOrderColumns['subtotal'])) {
                    $orderData['subtotal'] = $tempSubtotal;
                }
                
                if (isset($hasOrderColumns['discount_type'])) {
                    $orderData['discount_type'] = $tempDiscountType;
                }
                
                if (isset($hasOrderColumns['discount_amount'])) {
                    $orderData['discount_amount'] = $tempDiscountAmount;
                }
                
                if (isset($hasOrderColumns['tax_rate'])) {
                    $orderData['tax_rate'] = $tempTaxRate;
                }
                
                if (isset($hasOrderColumns['tax_amount'])) {
                    $orderData['tax_amount'] = $tempTaxAmount;
                }
                
                if (isset($hasOrderColumns['total_amount'])) {
                    $orderData['total_amount'] = $tempTotalAmount;
                }

                // สร้างใบสั่งขาย
                $order = Order::create($orderData);
                
                // อัพเดทข้อมูลสถานะ
                $this->updateOrderStatus($order, $status, $orderDate, $users, $hasOrderColumns);

                // สร้างรายการสินค้า 
                $subtotal = 0;
                
                foreach ($randomProducts as $product) {
                    $quantity = rand(1, 10);
                    $unitPrice = $product->price;
                    $total = $quantity * $unitPrice;
                    $subtotal += $total;

                    // สร้างข้อมูลพื้นฐานสำหรับ OrderItem
                    $itemData = [
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'description' => $product->name,
                        'quantity' => $quantity,
                        'created_at' => $orderDate,
                        'updated_at' => $orderDate,
                        'total' => $total,
                    ];
                    
                    // ใส่ข้อมูลตามโครงสร้างตาราง
                    if (isset($hasOrderItemColumns['unit_price'])) {
                        $itemData['unit_price'] = $unitPrice;
                    }
                    
                    if (isset($hasOrderItemColumns['price'])) {
                        $itemData['price'] = $unitPrice;
                    }
                    
                    if (isset($hasOrderItemColumns['unit_id'])) {
                        $itemData['unit_id'] = $product->unit_id ?? 1; // default = 1 ถ้าไม่มี
                    }
                    
                    if (isset($hasOrderItemColumns['sku'])) {
                        $itemData['sku'] = $product->sku ?? null;
                    }
                    
                    if (isset($hasOrderItemColumns['notes'])) {
                        $itemData['notes'] = rand(0, 1) ? 'หมายเหตุ: ' . $product->name : null;
                    }

                    OrderItem::create($itemData);
                }

                // อัพเดทยอดเงินใบสั่งขาย
                $this->updateOrderFinancials($order, $subtotal, $hasOrderColumns);
            }
            
            DB::commit();
            $this->command->info('สร้างข้อมูลใบสั่งขายจำนวน 10 รายการเรียบร้อยแล้ว');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('เกิดข้อผิดพลาด: ' . $e->getMessage());
            $this->command->error('ที่ไฟล์: ' . $e->getFile() . ' บรรทัด: ' . $e->getLine());
        }
    }

    /**
     * อัพเดทสถานะใบสั่งขาย
     */
    private function updateOrderStatus($order, $status, $orderDate, $users, $hasOrderColumns)
    {
        $updateData = [];
        
        if ($status != 'draft' && isset($hasOrderColumns['confirmed_by']) && isset($hasOrderColumns['confirmed_at'])) {
            $updateData['confirmed_by'] = $users->random()->id;
            $updateData['confirmed_at'] = $orderDate->copy()->addHours(rand(1, 24));
        }

        if (in_array($status, ['processing', 'shipped', 'delivered']) && 
            isset($hasOrderColumns['processed_by']) && isset($hasOrderColumns['processed_at'])) {
            $updateData['processed_by'] = $users->random()->id;
            $updateData['processed_at'] = isset($updateData['confirmed_at']) ? 
                Carbon::parse($updateData['confirmed_at'])->addHours(rand(1, 48)) : 
                $orderDate->copy()->addHours(rand(24, 72));
        }

        // อัพเดทสถานะเพิ่มเติม...
        
        if (!empty($updateData)) {
            $order->update($updateData);
        }
    }

    /**
     * อัพเดทข้อมูลการเงินของใบสั่งขาย
     */
    private function updateOrderFinancials($order, $subtotal, $hasOrderColumns)
    {
        $updateData = [];
        
        $discountType = Arr::random(['fixed', 'percentage', null]);
        $discountAmount = 0;
        
        if ($discountType === 'fixed') {
            $discountAmount = min(rand(100, 1000), $subtotal * 0.2);
        } elseif ($discountType === 'percentage') {
            $discountRate = rand(5, 15);
            $discountAmount = $subtotal * ($discountRate / 100);
        }
        
        $netTotal = $subtotal - $discountAmount;
        $taxRate = rand(0, 1) ? 7 : 0;
        $taxAmount = $taxRate ? $netTotal * ($taxRate / 100) : 0;
        $shippingCost = $order->shipping_cost ?? 0;
        $totalAmount = $netTotal + $taxAmount + $shippingCost;
        
        if (isset($hasOrderColumns['subtotal'])) {
            $updateData['subtotal'] = $subtotal;
        }
        
        if (isset($hasOrderColumns['discount_type'])) {
            $updateData['discount_type'] = $discountType;
        }
        
        // อัพเดทข้อมูลการเงินเพิ่มเติม...
        
        if (!empty($updateData)) {
            $order->update($updateData);
        }
    }
}
