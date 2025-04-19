<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateDeliveryOrderItemsTable extends Migration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        if (!Schema::hasTable('delivery_order_items')) {
            Schema::create('delivery_order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('delivery_order_id')->constrained()->onDelete('cascade');
                $table->foreignId('order_item_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
                $table->string('description');
                $table->decimal('quantity', 15, 2);
                $table->string('unit', 30)->nullable();
                $table->string('status', 30)->default('pending'); // pending, delivered, partial
                $table->text('notes')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                // Indexes
                $table->index('delivery_order_id');
                $table->index('order_item_id');
                $table->index('product_id');
                $table->index('status');
            });

            Log::info('สร้างตาราง delivery_order_items เรียบร้อยแล้ว');
        } else {
            Log::info('พบตาราง delivery_order_items อยู่แล้ว ข้ามการสร้างตาราง');
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_order_items');
    }
}

return new CreateDeliveryOrderItemsTable();
