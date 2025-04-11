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
        // สร้างตารางใหม่
        if (!Schema::hasTable('table_name')) {
            Schema::create('table_name', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained();
                // คอลัมน์อื่นๆ...
                $table->timestamps();
                $table->softDeletes();
                
                // Indexes
                $table->index('company_id');
            });
        }
        
        // หรือแก้ไขตารางที่มีอยู่
        // if (Schema::hasTable('table_name')) {
        //     Schema::table('table_name', function (Blueprint $table) {
        //         if (!Schema::hasColumn('table_name', 'new_column')) {
        //             $table->string('new_column')->after('existing_column');
        //         }
        //     });
        // }
        
        // SQL Statement โดยตรง (ถ้าจำเป็น)
        // try {
        //     DB::statement('UPDATE table_name SET column = value WHERE condition');
        // } catch (\Exception $e) {
        //     Log::warning('ไม่สามารถอัปเดตข้อมูลได้: ' . $e->getMessage());
        // }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ลบตาราง (ใช้สำหรับตารางใหม่)
        Schema::dropIfExists('table_name');
        
        // หรือลบคอลัมน์ (ใช้สำหรับตารางที่แก้ไข)
        // if (Schema::hasTable('table_name')) {
        //     Schema::table('table_name', function (Blueprint $table) {
        //         if (Schema::hasColumn('table_name', 'new_column')) {
        //             $table->dropColumn('new_column');
        //         }
        //     });
        // }
    }
};
