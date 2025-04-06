<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_shifts', function (Blueprint $table) {
            if (!Schema::hasColumn('work_shifts', 'code')) {
                $table->string('code', 20)->after('name')->unique();
            }
            if (!Schema::hasColumn('work_shifts', 'description')) {
                $table->text('description')->nullable()->after('code');
            }
            if (!Schema::hasColumn('work_shifts', 'start_time')) {
                $table->time('start_time')->after('description');
            }
            if (!Schema::hasColumn('work_shifts', 'end_time')) {
                $table->time('end_time')->after('start_time');
            }
            if (!Schema::hasColumn('work_shifts', 'break_start')) {
                $table->time('break_start')->nullable()->after('end_time');
            }
            if (!Schema::hasColumn('work_shifts', 'break_end')) {
                $table->time('break_end')->nullable()->after('break_start');
            }
            if (!Schema::hasColumn('work_shifts', 'working_hours')) {
                $table->float('working_hours')->default(8)->after('break_end');
            }
            if (!Schema::hasColumn('work_shifts', 'is_night_shift')) {
                $table->boolean('is_night_shift')->default(false)->after('working_hours');
            }
            if (!Schema::hasColumn('work_shifts', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_night_shift');
            }
            if (!Schema::hasColumn('work_shifts', 'color')) {
                $table->string('color', 20)->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('work_shifts', 'metadata')) {
                $table->json('metadata')->nullable()->after('color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('work_shifts', function (Blueprint $table) {
            $table->dropColumn([
                'code',
                'description',
                'start_time',
                'end_time',
                'break_start',
                'break_end',
                'working_hours',
                'is_night_shift',
                'is_active',
                'color',
                'metadata'
            ]);
        });
    }
};
