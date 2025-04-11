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
            $columns = Schema::getColumnListing('translations');
            
            Schema::table('translations', function (Blueprint $table) use ($columns) {
                // เพิ่มคอลัมน์ group ถ้ายังไม่มี
                if (!in_array('group', $columns)) {
                    $table->string('group')->nullable()->after('locale');
                    echo "เพิ่มคอลัมน์ group แล้ว\n";
                }
                
                // เพิ่มคอลัมน์ key ถ้ายังไม่มี
                if (!in_array('key', $columns)) {
                    $table->string('key')->nullable()->after('group');
                    echo "เพิ่มคอลัมน์ key แล้ว\n";
                }
                
                // เพิ่มคอลัมน์ value ถ้ายังไม่มี
                if (!in_array('value', $columns)) {
                    $table->text('value')->nullable()->after('key');
                    echo "เพิ่มคอลัมน์ value แล้ว\n";
                }

                // เพิ่มคอลัมน์ field ถ้ายังไม่มี
                if (!in_array('field', $columns)) {
                    $table->string('field')->default('general')->after('key');
                    echo "เพิ่มคอลัมน์ field แล้ว\n";
                }
                
                // เพิ่มคอลัมน์ translatable_type ถ้ายังไม่มี
                if (!in_array('translatable_type', $columns)) {
                    $table->string('translatable_type')->default('general')->after('value');
                    echo "เพิ่มคอลัมน์ translatable_type แล้ว\n";
                }
                
                // เพิ่มคอลัมน์ translatable_id ถ้ายังไม่มี
                if (!in_array('translatable_id', $columns)) {
                    $table->unsignedBigInteger('translatable_id')->default(0)->after('translatable_type');
                    echo "เพิ่มคอลัมน์ translatable_id แล้ว\n";
                }
            });

            // เพิ่ม index และ unique constraint
            try {
                Schema::table('translations', function (Blueprint $table) {
                    $table->index(['company_id', 'locale'], 'translations_company_locale_index');
                    $table->index(['company_id', 'group'], 'translations_company_group_index');
                });
            } catch (\Exception $e) {
                echo "ไม่สามารถเพิ่ม index ได้: " . $e->getMessage() . "\n";
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
        // ไม่จำเป็นต้องลบคอลัมน์ เพราะถือว่าคอลัมน์เหล่านี้เป็นส่วนหนึ่งของโครงสร้างหลักแล้ว
    }
};
