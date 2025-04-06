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
        // สร้างตารางแปลภาษา (translations) เฉพาะเมื่อยังไม่มีตารางนี้
        if (!Schema::hasTable('translations')) {
            Schema::create('translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->string('translatable_type');
                $table->unsignedBigInteger('translatable_id');
                $table->string('locale', 10);
                $table->string('field', 50);
                $table->text('value');
                $table->timestamps();
                
                // Indexes
                $table->index('company_id');
                $table->index(['translatable_type', 'translatable_id']);
                $table->index('locale');
                $table->index('field');
                
                // Unique constraints - กำหนดชื่อ constraint เองเพื่อให้สั้นลง
                $table->unique(
                    ['translatable_type', 'translatable_id', 'locale', 'field'], 
                    'translations_unique_fields'
                );
            });
        } else {
            // ถ้าตารางมีอยู่แล้วให้ตรวจสอบและเพิ่ม indexes หรือ constraints ที่อาจจะยังขาดอยู่
            try {
                Schema::table('translations', function (Blueprint $table) {
                    if (!Schema::hasIndex('translations', 'translations_unique_fields')) {
                        $table->unique(
                            ['translatable_type', 'translatable_id', 'locale', 'field'], 
                            'translations_unique_fields'
                        );
                    }
                });
            } catch (\Exception $e) {
                \Log::warning("ไม่สามารถเพิ่ม unique constraint ในตาราง translations: " . $e->getMessage());
            }
        }

        // สร้างตารางไฟล์แนบ (file_attachments)
        if (!Schema::hasTable('file_attachments')) {
            Schema::create('file_attachments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->string('attachable_type');
                $table->unsignedBigInteger('attachable_id');
                $table->string('path');
                $table->string('filename');
                $table->string('original_filename');
                $table->string('mime_type', 100);
                $table->integer('size');
                $table->string('disk', 50)->default('local');
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
                
                // Indexes
                $table->index('company_id');
                $table->index(['attachable_type', 'attachable_id'], 'file_attachments_attachable_index');
                $table->index('created_by');
                $table->index('mime_type');
            });
        } else {
            // ตรวจสอบและเพิ่ม indexes สำหรับตาราง file_attachments
            try {
                Schema::table('file_attachments', function (Blueprint $table) {
                    if (!Schema::hasIndex('file_attachments', 'file_attachments_attachable_index')) {
                        $table->index(['attachable_type', 'attachable_id'], 'file_attachments_attachable_index');
                    }
                });
            } catch (\Exception $e) {
                \Log::warning("ไม่สามารถเพิ่ม index ในตาราง file_attachments: " . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        // มีจัดการ index เพิ่มเติมในกรณีที่มีการเพิ่ม index ในตารางที่มีอยู่แล้ว
        try {
            if (Schema::hasTable('translations') && Schema::hasIndex('translations', 'translations_unique_fields')) {
                Schema::table('translations', function (Blueprint $table) {
                    $table->dropUnique('translations_unique_fields');
                });
            }
            
            if (Schema::hasTable('file_attachments') && Schema::hasIndex('file_attachments', 'file_attachments_attachable_index')) {
                Schema::table('file_attachments', function (Blueprint $table) {
                    $table->dropIndex('file_attachments_attachable_index');
                });
            }
        } catch (\Exception $e) {
            \Log::warning("เกิดข้อผิดพลาดในการลบ index ในขณะ rollback: " . $e->getMessage());
        }
        
        // ลบตารางตามปกติ
        Schema::dropIfExists('file_attachments');
        Schema::dropIfExists('translations');
    }
};
