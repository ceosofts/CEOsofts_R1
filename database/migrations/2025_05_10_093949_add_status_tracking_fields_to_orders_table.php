<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Check if the columns don't exist before adding them to avoid errors
            if (!Schema::hasColumn('orders', 'shipped_by')) {
                $table->unsignedBigInteger('shipped_by')->nullable();
            }
            
            // Add foreign key relationship if needed
            if (Schema::hasColumn('orders', 'shipped_by') && !Schema::hasColumn('orders', 'foreign_shipped_by')) {
                $table->foreign('shipped_by')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop foreign key first if it exists
            if (Schema::hasColumn('orders', 'shipped_by')) {
                // For SQLite, we can't drop foreign keys, so we'll just drop the column
                $table->dropColumn('shipped_by');
            }
        });
    }
};
