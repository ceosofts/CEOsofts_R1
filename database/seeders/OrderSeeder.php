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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            echo "เริ่มสร้างข้อมูลใบสั่งขาย...\n";
            
            // ตรวจสอบข้อมูลที่จำเป็น
            $company = Company::first();
            if (!$company) {
                echo "ไม่พบข้อมูลบริษัท กรุณาสร้างข้อมูลบริษัทก่อนสร้างใบสั่งขาย\n";
                return;
            }
            
            $customers = Customer::where('company_id', $company->id)->get();
            if ($customers->isEmpty()) {
                echo "ไม่พบข้อมูลลูกค้า กรุณาสร้างข้อมูลลูกค้าก่อนสร้างใบสั่งขาย\n";
                return;
            }
            
            $quotations = Quotation::where('company_id', $company->id)
                ->whereIn('status', ['approved', 'sent'])
                ->get();
            
            if ($quotations->isEmpty()) {
                echo "ไม่พบข้อมูลใบเสนอราคาที่อนุมัติแล้ว กรุณาสร้างข้อมูลใบเสนอราคาก่อนสร้างใบสั่งขาย\n";
                return;
            }
            
            // แก้ไขส่วนนี้: ปรับปรุงการค้นหาผู้ใช้งาน
            // ทางเลือกที่ 1: ค้นหาผู้ใช้งานที่เชื่อมโยงกับบริษัท
            $users = User::whereHas('companies', function ($query) use ($company) {
                $query->where('companies.id', $company->id);
            })->get();
            
            // ทางเลือกที่ 2: ถ้าไม่พบผู้ใช้งานที่เชื่อมโยงกับบริษัท ให้หาผู้ใช้งานที่มีบทบาท admin หรือ super_admin
            if ($users->isEmpty()) {
                $users = User::whereHas('roles', function ($query) {
                    $query->whereIn('name', ['admin', 'super_admin']);
                })->get();
            }
            
            // ทางเลือกที่ 3: ถ้ายังไม่พบ ให้หาผู้ใช้งานทั้งหมด
            if ($users->isEmpty()) {
                $users = User::all();
            }
            
            if ($users->isEmpty()) {
                echo "ไม่พบข้อมูลผู้ใช้งาน กรุณาสร้างข้อมูลผู้ใช้งานก่อนสร้างใบสั่งขาย\n";
                return;
            }
            
            // แสดงข้อมูลที่พบเพื่อการตรวจสอบ
            echo "พบผู้ใช้งานทั้งหมด: " . $users->count() . " คน\n";
            
            // สร้างใบสั่งขาย
            foreach ($quotations as $index => $quotation) {
                // สร้างใบสั่งขายเฉพาะ 5 รายการแรก
                if ($index >= 5) break;
                
                $customer = $customers->random();
                $user = $users->random();
                
                $orderDate = Carbon::now()->subDays(rand(1, 30));
                $deliveryDate = (clone $orderDate)->addDays(rand(5, 15));
                
                $order = new Order([
                    'company_id' => $company->id,
                    'customer_id' => $customer->id,
                    'quotation_id' => $quotation->id,
                    'order_number' => 'SO' . date('Ymd') . rand(100, 999),
                    'order_date' => $orderDate,
                    'delivery_date' => $deliveryDate,
                    'status' => 'pending', // 'draft', 'pending', 'approved', 'processing', 'shipped', 'completed', 'canceled'
                    'customer_po_number' => 'PO' . date('Ymd') . rand(100, 999),
                    'created_by' => $user->id,
                    'notes' => $quotation->notes,
                    'payment_terms' => 'ชำระเงินทันที',
                    'shipping_address' => $customer->address,
                    'subtotal' => $quotation->subtotal,
                    'discount_amount' => $quotation->discount_amount,
                    'tax_amount' => $quotation->tax_amount,
                    'total_amount' => $quotation->total_amount,
                ]);
                
                DB::beginTransaction();
                try {
                    $order->save();
                    
                    // สร้างรายการสินค้าในใบสั่งขาย
                    foreach ($quotation->items as $item) {
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $item->product_id,
                            'description' => $item->description,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'discount_percentage' => $item->discount_percentage,
                            'discount_amount' => $item->discount_amount,
                            'tax_percentage' => $item->tax_percentage,
                            'tax_amount' => $item->tax_amount,
                            'subtotal' => $item->subtotal,
                            'total' => $item->total,
                        ]);
                    }
                    
                    DB::commit();
                    echo "  สร้างใบสั่งขาย: {$order->order_number} สำเร็จ\n";
                } catch (\Exception $e) {
                    DB::rollBack();
                    echo "  เกิดข้อผิดพลาดในการสร้างใบสั่งขาย: " . $e->getMessage() . "\n";
                }
            }
            
            echo "สร้างข้อมูลใบสั่งขายเสร็จสิ้น\n";
        } catch (\Exception $e) {
            echo "เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
            echo "ที่ไฟล์: " . $e->getFile() . " บรรทัด: " . $e->getLine() . "\n";
        }
    }
}
