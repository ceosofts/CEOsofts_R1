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
        if (Schema::hasTable('file_attachments')) {
            $columns = Schema::getColumnListing('file_attachments');
            
            if (!in_array('deleted_at', $columns)) {
                Schema::table('file_attachments', function (Blueprint $table) {
                    $table->softDeletes();
                });
                
                echo "เพิ่มคอลัมน์ deleted_at แล้ว\n";
            } else {
                echo "คอลัมน์ deleted_at มีอยู่แล้ว\n";
            }
        } else {
            echo "ไม่พบตาราง file_attachments\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('file_attachments')) {
            Schema::table('file_attachments', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
