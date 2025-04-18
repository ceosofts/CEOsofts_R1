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
            // เพิ่มคอลัมน์ payment_terms ถ้ายังไม่มี
            if (!Schema::hasColumn('orders', 'payment_terms')) {
                $table->string('payment_terms')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'payment_terms')) {
                $table->dropColumn('payment_terms');
            }
        });
    }
};
