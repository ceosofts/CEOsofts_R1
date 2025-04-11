<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // ลบ unique index เดิมของ email
            $table->dropUnique(['email']);
            
            // สร้าง compound unique index ใหม่
            $table->unique(['company_id', 'email'], 'customers_company_email_unique');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // ลบ compound unique index
            $table->dropUnique('customers_company_email_unique');
            
            // สร้าง unique index เดิม
            $table->unique(['email']);
        });
    }
};
