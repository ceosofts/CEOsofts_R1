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
        if (Schema::hasTable('generated_documents')) {
            Schema::table('generated_documents', function (Blueprint $table) {
                // ตรวจสอบและเพิ่มคอลัมน์ที่อาจยังขาดไป
                if (!Schema::hasColumn('generated_documents', 'mime_type')) {
                    $table->string('mime_type')->default('application/pdf')->after('path');
                }
                if (!Schema::hasColumn('generated_documents', 'file_size')) {
                    $table->integer('file_size')->nullable()->after('mime_type');
                }
                if (!Schema::hasColumn('generated_documents', 'is_sent')) {
                    $table->boolean('is_sent')->default(false)->after('is_signed');
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
                
                // Indexes สำหรับคอลัมน์ใหม่
                $table->index('is_sent');
            });
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        if (Schema::hasTable('generated_documents')) {
            Schema::table('generated_documents', function (Blueprint $table) {
                // Drop indexes
                $table->dropIndex(['is_sent']);
                
                // Drop columns
                $columns = [
                    'mime_type',
                    'file_size',
                    'is_sent',
                    'sent_at',
                    'sent_to',
                    'sent_by',
                ];
                
                // ลบเฉพาะคอลัมน์ที่มีอยู่
                foreach ($columns as $column) {
                    if (Schema::hasColumn('generated_documents', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
