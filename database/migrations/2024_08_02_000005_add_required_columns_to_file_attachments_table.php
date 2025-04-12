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
        if (Schema::hasTable('file_attachments')) {
            // ตรวจสอบคอลัมน์ที่มีอยู่แล้ว
            $columns = Schema::getColumnListing('file_attachments');
            
            // เพิ่มคอลัมน์ที่จำเป็น
            Schema::table('file_attachments', function (Blueprint $table) use ($columns) {
                // name
                if (!in_array('name', $columns)) {
                    $table->string('name')->nullable()->after('attachable_id');
                    echo "เพิ่มคอลัมน์ name แล้ว\n";
                }
                
                // original_name
                if (!in_array('original_name', $columns)) {
                    $table->string('original_name')->nullable()->after('name');
                    echo "เพิ่มคอลัมน์ original_name แล้ว\n";
                }
                
                // disk
                if (!in_array('disk', $columns)) {
                    $table->string('disk')->default('local')->after('original_name');
                    echo "เพิ่มคอลัมน์ disk แล้ว\n";
                }
                
                // path
                if (!in_array('path', $columns)) {
                    $table->string('path')->nullable()->after('disk');
                    echo "เพิ่มคอลัมน์ path แล้ว\n";
                }
                
                // mime_type
                if (!in_array('mime_type', $columns)) {
                    $table->string('mime_type')->nullable()->after('path');
                    echo "เพิ่มคอลัมน์ mime_type แล้ว\n";
                }
                
                // size
                if (!in_array('size', $columns)) {
                    $table->unsignedBigInteger('size')->default(0)->after('mime_type');
                    echo "เพิ่มคอลัมน์ size แล้ว\n";
                }
                
                // created_by
                if (!in_array('created_by', $columns)) {
                    $table->unsignedBigInteger('created_by')->nullable()->after('size');
                    echo "เพิ่มคอลัมน์ created_by แล้ว\n";
                }
                
                // metadata
                if (!in_array('metadata', $columns)) {
                    $table->json('metadata')->nullable()->after('created_by');
                    echo "เพิ่มคอลัมน์ metadata แล้ว\n";
                }
            });
            
            // เพิ่ม foreign key constraints ถ้าจำเป็น
            try {
                Schema::table('file_attachments', function (Blueprint $table) {
                    if (!$this->hasForeignKey('file_attachments', 'created_by')) {
                        $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
                        echo "เพิ่ม foreign key สำหรับ created_by แล้ว\n";
                    }
                });
            } catch (\Exception $e) {
                echo "ไม่สามารถเพิ่ม foreign key ได้: " . $e->getMessage() . "\n";
            }
            
        } else {
            echo "ไม่พบตาราง file_attachments ทำการสร้างตารางใหม่\n";
            
            // สร้างตารางใหม่
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
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ไม่ต้องทำอะไรสำหรับ down เพราะเราไม่ควรลบคอลัมน์สำคัญเหล่านี้ออก
    }
    
    /**
     * ตรวจสอบว่ามี foreign key อยู่แล้วหรือไม่
     */
    private function hasForeignKey($table, $column)
    {
        $fks = DB::select("
            SELECT *
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = '{$table}'
            AND COLUMN_NAME = '{$column}'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        return count($fks) > 0;
    }
};
