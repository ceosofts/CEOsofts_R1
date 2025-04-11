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
        // ตรวจสอบว่ามีข้อมูลที่จำเป็นหรือไม่ ถ้าไม่มีหรือมีปัญหา ให้สร้างตารางใหม่
        if (Schema::hasTable('translations')) {
            // สำรองข้อมูลเดิม
            $this->backupTranslationsData();
            
            // ลบตารางเดิม
            Schema::dropIfExists('translations');
            
            // สร้างตารางใหม่
            $this->createTranslationsTable();
            
            // คืนข้อมูลเดิม
            $this->restoreTranslationsData();
        } else {
            $this->createTranslationsTable();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ไม่จำเป็นต้องทำอะไรเพราะเป็นการสร้างตารางใหม่
    }
    
    /**
     * สำรองข้อมูลเดิม
     */
    private function backupTranslationsData()
    {
        // ตรวจสอบว่ามีตารางสำรองหรือไม่
        if (!Schema::hasTable('translations_backup')) {
            Schema::create('translations_backup', function (Blueprint $table) {
                $table->id();
                $table->string('data', 10000); // เก็บข้อมูลในรูปแบบ JSON
                $table->timestamp('created_at')->useCurrent();
            });
        }
        
        // ดึงข้อมูลทั้งหมด
        $translations = DB::table('translations')->get();
        
        // เก็บข้อมูลในรูปแบบ JSON
        foreach ($translations as $translation) {
            DB::table('translations_backup')->insert([
                'data' => json_encode((array) $translation)
            ]);
        }
    }
    
    /**
     * สร้างตารางใหม่
     */
    private function createTranslationsTable()
    {
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('locale');
            $table->string('group');
            $table->string('key');
            $table->string('field')->default('general');
            $table->text('value')->nullable();
            $table->string('translatable_type')->default('general');
            $table->unsignedBigInteger('translatable_id')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // สร้าง index และ unique constraint
            $table->unique(['company_id', 'locale', 'group', 'key'], 'translations_company_locale_group_key_unique');
        });
    }
    
    /**
     * คืนข้อมูลเดิม
     */
    private function restoreTranslationsData()
    {
        if (Schema::hasTable('translations_backup')) {
            $backupData = DB::table('translations_backup')->get();
            
            foreach ($backupData as $record) {
                try {
                    $data = json_decode($record->data, true);
                    
                    // ตรวจสอบข้อมูลที่จำเป็น
                    if (isset($data['company_id'], $data['locale'])) {
                        // เพิ่มข้อมูลลงในตารางใหม่
                        DB::table('translations')->insert([
                            'company_id' => $data['company_id'],
                            'locale' => $data['locale'],
                            'group' => $data['group'] ?? '',
                            'key' => $data['key'] ?? '',
                            'field' => $data['field'] ?? 'general',
                            'value' => $data['value'] ?? '',
                            'translatable_type' => $data['translatable_type'] ?? 'general',
                            'translatable_id' => $data['translatable_id'] ?? 0,
                            'created_at' => $data['created_at'] ?? now(),
                            'updated_at' => $data['updated_at'] ?? now(),
                        ]);
                    }
                } catch (\Exception $e) {
                    // ข้ามข้อมูลที่มีปัญหา
                    continue;
                }
            }
        }
    }
};
