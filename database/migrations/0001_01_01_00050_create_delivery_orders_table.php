<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateDeliveryOrdersTable extends Migration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        if (!Schema::hasTable('delivery_orders')) {
            Schema::create('delivery_orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('customer_id')->constrained()->onDelete('restrict');
                $table->string('delivery_number')->unique();
                $table->date('delivery_date');
                $table->string('delivery_status', 30)->default('pending'); // pending, delivered, partial_delivered, cancelled
                $table->text('shipping_address');
                $table->string('shipping_contact');
                $table->string('shipping_method')->nullable();
                $table->string('tracking_number')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('approved_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Indexes
                $table->index('company_id');
                $table->index('order_id');
                $table->index('customer_id');
                $table->index('delivery_number');
                $table->index('delivery_date');
                $table->index('delivery_status');
                $table->index('created_by');
                $table->index('approved_by');
            });

            Log::info('สร้างตาราง delivery_orders เรียบร้อยแล้ว');
        } else {
            Log::info('พบตาราง delivery_orders อยู่แล้ว ข้ามการสร้างตาราง');
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_orders');
    }
}

return new CreateDeliveryOrdersTable();
