<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;


return new class extends Migration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        // สร้างตารางการส่งเอกสาร (document_sendings) เฉพาะเมื่อยังไม่มี
        if (!Schema::hasTable('document_sendings')) {
            Schema::create('document_sendings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->foreignId('generated_document_id')->constrained()->onDelete('cascade');
                $table->string('recipient_email');
                $table->string('recipient_name');
                $table->string('subject');
                $table->text('message')->nullable();
                $table->string('status', 20)->default('pending'); // pending, sent, delivered, failed
                $table->timestamp('sent_at')->nullable();
                $table->text('error')->nullable(); // กรณีส่งไม่สำเร็จ
                $table->foreignId('sent_by')->nullable()->constrained('users')->onDelete('set null');
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
        } else {
            // ถ้าตารางมีอยู่แล้ว ตรวจสอบว่ามี indexes ที่จำเป็นหรือไม่
            try {
                Schema::table('document_sendings', function (Blueprint $table) {
                    // เพิ่ม indexes ที่อาจจะยังไม่มี
                    $indexes = [
                        'document_sendings_company_id_index' => 'company_id',
                        'document_sendings_generated_document_id_index' => 'generated_document_id',
                        'document_sendings_recipient_email_index' => 'recipient_email',
                        'document_sendings_status_index' => 'status',
                        'document_sendings_sent_at_index' => 'sent_at',
                        'document_sendings_sent_by_index' => 'sent_by',
                    ];
                    
                    foreach ($indexes as $indexName => $columnName) {
                        if (!Schema::hasIndex('document_sendings', $indexName)) {
                            $table->index($columnName);
                        }
                    }
                });
            } catch (\Exception $e) {
                \Log::warning("ไม่สามารถเพิ่ม indexes ในตาราง document_sendings: " . $e->getMessage());
            }
        }
        
        // อัปเดตตาราง generated_documents เพื่อเพิ่มฟิลด์ที่จำเป็นสำหรับการส่ง
        if (Schema::hasTable('generated_documents')) {
            Schema::table('generated_documents', function (Blueprint $table) {
                // เพิ่มคอลัมน์เฉพาะเมื่อยังไม่มี
                if (!Schema::hasColumn('generated_documents', 'is_sent')) {
                    $table->boolean('is_sent')->default(false)->after('signature_data');
                }
                if (!Schema::hasColumn('generated_documents', 'sent_at')) {
                    $table->timestamp('sent_at')->nullable()->after('is_sent');
                }
                if (!Schema::hasColumn('generated_documents', 'sent_to')) {
                    $table->string('sent_to')->nullable()->after('sent_at');
                }
                if (!Schema::hasColumn('generated_documents', 'sent_by')) {
                    $table->string('sent_by')->nullable()->after('sent_to');
                }
                
                // เพิ่ม index สำหรับคอลัมน์ที่ควรมี
                if (!Schema::hasIndex('generated_documents', 'generated_documents_is_sent_index') && 
                    Schema::hasColumn('generated_documents', 'is_sent')) {
                    $table->index('is_sent');
                }
            });
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        // ลบตารางเฉพาะเมื่อมีตารางนั้นอยู่
        if (Schema::hasTable('document_sendings')) {
            Schema::dropIfExists('document_sendings');
        }
        
        // ลบคอลัมน์ที่เพิ่มในตาราง generated_documents
        if (Schema::hasTable('generated_documents')) {
            Schema::table('generated_documents', function (Blueprint $table) {
                $columns = ['is_sent', 'sent_at', 'sent_to', 'sent_by'];
                
                foreach ($columns as $column) {
                    if (Schema::hasColumn('generated_documents', $column)) {
                        if ($column === 'is_sent' && Schema::hasIndex('generated_documents', 'generated_documents_is_sent_index')) {
                            $table->dropIndex('generated_documents_is_sent_index');
                        }
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
