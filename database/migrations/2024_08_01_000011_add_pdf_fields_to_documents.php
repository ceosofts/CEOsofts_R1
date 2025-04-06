<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * เพิ่มฟิลด์สำหรับการจัดการ PDF ในตารางเอกสารต่างๆ
     */
    public function up(): void
    {
        // ตารางที่ต้องเพิ่มฟิลด์สำหรับ PDF
        $tables = [
            'quotations',
            'orders', 
            'invoices', 
            'receipts',
            'delivery_notes'
        ];
        
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->foreignId('last_generated_document_id')->nullable()->constrained('generated_documents')->nullOnDelete();
                $table->timestamp('last_pdf_generated_at')->nullable();
                $table->boolean('needs_pdf_regeneration')->default(false);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'quotations',
            'orders', 
            'invoices', 
            'receipts',
            'delivery_notes'
        ];
        
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['last_generated_document_id']);
                $table->dropColumn('last_generated_document_id');
                $table->dropColumn('last_pdf_generated_at');
                $table->dropColumn('needs_pdf_regeneration');
            });
        }
    }
};
