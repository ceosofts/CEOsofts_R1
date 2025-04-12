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
        // ลบตารางเก่าถ้ามี
        Schema::dropIfExists('translations');

        // สร้างตารางใหม่
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('locale', 10);
            $table->string('group', 100)->nullable();
            $table->string('namespace', 100)->nullable()->default('*');
            $table->string('key', 255);
            $table->text('value')->nullable();

            // ฟิลด์สำหรับ translatable models
            $table->string('translatable_type')->nullable();
            $table->unsignedBigInteger('translatable_id')->default(0);
            $table->string('field', 100)->nullable();

            // ข้อมูลเพิ่มเติม
            $table->json('metadata')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('locale');
            $table->index(['company_id', 'locale']);
            $table->index(['company_id', 'group']);
            $table->index(['translatable_type', 'translatable_id']);
        });

        // เพิ่ม foreign keys
        if (Schema::hasTable('users')) {
            try {
                Schema::table('translations', function (Blueprint $table) {
                    $table->foreign('created_by')
                        ->references('id')
                        ->on('users')
                        ->onDelete('set null');

                    $table->foreign('updated_by')
                        ->references('id')
                        ->on('users')
                        ->onDelete('set null');
                });
            } catch (\Exception $e) {
                Log::warning("ไม่สามารถสร้าง foreign keys ได้: " . $e->getMessage());
            }
        }

        // เพิ่มข้อมูลพื้นฐาน
        if (DB::table('companies')->count() > 0) {
            try {
                $companyId = DB::table('companies')->first()->id;

                $basicTranslations = [
                    // ภาษาไทย
                    ['company_id' => $companyId, 'locale' => 'th', 'group' => 'app', 'key' => 'name', 'value' => 'ระบบบริหารจัดการ'],
                    ['company_id' => $companyId, 'locale' => 'th', 'group' => 'app', 'key' => 'welcome', 'value' => 'ยินดีต้อนรับ'],

                    // ภาษาอังกฤษ
                    ['company_id' => $companyId, 'locale' => 'en', 'group' => 'app', 'key' => 'name', 'value' => 'Management System'],
                    ['company_id' => $companyId, 'locale' => 'en', 'group' => 'app', 'key' => 'welcome', 'value' => 'Welcome'],
                ];

                foreach ($basicTranslations as $translation) {
                    DB::table('translations')->insert($translation);
                }
            } catch (\Exception $e) {
                Log::warning("ไม่สามารถเพิ่มข้อมูลพื้นฐานได้: " . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
