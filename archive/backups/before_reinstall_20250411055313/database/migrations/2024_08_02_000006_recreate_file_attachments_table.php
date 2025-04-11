<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // สำรองข้อมูลก่อน
        $this->backupFileAttachments();
        
        // ลบและสร้างตาราง file_attachments ใหม่
        Schema::dropIfExists('file_attachments');
        
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
        
        echo "สร้างตาราง file_attachments ใหม่เรียบร้อยแล้ว\n";
        
        // คืนข้อมูลกลับ
        $this->restoreFileAttachments();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ไม่จำเป็นเพราะเราสร้างตารางใหม่
    }
    
    /**
     * สำรองข้อมูลจากตาราง file_attachments
     */
    private function backupFileAttachments()
    {
        if (Schema::hasTable('file_attachments')) {
            // ลบตาราง backup เก่าถ้ามี
            Schema::dropIfExists('file_attachments_backup');
            
            // สร้างตาราง backup
            Schema::create('file_attachments_backup', function (Blueprint $table) {
                $table->id();
                $table->text('data');
                $table->timestamp('created_at')->useCurrent();
            });
            
            // บันทึกข้อมูลทั้งหมดลงในตาราง backup
            try {
                $attachments = DB::table('file_attachments')->get();
                
                foreach ($attachments as $attachment) {
                    DB::table('file_attachments_backup')->insert([
                        'data' => json_encode((array)$attachment)
                    ]);
                }
                
                echo "สำรองข้อมูล file_attachments จำนวน " . count($attachments) . " รายการเรียบร้อย\n";
            } catch (\Exception $e) {
                echo "เกิดข้อผิดพลาดในการสำรองข้อมูล: " . $e->getMessage() . "\n";
            }
        } else {
            echo "ไม่มีตาราง file_attachments อยู่แล้ว ไม่จำเป็นต้องสำรองข้อมูล\n";
        }
    }
    
    /**
     * คืนข้อมูลกลับไปยังตาราง file_attachments
     */
    private function restoreFileAttachments()
    {
        if (Schema::hasTable('file_attachments_backup')) {
            try {
                $backups = DB::table('file_attachments_backup')->get();
                $restored = 0;
                
                foreach ($backups as $backup) {
                    $data = json_decode($backup->data, true);
                    
                    // กรองเอาเฉพาะฟิลด์ที่มีในโครงสร้างใหม่
                    $insertData = [];
                    $columns = Schema::getColumnListing('file_attachments');
                    
                    foreach ($data as $key => $value) {
                        if (in_array($key, $columns)) {
                            $insertData[$key] = $value;
                        }
                    }
                    
                    // เพิ่มข้อมูลกลับเข้าไป
                    if (!empty($insertData)) {
                        DB::table('file_attachments')->insert($insertData);
                        $restored++;
                    }
                }
                
                echo "คืนข้อมูลเข้าตาราง file_attachments จำนวน " . $restored . " รายการ\n";
            } catch (\Exception $e) {
                echo "เกิดข้อผิดพลาดในการคืนข้อมูล: " . $e->getMessage() . "\n";
            }
        } else {
            echo "ไม่พบตาราง file_attachments_backup ไม่สามารถคืนข้อมูลได้\n";
        }
    }
};
