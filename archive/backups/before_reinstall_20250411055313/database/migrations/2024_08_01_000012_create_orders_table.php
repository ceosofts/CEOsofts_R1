<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('quotation_id')->nullable()->constrained()->onDelete('set null');
            $table->string('order_number')->unique();
            $table->date('order_date');
            $table->date('delivery_date')->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->string('status', 20)->default('pending');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('company_id');
            $table->index('customer_id');
            $table->index('quotation_id');
            $table->index('status');
            $table->index('order_date');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
