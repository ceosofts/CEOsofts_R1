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
        if (Schema::hasTable('translations')) {
            try {
                // ดึงข้อมูล index ทั้งหมดจากตาราง translations
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $indexes = $sm->listTableIndexes('translations');
                
                // ลบ unique constraint ที่มีปัญหาถ้ามี
                Schema::table('translations', function (Blueprint $table) use ($indexes) {
                    if (isset($indexes['translations_unique_fields'])) {
                        $table->dropIndex('translations_unique_fields');
                        echo "ลบ index translations_unique_fields\n";
                    }
                    
                    if (isset($indexes['translations_translatable_type_translatable_id_locale_field_unique'])) {
                        $table->dropIndex('translations_translatable_type_translatable_id_locale_field_unique');
                        echo "ลบ index translations_translatable_type_translatable_id_locale_field_unique\n";
                    }

                    // ลบ index อื่นๆ ที่อาจเกี่ยวข้อง
                    foreach ($indexes as $name => $index) {
                        if (str_contains($name, 'unique') && str_contains($name, 'translations')) {
                            $table->dropIndex($name);
                            echo "ลบ index {$name}\n";
                        }
                    }
                });
                
                // สร้าง unique constraint ใหม่ที่ถูกต้อง
                Schema::table('translations', function (Blueprint $table) {
                    $table->unique(['company_id', 'locale', 'group', 'key'], 'translations_unique_identity');
                    echo "สร้าง index translations_unique_identity สำเร็จ\n";
                });
                
            } catch (\Exception $e) {
                echo "เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
            }
        } else {
            echo "ไม่พบตาราง translations\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('translations')) {
            Schema::table('translations', function (Blueprint $table) {
                $table->dropIndex('translations_unique_identity');
            });
        }
    }
};
