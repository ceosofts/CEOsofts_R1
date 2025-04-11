<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table) {
            if (!Schema::hasColumn('units', 'symbol')) {
                $table->string('symbol', 10)->after('name');
            }
            if (!Schema::hasColumn('units', 'base_unit_id')) {
                $table->foreignId('base_unit_id')->nullable()->after('symbol')
                    ->references('id')->on('units')->onDelete('set null');
            }
            if (!Schema::hasColumn('units', 'conversion_factor')) {
                $table->decimal('conversion_factor', 10, 4)->default(1)->after('base_unit_id');
            }
            if (!Schema::hasColumn('units', 'is_base_unit')) {
                $table->boolean('is_base_unit')->default(false)->after('conversion_factor');
            }
            if (!Schema::hasColumn('units', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_base_unit');
            }
            if (!Schema::hasColumn('units', 'metadata')) {
                $table->json('metadata')->nullable()->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropForeign(['base_unit_id']);
            $table->dropColumn([
                'symbol',
                'base_unit_id',
                'conversion_factor',
                'is_base_unit',
                'is_active',
                'metadata'
            ]);
        });
    }
};
