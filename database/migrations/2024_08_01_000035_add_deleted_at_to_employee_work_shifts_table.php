<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_work_shifts', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_work_shifts', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_work_shifts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
