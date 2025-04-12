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
        // สร้างตาราง translations ถ้ายังไม่มี
        if (!Schema::hasTable('translations')) {
            Schema::create('translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->string('locale');
                $table->string('group');
                $table->string('key');
                $table->string('field')->default('general');
                $table->text('value')->nullable();
                $table->string('translatable_type')->default('general');
                $table->unsignedBigInteger('translatable_id')->default(0);
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // สร้าง indices
                $table->index('company_id');
                $table->index(['translatable_type', 'translatable_id']);
                $table->index('locale');
                $table->index('field');

                // สร้าง unique constraint
                $table->unique(['company_id', 'locale', 'group', 'key'], 'translations_company_locale_group_key_unique');

                // เพิ่ม index เพิ่มเติม
                $table->index(['company_id', 'locale'], 'translations_company_locale_index');
                $table->index(['company_id', 'group'], 'translations_company_group_index');
            });
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
