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
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->string('key');
                $table->longText('value')->nullable();
                $table->string('type')->default('string'); // string, integer, float, boolean, json
                $table->string('group')->default('general');
                $table->boolean('editable')->default(true);
                $table->string('description')->nullable();
                $table->json('options')->nullable(); // For dropdown or other option-based settings
                $table->timestamps();
                $table->softDeletes();
                
                // Ensure key is unique per company
                $table->unique(['company_id', 'key']);
                
                // Add indexes
                $table->index(['company_id', 'group']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
