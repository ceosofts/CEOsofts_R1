<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class QuotationItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ตรวจสอบคอลัมน์ในตาราง quotation_items
        $hasUnitIdColumn = Schema::hasColumn('quotation_items', 'unit_id');
        if (!$hasUnitIdColumn) {
            $this->command->warn('ตาราง quotation_items ไม่มีคอลัมน์ unit_id จะทำการสร้างข้อมูลโดยไม่ใช้คอลัมน์นี้');
        }

        // ตรวจสอบว่ามี products หรือไม่
        $products = Product::all();
        if ($products->isEmpty()) {
            $this->command->error("ไม่พบข้อมูลสินค้า กรุณา seed ข้อมูลสินค้าก่อน");
            return;
        }
        
        // ดึงข้อมูลใบเสนอราคาทั้งหมด
        $quotations = Quotation::all();
        if ($quotations->isEmpty()) {
            $this->command->error("ไม่พบข้อมูลใบเสนอราคา กรุณา seed ข้อมูลใบเสนอราคาก่อน");
            return;
        }
        
        $this->command->info("เริ่มสร้างรายการสินค้าในใบเสนอราคา...");
        
        // ลบรายการเดิมถ้าต้องการ (อาจเอาออกได้ถ้าไม่ต้องการล้างข้อมูลเดิม)
        // QuotationItem::truncate();
        
        // วนลูปสร้างรายการสินค้าให้แต่ละใบเสนอราคา
        foreach ($quotations as $quotation) {
            // สุ่มจำนวนรายการ 1-5 รายการ
            $itemCount = rand(1, 5);
            $productsForThisQuotation = $products->random(min($itemCount, $products->count()));
            
            $this->command->info("  สร้างรายการสินค้าสำหรับใบเสนอราคา: {$quotation->quotation_number} จำนวน {$productsForThisQuotation->count()} รายการ");
            
            // คำนวณค่าพื้นฐาน
            $taxRate = $quotation->tax_rate;
            
            // สร้างรายการสินค้า
            foreach ($productsForThisQuotation as $index => $product) {
                try {
                    // สุ่มจำนวน 1-10 ชิ้น
                    $quantity = rand(1, 10);
                    
                    // คำนวณราคาต่อหน่วย
                    $unitPrice = $product->price > 0 ? $product->price : rand(100, 5000);
                    
                    // คำนวณส่วนลด (0-10%)
                    $discountPercent = rand(0, 10);
                    $itemSubtotal = $quantity * $unitPrice;
                    $discountAmount = ($discountPercent / 100) * $itemSubtotal;
                    
                    // คำนวณภาษี
                    $itemTaxAmount = (($itemSubtotal - $discountAmount) * $taxRate) / 100;
                    $itemTotal = ($itemSubtotal - $discountAmount) + $itemTaxAmount;
                    
                    // สร้างข้อมูลพื้นฐาน
                    $itemData = [
                        'quotation_id' => $quotation->id,
                        'product_id' => $product->id,
                        'description' => $product->name,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'discount_percentage' => $discountPercent,
                        'discount_amount' => $discountAmount,
                        'tax_percentage' => $taxRate,
                        'tax_amount' => $itemTaxAmount,
                        'subtotal' => $itemSubtotal,
                        'total' => $itemTotal
                    ];
                    
                    // เพิ่ม unit_id ถ้ามีคอลัมน์นี้
                    if ($hasUnitIdColumn && $product->unit_id) {
                        $itemData['unit_id'] = $product->unit_id;
                    }
                    
                    // สร้างรายการ
                    QuotationItem::create($itemData);
                    
                } catch (\Exception $e) {
                    $this->command->error("  เกิดข้อผิดพลาดในการสร้างรายการสินค้าสำหรับ ID: {$product->id}: " . $e->getMessage());
                    Log::error("QuotationItemSeeder - Error: " . $e->getMessage());
                }
            }
        }
        
        $this->command->info("สร้างรายการสินค้าในใบเสนอราคาเสร็จสิ้น");
    }
}
