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
        if (!Schema::hasTable('translations')) {
            if (!Schema::hasTable('translations')) {
            Schema::create('translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->string('locale');
                $table->string('group');
                $table->string('key');
                $table->text('value')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Unique constraint - one translation per key per locale per group per company
                $table->unique(['company_id', 'locale', 'group', 'key'], 'translations_unique');
                
                // Indexes for faster lookups
                $table->index(['company_id', 'locale']);
                $table->index(['company_id', 'group']);
            });
        } else {
            echo "ตาราง translations มีอยู่แล้ว\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
