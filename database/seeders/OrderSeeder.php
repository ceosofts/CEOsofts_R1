<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\User;
use App\Models\Employee;
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
     * 
     * @param int|null $specificCompanyId เฉพาะบริษัทที่ต้องการสร้างข้อมูล (ถ้าไม่ระบุจะสร้างให้ทุกบริษัท)
     */
    public function run(?int $specificCompanyId = null): void
    {
        // ข้อมูล Debug สำหรับเช็คการทำงาน
        echo "Starting OrderSeeder with specificCompanyId: " . ($specificCompanyId ?? 'null') . "\n";
        Log::info('เริ่มการทำงาน OrderSeeder', ['specificCompanyId' => $specificCompanyId]);
        
        // ถ้าระบุ specificCompanyId ให้สร้างเฉพาะบริษัทนั้น
        if ($specificCompanyId) {
            $company = Company::find($specificCompanyId);
            if ($company) {
                echo "Found company: " . $company->name . " (ID: " . $company->id . ")\n";
                Log::info('พบบริษัท', ['company_id' => $company->id, 'company_name' => $company->name]);
                $this->generateOrdersForCompany($company);
            } else {
                echo "ไม่พบบริษัทตาม ID ที่ระบุ: {$specificCompanyId}\n";
                Log::error("ไม่พบบริษัทตาม ID ที่ระบุ", ['company_id' => $specificCompanyId]);
                
                // สร้างบริษัทตัวอย่างถ้าไม่พบ
                echo "กำลังสร้างบริษัทตัวอย่าง...\n";
                $company = $this->createExampleCompany();
                if ($company) {
                    $this->generateOrdersForCompany($company);
                }
            }
            return;
        }
        
        // สำหรับแต่ละบริษัท (กรณีไม่ได้ระบุ specificCompanyId)
        $companies = Company::all();
        
        if ($companies->isEmpty()) {
            echo "ไม่พบข้อมูลบริษัทในระบบ กำลังสร้างบริษัทตัวอย่าง...\n";
            $company = $this->createExampleCompany();
            if ($company) {
                $companies = collect([$company]);
            }
        }
        
        $faker = \Faker\Factory::create('th_TH');

        try {
            echo "เริ่มสร้างใบสั่งขาย...\n";
            
            foreach ($companies as $company) {
                $this->generateOrdersForCompany($company);
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
    
    /**
     * สร้างบริษัทตัวอย่างถ้าไม่พบบริษัทในระบบ
     */
    private function createExampleCompany()
    {
        try {
            $faker = \Faker\Factory::create('th_TH');
            $company = Company::create([
                'name' => 'บริษัทตัวอย่าง ' . $faker->company,
                'tax_id' => $faker->numerify('###########'),
                'address' => $faker->address,
                'phone' => $faker->phoneNumber,
                'email' => $faker->companyEmail,
                'website' => $faker->domainName,
            ]);
            
            echo "สร้างบริษัทตัวอย่างสำเร็จ: " . $company->name . " (ID: " . $company->id . ")\n";
            Log::info('สร้างบริษัทตัวอย่างสำเร็จ', ['company_id' => $company->id, 'company_name' => $company->name]);
            
            return $company;
        } catch (\Exception $e) {
            echo "เกิดข้อผิดพลาดในการสร้างบริษัทตัวอย่าง: " . $e->getMessage() . "\n";
            Log::error('เกิดข้อผิดพลาดในการสร้างบริษัทตัวอย่าง', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return null;
        }
    }
    
    /**
     * สร้างใบสั่งขายสำหรับบริษัทที่ระบุ
     * 
     * @param Company $company
     * @return void
     */
    private function generateOrdersForCompany(Company $company): void
    {
        $faker = \Faker\Factory::create('th_TH');
        
        // สร้างใบสั่งขายจำนวน 5-10 รายการต่อบริษัท (จำนวนน้อยลงเพื่อให้สร้างเร็วขึ้น)
        $orderCount = rand(5, 10);
        echo "สร้างใบสั่งขายสำหรับบริษัท {$company->name} (ID: {$company->id}) จำนวน {$orderCount} รายการ\n";
                
        // ดึงลูกค้าของบริษัท
        $customers = Customer::where('company_id', $company->id)->get();
        if ($customers->isEmpty()) {
            echo "  ไม่พบลูกค้าสำหรับบริษัท {$company->name} กำลังสร้างลูกค้าตัวอย่าง...\n";
            // สร้างลูกค้าตัวอย่าง 3 ราย
            for ($i = 1; $i <= 3; $i++) {
                Customer::create([
                    'company_id' => $company->id,
                    'name' => $faker->company,
                    'contact_name' => $faker->name,
                    'email' => $faker->email,
                    'phone' => $faker->phoneNumber,
                    'address' => $faker->address,
                    'tax_id' => $faker->numerify('###########'),
                ]);
            }
            $customers = Customer::where('company_id', $company->id)->get();
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
            echo "  ไม่พบสินค้าสำหรับบริษัท {$company->name} กำลังสร้างสินค้าตัวอย่าง...\n";
            // สร้างสินค้าตัวอย่าง 5 รายการ
            for ($i = 1; $i <= 5; $i++) {
                Product::create([
                    'company_id' => $company->id,
                    'name' => 'สินค้าตัวอย่าง ' . $i,
                    'description' => 'คำอธิบายสินค้าตัวอย่าง ' . $i,
                    'price' => rand(100, 5000),
                    'cost' => rand(50, 3000),
                    'stock_quantity' => rand(10, 100),
                ]);
            }
            $products = Product::where('company_id', $company->id)->get();
        }
                
        // ดึงพนักงานขายของบริษัท
        $salesPersons = Employee::where('company_id', $company->id)->get();
                
        if ($salesPersons->isEmpty()) {
            echo "  ไม่พบพนักงานสำหรับบริษัท {$company->name} กำลังสร้างพนักงานตัวอย่าง...\n";
            // สร้างพนักงานตัวอย่าง 2 คน
            for ($i = 1; $i <= 2; $i++) {
                Employee::create([
                    'company_id' => $company->id,
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'email' => $faker->email,
                    'phone' => $faker->phoneNumber,
                    'employee_code' => 'EMP-' . $i,
                ]);
            }
            $salesPersons = Employee::where('company_id', $company->id)->get();
        }
        
        // ตรวจสอบว่ามีคอลัมน์ที่จำเป็นในฐานข้อมูลหรือไม่
        $hasTaxRate = Schema::hasColumn('orders', 'tax_rate');
        $hasShippingCost = Schema::hasColumn('orders', 'shipping_cost');
        $hasShippingMethod = Schema::hasColumn('orders', 'shipping_method'); // เพิ่มการตรวจสอบ shipping_method
        
        // สร้างใบสั่งขายสำหรับบริษัท
        for ($i = 0; $i < $orderCount; $i++) {
            $customer = $customers->random();
            $creator = $users->random();
                    
            // กำหนดพนักงานขาย (ถ้ามี)
            $salesPersonId = null;
            if ($salesPersons->isNotEmpty()) {
                $salesPerson = $salesPersons->random();
                $salesPersonId = $salesPerson->id;
            }
                    
            // สุ่มวันที่สั่งซื้อในช่วง 3 เดือนที่ผ่านมา
            $orderDate = Carbon::now()->subDays(rand(0, 90));
            $shortYear = $orderDate->format('y');
            $month = $orderDate->format('m');
            $companyCode = str_pad($company->id, 2, '0', STR_PAD_LEFT);
                    
            // สร้างเลขที่ใบสั่งขายตามรูปแบบใหม่: SO{CC}{YY}{MM}{NNNN}
            $orderNumber = "SO{$companyCode}{$shortYear}{$month}" . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            
            // ตรวจสอบว่าเลขที่ซ้ำหรือไม่
            while (Order::withTrashed()->where('order_number', $orderNumber)->exists()) {
                $i++;
                $orderNumber = "SO{$companyCode}{$shortYear}{$month}" . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            }
                    
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
                
                // เตรียมข้อมูลสำหรับสร้างใบสั่งขาย
                $orderData = [
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
                    'tax_amount' => $taxAmount,
                    'total_amount' => $totalAmount,
                    'notes' => rand(0, 1) ? $faker->sentence() : null,
                    'payment_terms' => rand(0, 1) ? $faker->randomElement(['เงินสด', 'เครดิต 30 วัน', 'เครดิต 60 วัน']) : null,
                    'shipping_address' => $customer->address,
                    'created_by' => $creator->id,
                    'sales_person_id' => $salesPersonId,
                ];
                
                // เพิ่ม shipping_method เฉพาะเมื่อคอลัมน์มีอยู่ในฐานข้อมูล
                if ($hasShippingMethod) {
                    $orderData['shipping_method'] = rand(0, 1) ? $faker->randomElement(['ขนส่งบริษัท', 'ไปรษณีย์ไทย', 'Kerry', 'Flash', 'J&T']) : null;
                }
                
                // เพิ่ม tax_rate เฉพาะเมื่อคอลัมน์มีอยู่ในฐานข้อมูล
                if ($hasTaxRate) {
                    $orderData['tax_rate'] = $taxRate;
                }
                
                // เพิ่ม shipping_cost เฉพาะเมื่อคอลัมน์มีอยู่ในฐานข้อมูล
                if ($hasShippingCost) {
                    $orderData['shipping_cost'] = $shippingCost;
                }
                
                $order = new \App\Models\Order($orderData);
                $order->save();
                        
                // สร้างรายการสินค้าใน order
                $orderItemCount = rand(1, 5);
                $orderProducts = $products->random(min($orderItemCount, $products->count()));
                        
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
                        'unit_id' => $product->unit_id ?? null,
                        'total' => $itemTotal,
                    ]);
                }
                        
                DB::commit();
                echo "สร้างใบสั่งขายสำเร็จทั้งหมด {$orderCount} รายการ\n";
                Log::info('สร้างใบสั่งขายสำเร็จ', ['company_id' => $company->id, 'count' => $orderCount]);
            } catch (\Exception $e) {
                if (DB::transactionLevel() > 0) {
                    DB::rollBack();
                }
                echo "เกิดข้อผิดพลาดในการสร้างใบสั่งขายสำหรับบริษัท {$company->name}: " . $e->getMessage() . "\n";
                Log::error('เกิดข้อผิดพลาดในการสร้างใบสั่งขาย', [
                    'company_id' => $company->id,
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(), 
                    'line' => $e->getLine()
                ]);
            }
        }
    }
}
