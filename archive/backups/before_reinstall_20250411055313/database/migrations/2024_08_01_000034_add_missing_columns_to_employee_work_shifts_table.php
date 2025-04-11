<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_work_shifts', function (Blueprint $table) {
            // เพิ่มคอลัมน์ที่จำเป็น
            if (!Schema::hasColumn('employee_work_shifts', 'work_date')) {
                $table->date('work_date')->after('work_shift_id');
            }
            if (!Schema::hasColumn('employee_work_shifts', 'status')) {
                $table->string('status')->default('scheduled')->after('work_date');
            }
            if (!Schema::hasColumn('employee_work_shifts', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
            if (!Schema::hasColumn('employee_work_shifts', 'metadata')) {
                $table->json('metadata')->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_work_shifts', function (Blueprint $table) {
            $table->dropColumn([
                'work_date',
                'status',
                'notes',
                'metadata'
            ]);
        });
    }
};
