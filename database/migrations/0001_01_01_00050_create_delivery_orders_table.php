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
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('delivery_number', 20)->unique(); // เพิ่มความยาวเป็น 20 เพื่อให้รองรับรูปแบบใหม่
            $table->date('delivery_date');
            $table->date('expected_delivery_date')->nullable();
            $table->enum('delivery_status', ['pending', 'processing', 'shipped', 'delivered', 'partial_delivered', 'cancelled'])->default('pending');
            $table->text('shipping_address');
            $table->string('shipping_method');
            $table->string('tracking_number')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_orders');
    }
};
