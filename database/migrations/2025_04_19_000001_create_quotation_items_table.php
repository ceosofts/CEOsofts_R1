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
        if (!Schema::hasTable('quotation_items')) {
            Schema::create('quotation_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('quotation_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
                $table->string('description');
                $table->decimal('quantity', 15, 2)->default(0);
                $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
                $table->decimal('unit_price', 15, 2)->default(0);
                $table->decimal('discount_percentage', 5, 2)->default(0);
                $table->decimal('discount_amount', 15, 2)->default(0);
                $table->decimal('tax_percentage', 5, 2)->default(0);
                $table->decimal('tax_amount', 15, 2)->default(0);
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('total', 15, 2)->default(0);
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
            Log::info('สร้างตาราง quotation_items เรียบร้อยแล้ว');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
    }
};
