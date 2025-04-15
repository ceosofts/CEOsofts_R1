<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('departments', function (Blueprint $table) {
            // เพิ่ม parent_id สำหรับทำโครงสร้างองค์กรแบบลำดับชั้น
            if (!Schema::hasColumn('departments', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('branch_office_id');
                $table->foreign('parent_id')->references('id')->on('departments')->onDelete('set null');
            }
            
            // เพิ่ม manager_id สำหรับกำหนดหัวหน้าแผนก
            if (!Schema::hasColumn('departments', 'manager_id')) {
                $table->unsignedBigInteger('manager_id')->nullable()->after('department_code');
                $table->foreign('manager_id')->references('id')->on('employees')->onDelete('set null');
            }
            
            // เพิ่ม level สำหรับกำหนดลำดับชั้นของแผนก
            if (!Schema::hasColumn('departments', 'level')) {
                $table->integer('level')->default(0)->after('parent_id');
            }
        });
        
        // สร้างตาราง Many-to-Many ระหว่าง departments และ positions
        if (!Schema::hasTable('department_position')) {
            Schema::create('department_position', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('department_id');
                $table->unsignedBigInteger('position_id');
                $table->timestamps();
                
                $table->unique(['department_id', 'position_id']);
                $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
                $table->foreign('position_id')->references('id')->on('positions')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('department_position');
        
        Schema::table('departments', function (Blueprint $table) {
            if (Schema::hasColumn('departments', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
            
            if (Schema::hasColumn('departments', 'manager_id')) {
                $table->dropForeign(['manager_id']);
                $table->dropColumn('manager_id');
            }
            
            if (Schema::hasColumn('departments', 'level')) {
                $table->dropColumn('level');
            }
        });
    }
};
