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
        // สร้างตาราง receipt_items ถ้ายังไม่มี
        if (!Schema::hasTable('receipt_items')) {
            Schema::create('receipt_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('receipt_id')->constrained()->onDelete('cascade');
                $table->foreignId('invoice_id')->constrained()->onDelete('restrict');
                $table->decimal('amount', 15, 2);
                $table->text('description')->nullable();
                $table->integer('sort_order')->default(0);
                $table->json('metadata')->nullable();
                $table->timestamps();
                
                // Indexes
                $table->index('receipt_id');
                $table->index('invoice_id');
            });
        }
        
        // อัปเดตตาราง receipts หากต้องการเพิ่มฟิลด์เพิ่มเติม
        if (Schema::hasTable('receipts')) {
            Schema::table('receipts', function (Blueprint $table) {
                if (!Schema::hasColumn('receipts', 'last_generated_document_id')) {
                    $table->foreignId('last_generated_document_id')->nullable()->constrained('generated_documents')->onDelete('set null');
                }
                if (!Schema::hasColumn('receipts', 'last_pdf_generated_at')) {
                    $table->timestamp('last_pdf_generated_at')->nullable();
                }
                if (!Schema::hasColumn('receipts', 'needs_pdf_regeneration')) {
                    $table->boolean('needs_pdf_regeneration')->default(false);
                }
                if (!Schema::hasColumn('receipts', 'currency_code')) {
                    $table->string('currency_code', 3)->default('THB');
                }
                if (!Schema::hasColumn('receipts', 'exchange_rate')) {
                    $table->decimal('exchange_rate', 15, 5)->default(1);
                }
                if (!Schema::hasColumn('receipts', 'reference')) {
                    $table->string('reference', 100)->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_items');
        
        if (Schema::hasTable('receipts')) {
            Schema::table('receipts', function (Blueprint $table) {
                $columns = [
                    'last_generated_document_id', 'last_pdf_generated_at', 
                    'needs_pdf_regeneration', 'currency_code', 'exchange_rate',
                    'reference'
                ];
                
                foreach ($columns as $column) {
                    if (Schema::hasColumn('receipts', $column)) {
                        if ($column === 'last_generated_document_id') {
                            $table->dropForeign(['last_generated_document_id']);
                        }
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
