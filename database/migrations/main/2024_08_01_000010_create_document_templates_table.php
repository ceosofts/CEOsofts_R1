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
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('name');
            $table->string('type', 50); // invoice, receipt, quotation, order, etc.
            $table->json('layout');
            $table->json('header')->nullable();
            $table->json('footer')->nullable();
            $table->text('css')->nullable();
            $table->string('orientation', 10)->default('portrait');
            $table->string('paper_size', 10)->default('a4');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('company_id');
            $table->index('type');
            $table->index('is_default');
            $table->index('is_active');
            $table->index('created_by');
            $table->unique(['company_id', 'name', 'type']);
        });

        Schema::create('generated_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('document_type', 50);
            $table->unsignedBigInteger('document_id');
            $table->foreignId('template_id')->nullable()->constrained('document_templates')->nullOnDelete();
            $table->string('filename');
            $table->string('disk', 50)->default('local');
            $table->string('path');
            $table->boolean('is_signed')->default(false);
            $table->json('signature_data')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('company_id');
            $table->index('document_type');
            $table->index(['document_type', 'document_id']);
            $table->index('template_id');
            $table->index('created_by');
        });
        
        Schema::create('document_sendings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('generated_document_id')->constrained('generated_documents')->onDelete('cascade');
            $table->string('recipient_email');
            $table->string('recipient_name');
            $table->string('subject');
            $table->text('message')->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->text('error')->nullable();
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('company_id');
            $table->index('generated_document_id');
            $table->index('recipient_email');
            $table->index('status');
            $table->index('sent_at');
            $table->index('sent_by');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_sendings');
        Schema::dropIfExists('generated_documents');
        Schema::dropIfExists('document_templates');
    }
};
