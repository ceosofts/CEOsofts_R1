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
        if (!Schema::hasTable('document_sendings')) {
            Schema::create('document_sendings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->foreignId('document_id')->constrained('generated_documents')->onDelete('cascade');
                $table->string('document_type');
                $table->timestamp('sent_at')->nullable();
                $table->unsignedBigInteger('sent_by')->nullable();
                $table->foreign('sent_by')->references('id')->on('users')->nullOnDelete();
                $table->string('sent_to');
                $table->string('sent_cc')->nullable();
                $table->string('sent_bcc')->nullable();
                $table->string('subject');
                $table->text('body')->nullable();
                $table->string('status')->default('sent');
                $table->string('result')->default('sent');
                $table->string('tracking_id')->nullable();
                $table->text('error_message')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Indexes
                $table->index(['company_id', 'document_type']);
                $table->index('sent_at');
                $table->index('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_sendings');
    }
};
