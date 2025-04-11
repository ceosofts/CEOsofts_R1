<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_work_shifts', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_work_shifts', 'effective_date')) {
                $table->date('effective_date')->nullable()->after('work_shift_id');
            }
        });

        // อัปเดตข้อมูลที่มีอยู่ โดยใช้ work_date เป็นค่า effective_date
        DB::statement('UPDATE employee_work_shifts SET effective_date = work_date WHERE effective_date IS NULL');
    }

    public function down(): void
    {
        Schema::table('employee_work_shifts', function (Blueprint $table) {
            $table->dropColumn('effective_date');
        });
    }
};
