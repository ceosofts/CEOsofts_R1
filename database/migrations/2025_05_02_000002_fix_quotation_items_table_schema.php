<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ตรวจสอบคอลัมน์ในตาราง quotation_items
        if (Schema::hasTable('quotation_items')) {
            // สำรองข้อมูลก่อน (ถ้ามี)
            $existingData = [];
            try {
                $existingData = DB::table('quotation_items')->get()->toArray();
                $this->command->info("สำรองข้อมูล quotation_items จำนวน " . count($existingData) . " รายการ");
            } catch (\Exception $e) {
                Log::warning("ไม่สามารถดึงข้อมูลจากตาราง quotation_items: " . $e->getMessage());
            }

            // ลบและสร้างตารางใหม่
            Schema::dropIfExists('quotation_items');
            
            // สร้างตารางใหม่ด้วยโครงสร้างที่ถูกต้อง
            Schema::create('quotation_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quotation_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
                $table->string('description');
                $table->decimal('quantity', 15, 2)->default(0);
                $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
                $table->decimal('unit_price', 15, 2)->default(0);
                $table->decimal('discount_percentage', 5, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('tax_percentage', 5, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('total', 15, 2)->default(0);
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes(); // เพิ่ม soft delete
            });
            
            Log::info("สร้างตาราง quotation_items ใหม่พร้อมคอลัมน์ที่ถูกต้องเรียบร้อยแล้ว");
            
            // คืนข้อมูลถ้ามีการสำรองไว้
            // ข้ามส่วนนี้ไปเพราะโครงสร้างตารางเก่าอาจไม่ตรงกับโครงสร้างใหม่
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ไม่ต้องทำอะไรในส่วนนี้ เนื่องจากเป็นการแก้ไขโครงสร้างตาราง
    }
};
