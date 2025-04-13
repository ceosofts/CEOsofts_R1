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
     * รวมการทำงานจากไฟล์ 0001_01_01_00039_create_file_attachments_table.php
     */
    public function up(): void
    {
        // สำรองข้อมูลเดิม (ถ้ามี)
        $existingAttachments = [];
        if (Schema::hasTable('file_attachments')) {
            try {
                $existingAttachments = DB::table('file_attachments')->get()->toArray();
                Log::info('สำรองข้อมูล file_attachments จำนวน ' . count($existingAttachments) . ' รายการ');
                Schema::dropIfExists('file_attachments');
            } catch (\Exception $e) {
                Log::warning('ไม่สามารถสำรองข้อมูล file_attachments: ' . $e->getMessage());
                Schema::dropIfExists('file_attachments');
            }
        }

        // สร้างตาราง file_attachments ใหม่ ด้วยโครงสร้างสมบูรณ์
        Schema::create('file_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->morphs('attachable'); // เพื่อสร้าง attachable_type, attachable_id และ index โดยอัตโนมัติ
            $table->string('filename');
            $table->string('original_filename');
            $table->string('disk')->default('local');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // สร้าง indices
            // ไม่ต้องสร้าง index ซ้ำ เพราะ morphs() สร้างให้แล้ว
            // $table->index(['attachable_type', 'attachable_id']); <-- ลบบรรทัดนี้
            $table->index('company_id');
            $table->index('filename');
            $table->index('created_by');
            $table->index('created_at');

            // สร้าง foreign key
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Log::info('สร้างตาราง file_attachments เรียบร้อยแล้ว');

        // นำข้อมูลเดิมกลับคืน (ถ้ามี) พร้อมปรับคอลัมน์ตามโครงสร้างใหม่
        if (!empty($existingAttachments)) {
            try {
                foreach ($existingAttachments as $attachment) {
                    $attachmentData = (array) $attachment;

                    // ปรับชื่อคอลัมน์ถ้าจำเป็น
                    if (isset($attachmentData['name'])) {
                        $attachmentData['filename'] = $attachmentData['name'];
                        unset($attachmentData['name']);
                    }
                    if (isset($attachmentData['original_name'])) {
                        $attachmentData['original_filename'] = $attachmentData['original_name'];
                        unset($attachmentData['original_name']);
                    }
                    if (isset($attachmentData['path'])) {
                        $attachmentData['file_path'] = $attachmentData['path'];
                        unset($attachmentData['path']);
                    }
                    if (isset($attachmentData['size'])) {
                        $attachmentData['file_size'] = $attachmentData['size'];
                        unset($attachmentData['size']);
                    }

                    // ตรวจสอบว่าคอลัมน์จำเป็นมีครบไหม
                    if (!isset($attachmentData['attachable_type']) && isset($attachmentData['attachable_id'])) {
                        $attachmentData['attachable_type'] = 'App\\Domain\\FileStorage\\Models\\FileAttachment';
                    }

                    // ลบ primary key เพื่อให้ auto-increment ทำงานได้ถูกต้อง
                    if (isset($attachmentData['id'])) {
                        unset($attachmentData['id']);
                    }

                    DB::table('file_attachments')->insert($attachmentData);
                }

                Log::info('นำข้อมูล file_attachments กลับคืนเรียบร้อยแล้ว');
            } catch (\Exception $e) {
                Log::error('ไม่สามารถนำข้อมูล file_attachments กลับคืน: ' . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_attachments');
    }
};
