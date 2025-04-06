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
        // ตรวจสอบโครงสร้างตาราง translations ปัจจุบัน
        $hasTable = Schema::hasTable('translations');
        $columns = $hasTable ? Schema::getColumnListing('translations') : [];
        
        if ($hasTable) {
            // ตารางมีอยู่แล้ว ตรวจสอบและเพิ่มคอลัมน์ที่จำเป็น
            Schema::table('translations', function (Blueprint $table) use ($columns) {
                // เพิ่มคอลัมน์ company_id ถ้ายังไม่มี
                if (!in_array('company_id', $columns)) {
                    $table->foreignId('company_id')->after('id')->constrained()->onDelete('cascade');
                }
                
                // เพิ่มคอลัมน์ locale ถ้ายังไม่มี
                if (!in_array('locale', $columns)) {
                    $table->string('locale')->after('company_id');
                }
                
                // เพิ่มคอลัมน์ group ถ้ายังไม่มี
                if (!in_array('group', $columns)) {
                    $table->string('group')->after('locale');
                }
                
                // เพิ่มคอลัมน์ key ถ้ายังไม่มี
                if (!in_array('key', $columns)) {
                    $table->string('key')->after('group');
                }
                
                // เพิ่มคอลัมน์ value ถ้ายังไม่มี
                if (!in_array('value', $columns)) {
                    $table->text('value')->after('key');
                }
                
                // เพิ่มคอลัมน์ metadata ถ้ายังไม่มี
                if (!in_array('metadata', $columns)) {
                    $table->json('metadata')->nullable()->after('value');
                }
                
                // เพิ่มคอลัมน์ deleted_at ถ้ายังไม่มี
                if (!in_array('deleted_at', $columns)) {
                    $table->softDeletes();
                }

                // สร้าง Unique constraint ถ้าสามารถทำได้
                try {
                    $tableIndexes = DB::select("SHOW INDEXES FROM translations");
                    $indexNames = collect($tableIndexes)->pluck('Key_name')->unique()->toArray();
                    
                    if (!in_array('translations_unique', $indexNames)) {
                        $table->unique(['company_id', 'locale', 'group', 'key'], 'translations_unique');
                    }
                    
                    if (!in_array('translations_company_id_locale_index', $indexNames)) {
                        $table->index(['company_id', 'locale']);
                    }
                    
                    if (!in_array('translations_company_id_group_index', $indexNames)) {
                        $table->index(['company_id', 'group']);
                    }
                } catch (\Exception $e) {
                    // หากเกิดข้อผิดพลาดในการเพิ่ม index ให้ข้ามไปและไม่ล้มเหลวทั้งหมด
                }
            });
        } else {
            // ตารางยังไม่มี ให้สร้างใหม่
            Schema::create('translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->string('locale');
                $table->string('group');
                $table->string('key');
                $table->text('value');
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // Unique constraint - one translation per key per locale per group per company
                $table->unique(['company_id', 'locale', 'group', 'key'], 'translations_unique');
                
                // Indexes for faster lookups
                $table->index(['company_id', 'locale']);
                $table->index(['company_id', 'group']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ไม่ต้องทำอะไร เพราะนี่เป็นการอัพเดทตาราง
    }
};
