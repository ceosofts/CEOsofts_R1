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
        if (!Schema::hasTable('generated_documents')) {
            Schema::create('generated_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->foreignId('template_id')->nullable()->constrained('document_templates')->onDelete('set null');
                $table->string('document_type', 50); // quotation, invoice, receipt, etc.
                $table->string('filename');
                $table->string('file_path'); // เปลี่ยนจาก storage_path เพื่อหลีกเลี่ยงชื่อที่ตรงกับ function PHP
                $table->string('mime_type')->default('application/pdf');
                $table->integer('file_size')->nullable();
                $table->boolean('is_sent')->default(false);
                $table->timestamp('sent_at')->nullable();
                $table->string('sent_to')->nullable();
                $table->string('sent_by')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                // Indexes
                $table->index('company_id');
                $table->index('document_type');
                $table->index('is_sent');
            });
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_documents');
    }
};
