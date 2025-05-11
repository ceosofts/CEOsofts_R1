<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // เพิ่มคอลัมน์ has_invoice เป็น boolean ค่าเริ่มต้นเป็น false
            $table->boolean('has_invoice')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // ลบคอลัมน์ has_invoice เมื่อ rollback
            $table->dropColumn('has_invoice');
        });
    }
};
