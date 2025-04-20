<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // สำหรับแต่ละบริษัท
        $companies = Company::all();
        $faker = \Faker\Factory::create('th_TH');

        try {
            echo "เริ่มสร้างใบสั่งขาย...\n";
            
            foreach ($companies as $company) {
                // สร้างใบสั่งขายจำนวน 10-15 รายการต่อบริษัท
                $orderCount = rand(10, 15);
                echo "สร้างใบสั่งขายสำหรับบริษัท {$company->name} จำนวน {$orderCount} รายการ\n";
                
                // ดึงลูกค้าของบริษัท
                $customers = Customer::where('company_id', $company->id)->get();
                if ($customers->isEmpty()) {
                    echo "  ไม่พบลูกค้าสำหรับบริษัท {$company->name} ข้ามไป\n";
                    continue; // ข้ามถ้าไม่มีลูกค้า
                }
                
                // ดึงผู้ใช้ในบริษัท
                $users = User::whereHas('companies', function ($query) use ($company) {
                    $query->where('companies.id', $company->id);
                })->get();
                
                if ($users->isEmpty()) {
                    $users = User::take(1)->get(); // ใช้ผู้ใช้คนแรกถ้าไม่มีผู้ใช้ในบริษัท
                }
                
                // ดึงสินค้าของบริษัท
                $products = Product::where('company_id', $company->id)->get();
                if ($products->isEmpty()) {
                    echo "  ไม่พบสินค้าสำหรับบริษัท {$company->name} ข้ามไป\n";
                    continue; // ข้ามถ้าไม่มีสินค้า
                }
                
                // ตรวจสอบว่ามีคอลัมน์ที่จำเป็นในฐานข้อมูลหรือไม่
                $hasConfirmedFields = Schema::hasColumn('orders', 'confirmed_by') && Schema::hasColumn('orders', 'confirmed_at');
                $hasProcessedFields = Schema::hasColumn('orders', 'processed_by') && Schema::hasColumn('orders', 'processed_at');
                $hasShippedFields = Schema::hasColumn('orders', 'shipped_by') && Schema::hasColumn('orders', 'shipped_at');
                $hasDeliveredFields = Schema::hasColumn('orders', 'delivered_by') && Schema::hasColumn('orders', 'delivered_at');
                $hasCancelledFields = Schema::hasColumn('orders', 'cancelled_by') && Schema::hasColumn('orders', 'cancelled_at');
                
                // สร้างใบสั่งขายสำหรับบริษัท
                for ($i = 0; $i < $orderCount; $i++) {
                    $customer = $customers->random();
                    $creator = $users->random();
                    
                    // สุ่มวันที่สั่งซื้อในช่วง 3 เดือนที่ผ่านมา
                    $orderDate = Carbon::now()->subDays(rand(0, 90));
                    $year = $orderDate->format('Y');
                    $month = $orderDate->format('m');
                    
                    // สร้างเลขที่ใบสั่งขายตามรูปแบบใหม่: SO{YYYY}{MM}{NNNN}
                    $orderNumber = sprintf("SO%s%s%04d", $year, $month, $i + 1);
                    
                    // กำหนดสถานะ
                    $status = $faker->randomElement(['draft', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled']);
                    
                    // คำนวณราคา
                    $subtotal = $faker->randomFloat(2, 1000, 50000);
                    $discountType = $faker->randomElement(['fixed', 'percentage']);
                    $discountAmount = $discountType === 'fixed' 
                        ? $faker->randomFloat(2, 0, $subtotal * 0.1) 
                        : $faker->randomFloat(2, 0, 10);
                    $discountValue = $discountType === 'fixed' 
                        ? $discountAmount 
                        : $subtotal * ($discountAmount / 100);
                        
                    $taxRate = 7;
                    $taxAmount = ($subtotal - $discountValue) * ($taxRate / 100);
                    $shippingCost = $faker->randomFloat(2, 0, 500);
                    $totalAmount = $subtotal - $discountValue + $taxAmount + $shippingCost;
                    
                    // อ้างอิงใบเสนอราคา (ถ้ามี)
                    $quotationId = null;
                    $quotations = Quotation::where('company_id', $company->id)
                        ->where('status', 'approved')
                        ->get();
                        
                    if ($quotations->count() > 0 && rand(0, 1) == 1) {
                        $quotationId = $quotations->random()->id;
                    }
                    
                    try {
                        // สร้างใบสั่งขาย
                        DB::beginTransaction();
                        
                        $order = new Order([
                            'company_id' => $company->id,
                            'customer_id' => $customer->id,
                            'quotation_id' => $quotationId,
                            'order_number' => $orderNumber,
                            'order_date' => $orderDate->format('Y-m-d'),
                            'delivery_date' => rand(0, 1) ? $orderDate->addDays(rand(1, 30))->format('Y-m-d') : null,
                            'status' => $status,
                            'subtotal' => $subtotal,
                            'discount_type' => $discountType,
                            'discount_amount' => $discountValue,
                            'tax_rate' => $taxRate,
                            'tax_amount' => $taxAmount,
                            'shipping_cost' => $shippingCost,
                            'total_amount' => $totalAmount,
                            'notes' => rand(0, 1) ? $faker->sentence() : null,
                            'payment_terms' => rand(0, 1) ? $faker->randomElement(['เงินสด', 'เครดิต 30 วัน', 'เครดิต 60 วัน']) : null,
                            'shipping_address' => $customer->address,
                            'shipping_method' => rand(0, 1) ? $faker->randomElement(['ขนส่งบริษัท', 'ไปรษณีย์ไทย', 'Kerry', 'Flash', 'J&T']) : null,
                            'created_by' => $creator->id,
                        ]);
                        
                        $order->save();
                        
                        // กำหนดข้อมูลตามสถานะโดยใช้ DB::raw เพื่อหลีกเลี่ยงปัญหาคอลัมน์ไม่มี
                        $updateData = [];
                        
                        // กำหนดข้อมูลตามสถานะ
                        if (in_array($status, ['confirmed', 'processing', 'shipped', 'delivered']) && $hasConfirmedFields) {
                            $updateData['confirmed_by'] = $users->random()->id;
                            $updateData['confirmed_at'] = $orderDate->addHours(rand(1, 24))->format('Y-m-d H:i:s');
                        }
                        
                        if (in_array($status, ['processing', 'shipped', 'delivered']) && $hasProcessedFields) {
                            $updateData['processed_by'] = $users->random()->id;
                            $updateData['processed_at'] = $orderDate->addHours(rand(24, 48))->format('Y-m-d H:i:s');
                        }
                        
                        if (in_array($status, ['shipped', 'delivered']) && $hasShippedFields) {
                            $updateData['shipped_by'] = $users->random()->id;
                            $updateData['shipped_at'] = $orderDate->addDays(rand(1, 3))->format('Y-m-d H:i:s');
                            if (Schema::hasColumn('orders', 'tracking_number')) {
                                $updateData['tracking_number'] = strtoupper($faker->bothify('??#####??'));
                            }
                        }
                        
                        if ($status === 'delivered' && $hasDeliveredFields) {
                            $updateData['delivered_by'] = $users->random()->id;
                            $updateData['delivered_at'] = $orderDate->addDays(rand(4, 7))->format('Y-m-d H:i:s');
                        }
                        
                        if ($status === 'cancelled' && $hasCancelledFields) {
                            $updateData['cancelled_by'] = $users->random()->id;
                            $updateData['cancelled_at'] = $orderDate->addHours(rand(1, 72))->format('Y-m-d H:i:s');
                            if (Schema::hasColumn('orders', 'cancellation_reason')) {
                                $updateData['cancellation_reason'] = $faker->sentence();
                            }
                        }
                        
                        // อัพเดทฐานข้อมูลเฉพาะถ้ามีค่าที่ต้องอัพเดท
                        if (!empty($updateData)) {
                            $updateData['updated_at'] = now()->format('Y-m-d H:i:s');
                            DB::table('orders')->where('id', $order->id)->update($updateData);
                        }
                        
                        // สร้างรายการสินค้าใน order
                        $orderItemCount = rand(1, 5);
                        $orderProducts = $products->random($orderItemCount);
                        
                        $itemTotalAmount = 0;
                        
                        foreach ($orderProducts as $product) {
                            $quantity = rand(1, 10);
                            $unitPrice = $product->price * (rand(90, 110) / 100); // ราคาที่อาจจะมีส่วนลดหรือเพิ่มขึ้น
                            $itemTotal = $quantity * $unitPrice;
                            $itemTotalAmount += $itemTotal;
                            
                            OrderItem::create([
                                'order_id' => $order->id,
                                'product_id' => $product->id,
                                'description' => $product->name,
                                'quantity' => $quantity,
                                'unit_price' => $unitPrice,
                                'price' => $unitPrice, // ใช้ค่าเดียวกันกับ unit_price
                                'unit_id' => $product->unit_id,
                                'total' => $itemTotal,
                            ]);
                        }
                        
                        DB::commit();
                        echo "  สร้างใบสั่งขาย {$orderNumber} สำเร็จ\n";
                    } catch (\Exception $e) {
                        DB::rollBack();
                        echo "  เกิดข้อผิดพลาดในการสร้างใบสั่งขาย {$orderNumber}: " . $e->getMessage() . "\n";
                        Log::error('เกิดข้อผิดพลาดในการสร้างใบสั่งขาย: ' . $e->getMessage(), [
                            'order_number' => $orderNumber,
                            'file' => $e->getFile(),
                            'line' => $e->getLine()
                        ]);
                    }
                }
            }
            
            echo "สร้างใบสั่งขายเสร็จสิ้น\n";
        } catch (\Exception $e) {
            echo "เกิดข้อผิดพลาดในขั้นตอนการเตรียมข้อมูล: " . $e->getMessage() . "\n";
            Log::error('เกิดข้อผิดพลาดในขั้นตอนการเตรียมข้อมูล: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }
}
