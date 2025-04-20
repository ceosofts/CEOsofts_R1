<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            Schema::table('orders', function (Blueprint $table) {
                // เพิ่มคอลัมน์สำหรับสถานะยืนยัน
                if (!Schema::hasColumn('orders', 'confirmed_by')) {
                    $table->unsignedBigInteger('confirmed_by')->nullable()->after('created_by');
                    Log::info('เพิ่มคอลัมน์ confirmed_by ลงในตาราง orders เรียบร้อยแล้ว');
                }
                if (!Schema::hasColumn('orders', 'confirmed_at')) {
                    $table->timestamp('confirmed_at')->nullable()->after('confirmed_by');
                    Log::info('เพิ่มคอลัมน์ confirmed_at ลงในตาราง orders เรียบร้อยแล้ว');
                }
                
                // เพิ่มคอลัมน์สำหรับสถานะดำเนินการ
                if (!Schema::hasColumn('orders', 'processed_by')) {
                    $table->unsignedBigInteger('processed_by')->nullable()->after('confirmed_at');
                    Log::info('เพิ่มคอลัมน์ processed_by ลงในตาราง orders เรียบร้อยแล้ว');
                }
                if (!Schema::hasColumn('orders', 'processed_at')) {
                    $table->timestamp('processed_at')->nullable()->after('processed_by');
                    Log::info('เพิ่มคอลัมน์ processed_at ลงในตาราง orders เรียบร้อยแล้ว');
                }
                
                // เพิ่มคอลัมน์อื่นๆ ที่อาจจำเป็นต้องมี แต่ไม่ได้กำหนดไว้
                if (!Schema::hasColumn('orders', 'shipped_by')) {
                    $table->unsignedBigInteger('shipped_by')->nullable()->after('processed_at');
                    Log::info('เพิ่มคอลัมน์ shipped_by ลงในตาราง orders เรียบร้อยแล้ว');
                }
                if (!Schema::hasColumn('orders', 'shipped_at')) {
                    $table->timestamp('shipped_at')->nullable()->after('shipped_by');
                    Log::info('เพิ่มคอลัมน์ shipped_at ลงในตาราง orders เรียบร้อยแล้ว');
                }
                
                if (!Schema::hasColumn('orders', 'delivered_by')) {
                    $table->unsignedBigInteger('delivered_by')->nullable()->after('shipped_at');
                    Log::info('เพิ่มคอลัมน์ delivered_by ลงในตาราง orders เรียบร้อยแล้ว');
                }
                if (!Schema::hasColumn('orders', 'delivered_at')) {
                    $table->timestamp('delivered_at')->nullable()->after('delivered_by');
                    Log::info('เพิ่มคอลัมน์ delivered_at ลงในตาราง orders เรียบร้อยแล้ว');
                }
                
                if (!Schema::hasColumn('orders', 'cancelled_by')) {
                    $table->unsignedBigInteger('cancelled_by')->nullable()->after('delivered_at');
                    Log::info('เพิ่มคอลัมน์ cancelled_by ลงในตาราง orders เรียบร้อยแล้ว');
                }
                if (!Schema::hasColumn('orders', 'cancelled_at')) {
                    $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
                    Log::info('เพิ่มคอลัมน์ cancelled_at ลงในตาราง orders เรียบร้อยแล้ว');
                }
                
                if (!Schema::hasColumn('orders', 'cancellation_reason')) {
                    $table->text('cancellation_reason')->nullable()->after('cancelled_at');
                    Log::info('เพิ่มคอลัมน์ cancellation_reason ลงในตาราง orders เรียบร้อยแล้ว');
                }
            });
        } catch (\Exception $e) {
            Log::error('ไม่สามารถเพิ่มคอลัมน์สถานะในตาราง orders: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = [
                'confirmed_by', 'confirmed_at', 
                'processed_by', 'processed_at', 
                'shipped_by', 'shipped_at', 
                'delivered_by', 'delivered_at', 
                'cancelled_by', 'cancelled_at', 
                'cancellation_reason'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
