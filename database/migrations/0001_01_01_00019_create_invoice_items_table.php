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
        // Check if invoice_items table exists before creating it
        if (!Schema::hasTable('invoice_items')) {
            Schema::create('invoice_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('invoice_id');
                $table->unsignedBigInteger('product_id')->nullable();
                $table->string('description');
                $table->decimal('quantity', 15, 2)->default(1);
                $table->decimal('unit_price', 15, 2)->default(0);
                $table->decimal('discount_percentage', 5, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('tax_percentage', 5, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('total', 15, 2)->default(0);
                $table->unsignedBigInteger('unit_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                // Add foreign key constraints after table creation
                if (Schema::hasTable('invoices')) {
                    $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
                }
                
                if (Schema::hasTable('products')) {
                    $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
                }
                
                if (Schema::hasTable('units')) {
                    $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
