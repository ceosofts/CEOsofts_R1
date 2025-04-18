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
            // เพิ่มคอลัมน์ created_by ถ้ายังไม่มี
            if (!Schema::hasColumn('orders', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable();
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            }
            
            // เพิ่มคอลัมน์ confirmed_by ถ้ายังไม่มี
            if (!Schema::hasColumn('orders', 'confirmed_by')) {
                $table->unsignedBigInteger('confirmed_by')->nullable();
                $table->foreign('confirmed_by')->references('id')->on('users')->nullOnDelete();
            }
            
            // เพิ่มคอลัมน์ confirmed_at ถ้ายังไม่มี
            if (!Schema::hasColumn('orders', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable();
            }
            
            // เพิ่มคอลัมน์ processed_by ถ้ายังไม่มี
            if (!Schema::hasColumn('orders', 'processed_by')) {
                $table->unsignedBigInteger('processed_by')->nullable();
                $table->foreign('processed_by')->references('id')->on('users')->nullOnDelete();
            }
            
            // เพิ่มคอลัมน์ processed_at ถ้ายังไม่มี
            if (!Schema::hasColumn('orders', 'processed_at')) {
                $table->timestamp('processed_at')->nullable();
            }
            
            // เพิ่มคอลัมน์ shipped_by ถ้ายังไม่มี
            if (!Schema::hasColumn('orders', 'shipped_by')) {
                $table->unsignedBigInteger('shipped_by')->nullable();
                $table->foreign('shipped_by')->references('id')->on('users')->nullOnDelete();
            }
            
            // เพิ่มคอลัมน์ shipped_at ถ้ายังไม่มี
            if (!Schema::hasColumn('orders', 'shipped_at')) {
                $table->timestamp('shipped_at')->nullable();
            }
            
            // เพิ่มคอลัมน์ delivered_by ถ้ายังไม่มี
            if (!Schema::hasColumn('orders', 'delivered_by')) {
                $table->unsignedBigInteger('delivered_by')->nullable();
                $table->foreign('delivered_by')->references('id')->on('users')->nullOnDelete();
            }
            
            // เพิ่มคอลัมน์ delivered_at ถ้ายังไม่มี
            if (!Schema::hasColumn('orders', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable();
            }
            
            // เพิ่มคอลัมน์ cancelled_by ถ้ายังไม่มี
            if (!Schema::hasColumn('orders', 'cancelled_by')) {
                $table->unsignedBigInteger('cancelled_by')->nullable();
                $table->foreign('cancelled_by')->references('id')->on('users')->nullOnDelete();
            }
            
            // เพิ่มคอลัมน์ cancelled_at ถ้ายังไม่มี
            if (!Schema::hasColumn('orders', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable();
            }
            
            // เพิ่มคอลัมน์ cancellation_reason ถ้ายังไม่มี
            if (!Schema::hasColumn('orders', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = [
                'created_by', 'confirmed_by', 'confirmed_at', 
                'processed_by', 'processed_at', 'shipped_by', 
                'shipped_at', 'delivered_by', 'delivered_at',
                'cancelled_by', 'cancelled_at', 'cancellation_reason'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    if (in_array($column, ['created_by', 'confirmed_by', 'processed_by', 'shipped_by', 'delivered_by', 'cancelled_by'])) {
                        $table->dropForeign(['created_by']);
                    }
                    $table->dropColumn($column);
                }
            }
        });
    }
};
