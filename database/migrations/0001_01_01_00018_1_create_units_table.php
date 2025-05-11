<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('units')) {
            Schema::create('units', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->string('code')->nullable();       // เพิ่มคอลัมน์ code
                $table->string('symbol')->nullable();     // เพิ่มคอลัมน์ symbol
                $table->foreignId('base_unit_id')->nullable(); // เพิ่มคอลัมน์ base_unit_id
                $table->decimal('conversion_factor', 15, 5)->default(1); // เพิ่มคอลัมน์ conversion_factor
                $table->boolean('is_default')->default(false); // เพิ่มคอลัมน์ is_default
                $table->string('type')->default('standard'); // เพิ่มคอลัมน์ type
                $table->string('category')->nullable();   // เพิ่มคอลัมน์ category
                $table->string('abbreviation')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
                $table->softDeletes();
            });
            
            echo "สร้างตาราง units เรียบร้อยแล้ว\n";
            Log::info('สร้างตาราง units เรียบร้อยแล้ว');
        } else {
            // ตรวจสอบและเพิ่มคอลัมน์ที่อาจจะขาดหายไป
            Schema::table('units', function (Blueprint $table) {
                if (!Schema::hasColumn('units', 'code')) {
                    $table->string('code')->nullable()->after('name');
                    echo "เพิ่มคอลัมน์ code ในตาราง units\n";
                }
                
                if (!Schema::hasColumn('units', 'symbol')) {
                    $table->string('symbol')->nullable()->after('code');
                    echo "เพิ่มคอลัมน์ symbol ในตาราง units\n";
                }
                
                if (!Schema::hasColumn('units', 'base_unit_id')) {
                    $table->foreignId('base_unit_id')->nullable()->after('symbol');
                    echo "เพิ่มคอลัมน์ base_unit_id ในตาราง units\n";
                }
                
                if (!Schema::hasColumn('units', 'conversion_factor')) {
                    $table->decimal('conversion_factor', 15, 5)->default(1)->after('base_unit_id');
                    echo "เพิ่มคอลัมน์ conversion_factor ในตาราง units\n";
                }
                
                if (!Schema::hasColumn('units', 'is_default')) {
                    $table->boolean('is_default')->default(false)->after('conversion_factor');
                    echo "เพิ่มคอลัมน์ is_default ในตาราง units\n";
                }
                
                if (!Schema::hasColumn('units', 'type')) {
                    $table->string('type')->default('standard')->after('is_default');
                    echo "เพิ่มคอลัมน์ type ในตาราง units\n";
                }
                
                if (!Schema::hasColumn('units', 'category')) {
                    $table->string('category')->nullable()->after('type');
                    echo "เพิ่มคอลัมน์ category ในตาราง units\n";
                }
                
                if (!Schema::hasColumn('units', 'abbreviation')) {
                    $table->string('abbreviation')->nullable()->after('name');
                    echo "เพิ่มคอลัมน์ abbreviation ในตาราง units\n";
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
