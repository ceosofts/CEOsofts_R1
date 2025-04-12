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
            });
        }

        // สร้างตาราง file_attachments ถ้ายังไม่มี
        if (!Schema::hasTable('file_attachments')) {
            Schema::create('file_attachments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->string('attachable_type')->nullable();
                $table->unsignedBigInteger('attachable_id')->nullable();
                $table->string('name');
                $table->string('original_name');
                $table->string('disk')->default('local');
                $table->string('path');
                $table->string('mime_type')->nullable();
                $table->unsignedBigInteger('size')->default(0);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // สร้าง indices
                $table->index(['attachable_type', 'attachable_id']);
                $table->index('company_id');

                // สร้าง foreign key
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ลบตาราง translations ถ้ามีอยู่
        Schema::dropIfExists('translations');

        // ลบตาราง file_attachments ถ้ามีอยู่
        Schema::dropIfExists('file_attachments');
    }
};
