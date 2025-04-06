<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('id_card_number');
            }
            if (!Schema::hasColumn('employees', 'hire_date')) {
                $table->date('hire_date')->nullable()->after('birth_date');
            }
            if (!Schema::hasColumn('employees', 'employee_code')) {
                $table->string('employee_code')->after('branch_office_id');
            }
            if (!Schema::hasColumn('employees', 'first_name')) {
                $table->string('first_name')->after('employee_code');
            }
            if (!Schema::hasColumn('employees', 'last_name')) {
                $table->string('last_name')->after('first_name');
            }
            if (!Schema::hasColumn('employees', 'phone')) {
                $table->string('phone', 20)->nullable()->after('email');
            }
            if (!Schema::hasColumn('employees', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('employees', 'id_card_number')) {
                $table->string('id_card_number', 13)->nullable()->after('address');
            }
            if (!Schema::hasColumn('employees', 'status')) {
                $table->string('status', 20)->default('active')->after('hire_date');
            }
            if (!Schema::hasColumn('employees', 'metadata')) {
                $table->json('metadata')->nullable()->after('status');
            }
            if (!Schema::hasColumn('employees', 'uuid')) {
                $table->uuid('uuid')->after('id')->unique();
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'birth_date',
                'hire_date',
                'employee_code',
                'first_name',
                'last_name',
                'phone',
                'address',
                'id_card_number',
                'status',
                'metadata'
            ]);
        });
    }
};
