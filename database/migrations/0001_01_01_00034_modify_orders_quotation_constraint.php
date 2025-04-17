<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ป้องกันการทำงานซ้ำหรือเกิด error
        try {
            // ตรวจสอบว่ามีตาราง orders หรือไม่
            if (Schema::hasTable('orders')) {
                // ตรวจสอบว่ามีคอลัมน์ quotation_id หรือไม่
                if (Schema::hasColumn('orders', 'quotation_id')) {
                    Schema::table('orders', function (Blueprint $table) {
                        // ถ้ามี foreign key อยู่แล้ว ให้ลบออกก่อน
                        $foreignKeys = $this->listTableForeignKeys('orders');
                        foreach ($foreignKeys as $key) {
                            if (str_contains($key, 'quotation_id')) {
                                $table->dropForeign($key);
                                Log::info('ลบ foreign key เดิม: ' . $key);
                            }
                        }
                        
                        // เพิ่ม foreign key ใหม่ที่มี onDelete('set null')
                        $table->foreign('quotation_id')
                              ->references('id')
                              ->on('quotations')
                              ->nullOnDelete();
                        
                        Log::info('เพิ่ม foreign key ใหม่: orders.quotation_id -> quotations.id (nullOnDelete)');
                    });
                } else {
                    Schema::table('orders', function (Blueprint $table) {
                        // เพิ่มคอลัมน์ quotation_id ถ้ายังไม่มี
                        $table->foreignId('quotation_id')->nullable()->after('customer_id');
                        
                        // เพิ่ม foreign key
                        $table->foreign('quotation_id')
                              ->references('id')
                              ->on('quotations')
                              ->nullOnDelete();
                        
                        Log::info('เพิ่มคอลัมน์และ foreign key: orders.quotation_id -> quotations.id (nullOnDelete)');
                    });
                }
            }
        } catch (\Exception $e) {
            Log::error('ไม่สามารถแก้ไข constraint ของตาราง orders: ' . $e->getMessage());
            // ไม่ throw exception เพื่อให้การ migrate ทำงานต่อได้
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ป้องกันการทำงานซ้ำหรือเกิด error
        try {
            // ตรวจสอบว่ามีตาราง orders หรือไม่
            if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'quotation_id')) {
                Schema::table('orders', function (Blueprint $table) {
                    // ถ้ามี foreign key อยู่แล้ว ให้ลบออกก่อน
                    $foreignKeys = $this->listTableForeignKeys('orders');
                    foreach ($foreignKeys as $key) {
                        if (str_contains($key, 'quotation_id')) {
                            $table->dropForeign($key);
                        }
                    }
                    
                    // เพิ่ม foreign key ใหม่แบบมาตรฐาน (onDelete cascade)
                    $table->foreign('quotation_id')
                          ->references('id')
                          ->on('quotations')
                          ->cascadeOnDelete();
                });
            }
        } catch (\Exception $e) {
            Log::error('ไม่สามารถย้อนกลับ constraint ของตาราง orders: ' . $e->getMessage());
            // ไม่ throw exception เพื่อให้การ rollback ทำงานต่อได้
        }
    }

    /**
     * Get the foreign keys for the given table.
     *
     * @param string $table
     * @return array
     */
    protected function listTableForeignKeys($table)
    {
        $conn = Schema::getConnection()->getDoctrineSchemaManager();
        
        $foreignKeys = [];
        try {
            $tableDetails = $conn->listTableDetails($table);
            foreach ($tableDetails->getForeignKeys() as $key) {
                $foreignKeys[] = $key->getName();
            }
        } catch (\Exception $e) {
            // หากมีข้อผิดพลาดในการดึง foreign key ให้กลับ array ว่าง
        }
        
        return $foreignKeys;
    }
};
