<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migration.
     * สร้างตารางภาษี (taxes)
     */
    public function up(): void
    {
        // สร้างตารางภาษี (taxes)
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('name', 100);
            $table->string('code', 20);
            $table->decimal('rate', 5, 2);
            $table->string('type', 20)->default('percentage'); // percentage, fixed
            $table->boolean('is_compound')->default(false);
            $table->string('apply_to', 50)->default('all');
            $table->boolean('is_recoverable')->default(true);
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('company_id');
            $table->index('name');
            $table->index('code');
            $table->index('rate');
            $table->index('is_active');

            // Unique constraints
            $table->unique(['company_id', 'code']);
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};
