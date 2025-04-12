<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('product_categories', 'slug')) {
                $table->string('slug')->nullable()->after('name');
            }
            if (!Schema::hasColumn('product_categories', 'level')) {
                $table->integer('level')->default(0)->after('is_active');
            }
            if (!Schema::hasColumn('product_categories', 'path')) {
                $table->string('path')->nullable()->after('level');
            }
            if (!Schema::hasColumn('product_categories', 'metadata')) {
                $table->json('metadata')->nullable()->after('path');
            }

            // เพิ่ม index สำหรับการค้นหา
            $table->index(['company_id', 'slug']);
            $table->index(['company_id', 'path']);
        });
    }

    public function down(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            // ลบ indexes ก่อน
            $table->dropIndex(['company_id', 'slug']);
            $table->dropIndex(['company_id', 'path']);

            // ลบคอลัมน์
            $table->dropColumn([
                'slug',
                'level',
                'path',
                'metadata'
            ]);
        });
    }
};
