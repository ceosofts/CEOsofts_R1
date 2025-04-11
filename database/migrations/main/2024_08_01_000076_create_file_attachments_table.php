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
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                $table->index(['attachable_type', 'attachable_id']);
            });
            
            echo "สร้างตาราง file_attachments เรียบร้อยแล้ว\n";
        } else {
            echo "ตาราง file_attachments มีอยู่แล้ว\n";
            
            // อาจเพิ่มคำสั่งเพื่อเพิ่มหรือแก้ไขคอลัมน์ที่จำเป็น
            $this->ensureRequiredColumns();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ไม่ลบตารางในกรณีที่เป็น migration อัปเดต
        // Schema::dropIfExists('file_attachments');
    }
    
    /**
     * ตรวจสอบและเพิ่มคอลัมน์ที่จำเป็น
     */
    private function ensureRequiredColumns()
    {
        $columns = Schema::getColumnListing('file_attachments');
        
        if (!in_array('deleted_at', $columns)) {
            Schema::table('file_attachments', function (Blueprint $table) {
                $table->softDeletes();
            });
            echo "เพิ่มคอลัมน์ deleted_at แล้ว\n";
        }
        
        if (!in_array('metadata', $columns)) {
            Schema::table('file_attachments', function (Blueprint $table) {
                $table->json('metadata')->nullable()->after('created_by');
            });
            echo "เพิ่มคอลัมน์ metadata แล้ว\n";
        }
    }
};
