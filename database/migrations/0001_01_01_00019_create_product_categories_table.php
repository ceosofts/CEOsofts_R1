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
        if (!Schema::hasTable('product_categories')) {
            Schema::create('product_categories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
                $table->string('name');
                $table->string('code')->nullable();
                $table->text('description')->nullable();
                $table->foreignId('parent_id')->nullable()->constrained('product_categories')->nullOnDelete();
                $table->integer('level')->default(1); // เพิ่มคอลัมน์ level ที่จำเป็นต้องใช้
                $table->string('path')->nullable();    // เพิ่มคอลัมน์ path ที่จำเป็นต้องใช้
                $table->integer('ordering')->default(0);
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
            });
            
            echo "สร้างตาราง product_categories เรียบร้อยแล้ว\n";
        } else {
            // ตรวจสอบและเพิ่มคอลัมน์ที่อาจจะขาดหายไป
            Schema::table('product_categories', function (Blueprint $table) {
                if (!Schema::hasColumn('product_categories', 'level')) {
                    $table->integer('level')->default(1)->after('parent_id');
                    echo "เพิ่มคอลัมน์ level ในตาราง product_categories\n";
                }
                
                if (!Schema::hasColumn('product_categories', 'path')) {
                    $table->string('path')->nullable()->after('level');
                    echo "เพิ่มคอลัมน์ path ในตาราง product_categories\n";
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
