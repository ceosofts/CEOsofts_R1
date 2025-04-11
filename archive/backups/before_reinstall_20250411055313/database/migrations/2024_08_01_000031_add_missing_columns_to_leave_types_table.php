<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_types', function (Blueprint $table) {
            if (!Schema::hasColumn('leave_types', 'days_allowed')) {
                $table->float('days_allowed')->default(0)->after('description');
            }
            if (!Schema::hasColumn('leave_types', 'days_advance_notice')) {
                $table->integer('days_advance_notice')->default(0)->after('days_allowed');
            }
            if (!Schema::hasColumn('leave_types', 'requires_approval')) {
                $table->boolean('requires_approval')->default(true)->after('days_advance_notice');
            }
            if (!Schema::hasColumn('leave_types', 'requires_attachment')) {
                $table->boolean('requires_attachment')->default(false)->after('requires_approval');
            }
            if (!Schema::hasColumn('leave_types', 'is_paid')) {
                $table->boolean('is_paid')->default(true)->after('requires_attachment');
            }
            if (!Schema::hasColumn('leave_types', 'color')) {
                $table->string('color', 20)->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('leave_types', 'icon')) {
                $table->string('icon', 50)->nullable()->after('color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->dropColumn([
                'days_allowed',
                'days_advance_notice',
                'requires_approval',
                'requires_attachment',
                'is_paid',
                'color',
                'icon'
            ]);
        });
    }
};
