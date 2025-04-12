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
            // เพิ่มคอลัมน์ code หลัง id
            if (!Schema::hasColumn('companies', 'code')) {
                $table->string('code')->nullable()->after('id');
            }

            // เพิ่มคอลัมน์ status หลัง is_active
            if (!Schema::hasColumn('companies', 'status')) {
                $table->string('status')->nullable()->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // ลบคอลัมน์ที่เพิ่ม
            if (Schema::hasColumn('companies', 'code')) {
                $table->dropColumn('code');
            }

            if (Schema::hasColumn('companies', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
