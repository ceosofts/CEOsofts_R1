<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->unique();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('restrict');
            $table->foreignId('position_id')->constrained()->onDelete('restrict');
            $table->foreignId('branch_office_id')->nullable()->constrained()->onDelete('set null');
            $table->string('employee_code', 50);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->date('birthdate')->nullable();
            $table->date('hire_date');
            $table->date('termination_date')->nullable();
            $table->string('id_card_number', 50)->nullable();
            $table->string('tax_id', 50)->nullable();
            $table->string('bank_account', 50)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->string('profile_image')->nullable();
            $table->string('status', 20)->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable();
            
            // Indexes
            $table->index('company_id');
            $table->index('department_id');
            $table->index('position_id');
            $table->index('employee_code');
            $table->index('email');
            $table->index('phone');
            $table->index('status');
            $table->index('hire_date');
            $table->index(['first_name', 'last_name']);
            
            // Unique constraints
            $table->unique(['company_id', 'employee_code']);
            $table->unique(['company_id', 'email']);
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
