<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // เปลี่ยนขนาดคอลัมน์ uuid เป็น char(36) เพื่อรองรับ UUID มาตรฐาน
            $table->string('uuid', 36)->change();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('uuid')->change();
        });
    }
};
