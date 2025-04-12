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
        Schema::table('companies', function (Blueprint $table) {
            // เพิ่มคอลัมน์ metadata ถ้ายังไม่มี
            if (!Schema::hasColumn('companies', 'metadata')) {
                $table->json('metadata')->nullable()->after('settings');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // ลบคอลัมน์ถ้ามีอยู่
            if (Schema::hasColumn('companies', 'metadata')) {
                $table->dropColumn('metadata');
            }
        });
    }
};
