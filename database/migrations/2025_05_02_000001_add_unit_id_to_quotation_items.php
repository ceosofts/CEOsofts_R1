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
        if (Schema::hasTable('quotation_items')) {
            if (!Schema::hasColumn('quotation_items', 'unit_id')) {
                Schema::table('quotation_items', function (Blueprint $table) {
                    $table->foreignId('unit_id')->nullable()->after('quantity');
                    $table->foreign('unit_id')->references('id')->on('units')->nullOnDelete();

                    Log::info('เพิ่มคอลัมน์ unit_id ในตาราง quotation_items เรียบร้อยแล้ว');
                });
            }
        } else {
            Log::warning('ไม่พบตาราง quotation_items จึงไม่สามารถเพิ่มคอลัมน์ unit_id ได้');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('quotation_items') && Schema::hasColumn('quotation_items', 'unit_id')) {
            Schema::table('quotation_items', function (Blueprint $table) {
                $table->dropForeign(['unit_id']);
                $table->dropColumn('unit_id');
            });
            Log::info('ลบคอลัมน์ unit_id จากตาราง quotation_items เรียบร้อยแล้ว');
        }
    }
};
