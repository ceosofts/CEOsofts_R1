<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('quotation_items') && !Schema::hasColumn('quotation_items', 'deleted_at')) {
            Schema::table('quotation_items', function (Blueprint $table) {
                $table->softDeletes();
            });
            echo "เพิ่มคอลัมน์ deleted_at ลงในตาราง quotation_items เรียบร้อยแล้ว\n";
        } else {
            echo "ตาราง quotation_items มีคอลัมน์ deleted_at อยู่แล้ว หรือไม่พบตาราง\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('quotation_items') && Schema::hasColumn('quotation_items', 'deleted_at')) {
            Schema::table('quotation_items', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
