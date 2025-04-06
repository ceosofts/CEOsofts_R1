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
            // ตรวจสอบว่าตารางมีคอลัมน์ที่จำเป็นหรือไม่
            $columns = Schema::getColumnListing('translations');
            
            Schema::table('translations', function (Blueprint $table) use ($columns) {
                // เพิ่มคอลัมน์ translatable_type
                if (!in_array('translatable_type', $columns)) {
                    $table->string('translatable_type')->default('general')->after('value');
                }
                
                // เพิ่มคอลัมน์ translatable_id
                if (!in_array('translatable_id', $columns)) {
                    $table->unsignedBigInteger('translatable_id')->nullable()->after('translatable_type');
                }
                
                // เพิ่มดัชนีสำหรับความเร็วในการค้นหา
                try {
                    $tableIndexes = DB::select("SHOW INDEXES FROM translations");
                    $indexNames = collect($tableIndexes)->pluck('Key_name')->unique()->toArray();
                    
                    if (!in_array('translations_translatable_index', $indexNames)) {
                        $table->index(['translatable_type', 'translatable_id'], 'translations_translatable_index');
                    }
                } catch (\Exception $e) {
                    // หากเกิดข้อผิดพลาดในการเพิ่ม index ให้ข้ามไป
                }
            });
            
            // อัพเดทข้อมูลเดิมให้มีค่า translatable_type
            if (in_array('translatable_type', Schema::getColumnListing('translations'))) {
                DB::statement("UPDATE translations SET translatable_type = 'general' WHERE translatable_type IS NULL OR translatable_type = ''");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('translations')) {
            Schema::table('translations', function (Blueprint $table) {
                $table->dropIndex('translations_translatable_index');
                $table->dropColumn(['translatable_type', 'translatable_id']);
            });
        }
    }
};
