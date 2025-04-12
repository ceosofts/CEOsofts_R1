<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migration.
     * รวมการทำงานจากไฟล์ 2024_08_01_000011_add_pdf_fields_to_documents.php แล้ว
     */
    public function up(): void
    {
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('name');
            $table->string('type', 50); // invoice, receipt, quotation, order, etc.
            $table->json('layout');
            $table->json('header')->nullable();
            $table->json('footer')->nullable();
            $table->text('css')->nullable();
            $table->string('orientation', 10)->default('portrait');
            $table->string('paper_size', 10)->default('a4');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->json('metadata')->nullable();

            // PDF Fields จากไฟล์ add_pdf_fields_to_documents.php
            $table->json('pdf_options')->nullable(); // แบบตั้งค่าพิเศษสำหรับ PDF เช่น margins, watermark, เป็นต้น
            $table->string('template_engine', 50)->default('blade'); // ระบุ template engine ที่ใช้ (blade, twig, etc.)
            $table->boolean('enable_signature')->default(false); // เปิดใช้งานการลงลายมือชื่อในเอกสารหรือไม่
            $table->json('signature_fields')->nullable(); // ตำแหน่งและรูปแบบของลายมือชื่อ

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('company_id');
            $table->index('type');
            $table->index('is_default');
            $table->index('is_active');
            $table->index('created_by');
            $table->unique(['company_id', 'name', 'type']);
        });

        Schema::create('generated_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('document_type', 50);
            $table->unsignedBigInteger('document_id');
            $table->foreignId('template_id')->nullable()->constrained('document_templates')->nullOnDelete();
            $table->string('filename');
            $table->string('disk', 50)->default('local');
            $table->string('path');
            $table->boolean('is_signed')->default(false);
            $table->json('signature_data')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->json('metadata')->nullable();

            // PDF Fields จากไฟล์ add_pdf_fields_to_documents.php
            $table->string('file_hash', 64)->nullable(); // บันทึก hash ของไฟล์เพื่อตรวจสอบความถูกต้อง
            $table->integer('file_size')->nullable(); // ขนาดไฟล์ในหน่วย bytes
            $table->string('mime_type', 100)->nullable(); // ประเภทของไฟล์ เช่น application/pdf
            $table->timestamp('expires_at')->nullable(); // วันหมดอายุของลิงก์ดาวน์โหลด (ถ้ามี)
            $table->boolean('is_protected')->default(false); // มีการป้องกันการเข้าถึงหรือไม่
            $table->string('password_hash', 100)->nullable(); // hash ของรหัสผ่านที่ใช้ป้องกันเอกสาร (ถ้ามี)

            $table->timestamps();

            // Indexes
            $table->index('company_id');
            $table->index('document_type');
            $table->index(['document_type', 'document_id']);
            $table->index('template_id');
            $table->index('created_by');
            $table->index('file_hash'); // เพิ่ม index สำหรับการค้นหาตาม hash
        });

        Schema::create('document_sendings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('generated_document_id')->constrained('generated_documents')->onDelete('cascade');
            $table->string('recipient_email');
            $table->string('recipient_name');
            $table->string('subject');
            $table->text('message')->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->text('error')->nullable();
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();

            // PDF Fields จากไฟล์ add_pdf_fields_to_documents.php
            $table->string('access_token', 100)->nullable(); // Token สำหรับเข้าถึงเอกสารโดยไม่ต้อง login
            $table->timestamp('opened_at')->nullable(); // เวลาที่ผู้รับเปิดอ่านเอกสาร
            $table->string('ip_address')->nullable(); // IP ของผู้เปิดเอกสาร
            $table->string('user_agent')->nullable(); // Browser/OS ของผู้เปิดเอกสาร
            $table->integer('view_count')->default(0); // จำนวนครั้งที่เปิดดู

            $table->timestamps();

            // Indexes
            $table->index('company_id');
            $table->index('generated_document_id');
            $table->index('recipient_email');
            $table->index('status');
            $table->index('sent_at');
            $table->index('sent_by');
            $table->index('access_token'); // เพิ่ม index สำหรับการค้นหาตาม access token
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_sendings');
        Schema::dropIfExists('generated_documents');
        Schema::dropIfExists('document_templates');
    }
};
