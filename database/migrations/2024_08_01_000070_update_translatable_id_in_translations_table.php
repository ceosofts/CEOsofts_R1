<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // สำหรับ SQLite: ใช้วิธีสร้างตารางใหม่และย้ายข้อมูล
            Schema::create('translations_temp', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('translatable_id')->default(0);
                $table->string('translatable_type');
                $table->string('locale');
                $table->text('key')->nullable();
                $table->text('value')->nullable();
                $table->timestamps();
            });

            // ย้ายข้อมูลจากตารางเดิมไปยังตารางใหม่
            DB::statement('
                INSERT INTO translations_temp (id, translatable_id, translatable_type, locale, key, value, created_at, updated_at)
                SELECT id, COALESCE(translatable_id, 0), translatable_type, locale, key, value, created_at, updated_at
                FROM translations
            ');

            // ลบตารางเดิมและเปลี่ยนชื่อ
            Schema::drop('translations');
            Schema::rename('translations_temp', 'translations');
        } else {
            // สำหรับ MySQL หรือฐานข้อมูลอื่นๆ
            Schema::table('translations', function (Blueprint $table) {
                $table->unsignedBigInteger('translatable_id')->default(0)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // สำหรับ SQLite: ใช้วิธีสร้างตารางใหม่และย้ายข้อมูลกลับ
            Schema::create('translations_temp', function (Blueprint $table) {
                $table->id();
                $table->integer('translatable_id')->nullable();
                $table->string('translatable_type');
                $table->string('locale');
                $table->text('key')->nullable();
                $table->text('value')->nullable();
                $table->timestamps();
            });

            // ย้ายข้อมูลกลับไปยังตารางใหม่
            DB::statement('
                INSERT INTO translations_temp (id, translatable_id, translatable_type, locale, key, value, created_at, updated_at)
                SELECT id, translatable_id, translatable_type, locale, key, value, created_at, updated_at
                FROM translations
            ');

            // ลบตารางเดิมและเปลี่ยนชื่อ
            Schema::drop('translations');
            Schema::rename('translations_temp', 'translations');
        } else {
            // สำหรับ MySQL หรือฐานข้อมูลอื่นๆ
            Schema::table('translations', function (Blueprint $table) {
                $table->integer('translatable_id')->nullable()->change();
            });
        }
    }
};
