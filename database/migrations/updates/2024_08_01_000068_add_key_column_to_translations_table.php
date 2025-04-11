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
        // เพิ่มคอลัมน์ key และคอลัมน์อื่น ๆ ที่จำเป็น
        if (Schema::hasTable('translations')) {
            Schema::table('translations', function (Blueprint $table) {
                // เพิ่มคอลัมน์ key หากยังไม่มี
                if (!Schema::hasColumn('translations', 'key')) {
                    $table->string('key')->after('group');
                }
                
                // เพิ่มคอลัมน์ value หากยังไม่มี
                if (!Schema::hasColumn('translations', 'value')) {
                    $table->text('value')->after('key');
                }
                
                // เพิ่มคอลัมน์ metadata หากยังไม่มี
                if (!Schema::hasColumn('translations', 'metadata')) {
                    $table->json('metadata')->nullable()->after('value');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ไม่จำเป็นต้องทำอะไรใน down method
    }
};
