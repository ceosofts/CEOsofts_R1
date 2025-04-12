<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * รวมการทำงานและการปรับปรุงจากไฟล์ 0001_01_01_00039_create_file_attachments_table.php
     * และจากไฟล์ต่อไปนี้:
     * - 2024_08_02_000003_add_deleted_at_to_file_attachments_table.php
     * - 2024_08_02_000005_add_required_columns_to_file_attachments_table.php
     * - 2024_08_02_000006_recreate_file_attachments_table.php
     */
    public function up(): void
    {
        // ตรวจสอบว่ามีตาราง file_attachments อยู่แล้วหรือไม่
        $hasTable = Schema::hasTable('file_attachments');
        $attachmentsData = [];

        // ถ้ามีตารางอยู่แล้ว ให้สำรองข้อมูลก่อนสร้างใหม่
        if ($hasTable) {
            try {
                Log::info('สำรองข้อมูล file_attachments ก่อนสร้างตารางใหม่');
                $attachmentsData = DB::table('file_attachments')->get()->toArray();
                Schema::dropIfExists('file_attachments');
                Log::info('สำรองข้อมูล file_attachments จำนวน ' . count($attachmentsData) . ' รายการเรียบร้อย');
            } catch (\Exception $e) {
                Log::warning('ไม่สามารถสำรองข้อมูล file_attachments: ' . $e->getMessage());
            }
        }

        Schema::create('file_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->morphs('attachable'); // สร้าง attachable_type และ attachable_id
            $table->string('filename'); // ชื่อไฟล์ที่จัดเก็บ
            $table->string('original_filename'); // ชื่อไฟล์ดั้งเดิม
            $table->string('disk')->default('public'); // disk ที่ใช้จัดเก็บ (local, s3, ฯลฯ)
            $table->string('file_path'); // path ภายใน disk
            $table->string('mime_type'); // ประเภทของไฟล์
            $table->unsignedBigInteger('file_size'); // ขนาดไฟล์ในหน่วย bytes
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('metadata')->nullable(); // ข้อมูลเพิ่มเติมในรูปแบบ JSON
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('company_id');
            $table->index('filename');
            $table->index('mime_type');
            $table->index('created_by');
        });

        // นำข้อมูลเดิมกลับมาใส่ในตารางใหม่ (ถ้ามี)
        if (!empty($attachmentsData)) {
            try {
                foreach ($attachmentsData as $attachment) {
                    // แปลง stdClass object เป็น array
                    $attachment = (array) $attachment;

                    // ตรวจสอบคอลัมน์ที่จำเป็น และกำหนดค่าเริ่มต้นถ้าไม่มี
                    if (!isset($attachment['mime_type'])) {
                        $attachment['mime_type'] = $this->guessMimeTypeFromFilename($attachment['filename'] ?? '');
                    }

                    if (!isset($attachment['file_size'])) {
                        $attachment['file_size'] = 0;
                    }

                    if (!isset($attachment['original_filename']) && isset($attachment['filename'])) {
                        $attachment['original_filename'] = $attachment['filename'];
                    }

                    if (!isset($attachment['file_path']) && isset($attachment['path'])) {
                        $attachment['file_path'] = $attachment['path'];
                        unset($attachment['path']);
                    }

                    // ตรวจสอบว่ามี id อยู่หรือไม่ ถ้ามี ให้ลบออกเพื่อให้สร้างใหม่
                    if (isset($attachment['id'])) {
                        unset($attachment['id']);
                    }

                    DB::table('file_attachments')->insert($attachment);
                }

                Log::info('คืนข้อมูลเข้าตาราง file_attachments จำนวน ' . count($attachmentsData) . ' รายการ');
            } catch (\Exception $e) {
                Log::error('ไม่สามารถคืนข้อมูล file_attachments: ' . $e->getMessage());
            }
        }

        Log::info('สร้างตาราง file_attachments เรียบร้อยแล้ว');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_attachments');
    }

    /**
     * ทายประเภท MIME จากชื่อไฟล์
     *
     * @param string $filename
     * @return string
     */
    private function guessMimeTypeFromFilename(string $filename): string
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'txt' => 'text/plain',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
        ];

        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }
};
