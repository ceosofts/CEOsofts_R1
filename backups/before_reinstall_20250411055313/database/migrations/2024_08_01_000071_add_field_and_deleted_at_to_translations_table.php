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
                // เพิ่มคอลัมน์ field หากยังไม่มี
                if (!in_array('field', $columns)) {
                    $table->string('field')->default('general')->after('key');
                }
                
                // เพิ่มคอลัมน์ deleted_at หากยังไม่มี
                if (!in_array('deleted_at', $columns)) {
                    $table->softDeletes();
                }
            });
            
            // อัปเดตข้อมูลเดิมในฟิลด์ field
            if (in_array('field', Schema::getColumnListing('translations'))) {
                DB::statement("UPDATE translations SET field = 'general' WHERE field IS NULL");
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
                if (Schema::hasColumn('translations', 'field')) {
                    $table->dropColumn('field');
                }
                
                if (Schema::hasColumn('translations', 'deleted_at')) {
                    $table->dropSoftDeletes();
                }
            });
        }
    }
};
