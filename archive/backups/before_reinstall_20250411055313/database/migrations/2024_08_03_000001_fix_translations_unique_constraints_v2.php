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
            // ลบ unique constraint ที่มีปัญหา (ถ้ามี)
            try {
                DB::statement('ALTER TABLE translations DROP INDEX translations_unique_fields');
                echo "ลบ index translations_unique_fields สำเร็จ\n";
            } catch (\Exception $e) {
                echo "ไม่พบ index translations_unique_fields: " . $e->getMessage() . "\n";
            }
            
            try {
                DB::statement('ALTER TABLE translations DROP INDEX translations_translatable_type_translatable_id_locale_field_unique');
                echo "ลบ index translations_translatable_type_translatable_id_locale_field_unique สำเร็จ\n";
            } catch (\Exception $e) {
                echo "ไม่พบ index translations_translatable_type_translatable_id_locale_field_unique: " . $e->getMessage() . "\n";
            }
            
            try {
                DB::statement('ALTER TABLE translations DROP INDEX translations_unique_identity');
                echo "ลบ index translations_unique_identity สำเร็จ\n";
            } catch (\Exception $e) {
                echo "ไม่พบ index translations_unique_identity: " . $e->getMessage() . "\n";
            }

            // สร้าง unique constraint ใหม่
            try {
                DB::statement('ALTER TABLE translations ADD UNIQUE INDEX translations_company_locale_group_key_unique (company_id, locale, `group`, `key`)');
                echo "สร้าง index translations_company_locale_group_key_unique สำเร็จ\n";
            } catch (\Exception $e) {
                echo "ไม่สามารถสร้าง index translations_company_locale_group_key_unique ได้: " . $e->getMessage() . "\n";
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
            try {
                DB::statement('ALTER TABLE translations DROP INDEX translations_company_locale_group_key_unique');
            } catch (\Exception $e) {
                echo "ไม่สามารถลบ index translations_company_locale_group_key_unique ได้: " . $e->getMessage() . "\n";
            }
        }
    }
};
