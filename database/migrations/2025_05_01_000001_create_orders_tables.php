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
        // ตรวจสอบว่ามีตาราง orders หรือไม่
        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id');
                $table->unsignedBigInteger('customer_id');
                $table->unsignedBigInteger('quotation_id')->nullable();
                $table->string('order_number')->unique(); // เลขที่ใบสั่งขายของเรา (SO)
                $table->string('customer_po_number')->nullable(); // เพิ่มฟิลด์เก็บเลขที่ใบสั่งซื้อของลูกค้า (PO)
                $table->date('order_date');
                $table->date('delivery_date')->nullable();
                $table->enum('status', ['draft', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])->default('draft');
                $table->text('notes')->nullable();
                $table->string('payment_terms')->nullable();
                $table->text('shipping_address')->nullable();
                $table->string('shipping_method')->nullable();
                $table->decimal('shipping_cost', 15, 2)->default(0);
                $table->string('tracking_number')->nullable();
                $table->text('shipping_notes')->nullable();
                $table->decimal('subtotal', 15, 2);
                $table->enum('discount_type', ['fixed', 'percentage'])->nullable();
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('tax_rate', 5, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('total_amount', 15, 2);
                $table->unsignedBigInteger('created_by');
                $table->unsignedBigInteger('confirmed_by')->nullable();
                $table->timestamp('confirmed_at')->nullable();
                $table->unsignedBigInteger('processed_by')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->unsignedBigInteger('shipped_by')->nullable();
                $table->timestamp('shipped_at')->nullable();
                $table->unsignedBigInteger('delivered_by')->nullable();
                $table->timestamp('delivered_at')->nullable();
                $table->unsignedBigInteger('cancelled_by')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->text('cancellation_reason')->nullable();
                $table->timestamps();
                $table->softDeletes(); // เพิ่ม deleted_at column
                
                $table->foreign('company_id')->references('id')->on('companies');
                $table->foreign('customer_id')->references('id')->on('customers');
                $table->foreign('quotation_id')->references('id')->on('quotations')->nullOnDelete();
                $table->foreign('created_by')->references('id')->on('users');
                $table->foreign('confirmed_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('processed_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('shipped_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('delivered_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('cancelled_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        // ตรวจสอบว่ามีตาราง order_items หรือไม่
        if (!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('product_id');
                $table->string('description');
                $table->decimal('quantity', 15, 2);
                $table->decimal('unit_price', 15, 2);
                $table->unsignedBigInteger('unit_id')->nullable();
                $table->decimal('total', 15, 2);
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes(); // เพิ่ม deleted_at column ในตาราง order_items
                
                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
                $table->foreign('unit_id')->references('id')->on('units')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
