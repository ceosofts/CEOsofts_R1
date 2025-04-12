<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     * รวมการทำงานจากไฟล์ 2024_08_01_000015_update_generated_documents_table.php แล้ว
     * รวมการทำงานจากไฟล์ 2024_08_01_000053_add_deleted_at_to_generated_documents.php
     */
    public function up(): void
    {
        // สำรองข้อมูลเดิม (ถ้ามี)
        $existingDocuments = [];
        if (Schema::hasTable('generated_documents')) {
            try {
                $existingDocuments = DB::table('generated_documents')->get()->toArray();
                Log::info('สำรองข้อมูล generated_documents จำนวน ' . count($existingDocuments) . ' รายการ');
                Schema::dropIfExists('generated_documents');
            } catch (\Exception $e) {
                Log::warning('ไม่สามารถสำรองข้อมูล generated_documents: ' . $e->getMessage());
                Schema::dropIfExists('generated_documents');
            }
        }

        Schema::create('generated_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('document_template_id')->constrained()->onDelete('cascade');
            $table->foreignId('template_id')->nullable(); // เพิ่มคอลัมน์ template_id
            $table->morphs('documentable'); // polymorphic relationship สำหรับเชื่อมกับ model ต่างๆ
            $table->string('filename');
            $table->string('display_name');
            $table->string('file_path');
            $table->string('file_type', 20); // pdf, docx, html, etc.
            $table->string('status', 20)->default('generated'); // generated, sent, viewed, etc.
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('metadata')->nullable(); // สำหรับข้อมูลเพิ่มเติม
            $table->timestamps();
            $table->softDeletes(); // เพิ่มจากไฟล์ add_deleted_at_to_generated_documents.php

            // Indexes
            $table->index('company_id');
            $table->index('document_template_id');
            $table->index('template_id'); // เพิ่ม index สำหรับ template_id
            $table->index('status');
            $table->index('created_by');
            $table->index('created_at');
            $table->index('deleted_at'); // เพิ่ม index สำหรับ soft delete
        });

        Log::info('สร้างตาราง generated_documents พร้อมรองรับ soft deletes เรียบร้อยแล้ว');

        // นำข้อมูลเดิมกลับคืน (ถ้ามี)
        if (!empty($existingDocuments)) {
            try {
                foreach ($existingDocuments as $document) {
                    $documentData = (array) $document;

                    // ลบ primary key เพื่อให้ auto-increment ทำงานได้ถูกต้อง
                    if (isset($documentData['id'])) {
                        unset($documentData['id']);
                    }

                    DB::table('generated_documents')->insert($documentData);
                }

                Log::info('นำข้อมูล generated_documents กลับคืนเรียบร้อยแล้ว');
            } catch (\Exception $e) {
                Log::error('ไม่สามารถนำข้อมูล generated_documents กลับคืน: ' . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_documents');
    }

    /**
     * ตรวจสอบว่า index มีอยู่แล้วหรือไม่
     * 
     * @param string $table ชื่อตาราง
     * @param string $index ชื่อ index
     * @return bool
     */
    private function hasIndex(string $table, string $index): bool
    {
        $driver = DB::connection()->getDriverName();

        if ($driver == 'sqlite') {
            // SQLite ไม่มีวิธีตรวจสอบ index โดยตรง ต้องตรวจสอบจาก schema
            $indexes = DB::select("PRAGMA index_list('$table')");
            foreach ($indexes as $idx) {
                if ($idx->name === $index) {
                    return true;
                }
            }
            return false;
        } else {
            // สำหรับ MySQL
            $query = DB::select("SHOW INDEX FROM $table WHERE Key_name = '$index'");
            return !empty($query);
        }
    }
};
