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
        // สำรองข้อมูลก่อนใช้
        $this->backupTranslationsData();
        
        // ลบตารางเดิมถ้ามี
        Schema::dropIfExists('translations');
        
        // สร้างตารางใหม่
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
            
            // สร้าง index
            $table->index(['company_id', 'locale']);
            $table->index(['company_id', 'group']);
            
            // สร้าง unique constraint
            $table->unique(['company_id', 'locale', 'group', 'key'], 'translations_unique_index');
        });
        
        // คืนข้อมูลเดิม
        $this->restoreTranslationsData();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
    
    /**
     * สำรองข้อมูลเดิม
     */
    private function backupTranslationsData()
    {
        if (Schema::hasTable('translations')) {
            // สร้างตาราง backup ถ้ายังไม่มี
            if (!Schema::hasTable('translations_backup')) {
                Schema::create('translations_backup', function (Blueprint $table) {
                    $table->id();
                    $table->text('data'); // เก็บข้อมูลในรูปแบบ JSON
                    $table->timestamp('created_at')->useCurrent();
                });
            } else {
                // ถ้ามีตาราง backup แล้ว ให้ล้างข้อมูลเดิมก่อน
                DB::table('translations_backup')->truncate();
            }
            
            // ดึงข้อมูลทั้งหมดและบันทึกลงตาราง backup
            $translations = DB::table('translations')->get();
            foreach ($translations as $translation) {
                DB::table('translations_backup')->insert([
                    'data' => json_encode((array) $translation)
                ]);
            }
            
            echo "สำรองข้อมูล translations จำนวน " . count($translations) . " รายการ\n";
        }
    }
    
    /**
     * คืนข้อมูลเดิม
     */
    private function restoreTranslationsData()
    {
        if (Schema::hasTable('translations_backup')) {
            $backupData = DB::table('translations_backup')->get();
            $restoredCount = 0;
            
            foreach ($backupData as $record) {
                try {
                    $data = json_decode($record->data, true);
                    
                    // ตรวจสอบข้อมูลที่จำเป็น
                    if (isset($data['company_id'], $data['locale'])) {
                        // กำหนดค่าที่จำเป็นสำหรับการ insert
                        $insertData = [
                            'company_id' => $data['company_id'],
                            'locale' => $data['locale'],
                            'group' => $data['group'] ?? '',
                            'key' => $data['key'] ?? '',
                            'field' => $data['field'] ?? 'general',
                            'value' => $data['value'] ?? '',
                            'translatable_type' => $data['translatable_type'] ?? 'general',
                            'translatable_id' => $data['translatable_id'] ?? 0,
                            'created_at' => $data['created_at'] ?? now()->toDateTimeString(),
                            'updated_at' => $data['updated_at'] ?? now()->toDateTimeString(),
                        ];
                        
                        // เพิ่มค่า metadata ถ้ามี
                        if (isset($data['metadata'])) {
                            $insertData['metadata'] = $data['metadata'];
                        }
                        
                        // ตรวจสอบว่ามีข้อมูลนี้อยู่แล้วหรือไม่
                        $exists = DB::table('translations')
                            ->where('company_id', $insertData['company_id'])
                            ->where('locale', $insertData['locale'])
                            ->where('group', $insertData['group'])
                            ->where('key', $insertData['key'])
                            ->exists();
                            
                        if (!$exists) {
                            DB::table('translations')->insert($insertData);
                            $restoredCount++;
                        }
                    }
                } catch (\Exception $e) {
                    echo "ไม่สามารถคืนข้อมูลได้: " . $e->getMessage() . "\n";
                }
            }
            
            echo "คืนข้อมูล translations จำนวน " . $restoredCount . " รายการ\n";
        }
    }
};
