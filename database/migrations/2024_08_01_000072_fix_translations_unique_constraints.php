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
            // ลบ unique constraint เดิมถ้ามี (ใช้ DB statement แทน Doctrine SchemaManager)
            try {
                DB::statement('ALTER TABLE translations DROP INDEX translations_unique_fields');
                echo "ลบ index translations_unique_fields สำเร็จ\n";
            } catch (\Exception $e) {
                echo "ไม่พบ index translations_unique_fields\n";
            }
            
            try {
                DB::statement('ALTER TABLE translations DROP INDEX translations_unique');
                echo "ลบ index translations_unique สำเร็จ\n";
            } catch (\Exception $e) {
                echo "ไม่พบ index translations_unique\n";
            }
            
            // สร้าง unique constraint ใหม่
            try {
                Schema::table('translations', function (Blueprint $table) {
                    $table->unique(['company_id', 'locale', 'group', 'key'], 'translations_unique_company_locale_group_key');
                });
                echo "สร้าง constraint สำเร็จ\n";
            } catch (\Exception $e) {
                echo "ไม่สามารถสร้าง constraint ได้: " . $e->getMessage() . "\n";
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
                Schema::table('translations', function (Blueprint $table) {
                    $table->dropIndex('translations_unique_company_locale_group_key');
                });
            } catch (\Exception $e) {
                echo "ไม่สามารถลบ index ได้: " . $e->getMessage() . "\n";
            }
        }
    }
};
