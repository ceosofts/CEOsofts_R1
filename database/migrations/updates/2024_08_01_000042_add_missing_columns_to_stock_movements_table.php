<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ตรวจสอบว่าตารางมีอยู่หรือไม่
        if (!Schema::hasTable('stock_movements')) {
            Schema::create('stock_movements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->onDelete('cascade');
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->string('type'); // receive, issue, transfer, adjust
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->decimal('quantity', 15, 2)->default(0);
                $table->decimal('before_quantity', 15, 2)->default(0);
                $table->decimal('after_quantity', 15, 2)->default(0);
                $table->decimal('unit_cost', 15, 2)->default(0);
                $table->decimal('total_cost', 15, 2)->default(0);
                $table->string('location')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('processed_by')->nullable()->constrained('users');
                $table->timestamp('processed_at')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();

                // สร้าง indexes
                $table->index(['company_id', 'type']);
                $table->index(['company_id', 'product_id']);
                $table->index(['reference_type', 'reference_id']);
                $table->index('processed_at');
            });
        } else {
            // ถ้าตารางมีอยู่แล้ว เพิ่มคอลัมน์ที่ขาดไป
            Schema::table('stock_movements', function (Blueprint $table) {
                $addColumns = [
                    'type' => fn ($table) => $table->string('type')->after('product_id'),
                    'reference_type' => fn ($table) => $table->string('reference_type')->nullable()->after('type'),
                    'reference_id' => fn ($table) => $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type'),
                    'quantity' => fn ($table) => $table->decimal('quantity', 15, 2)->default(0)->after('reference_id'),
                    'before_quantity' => fn ($table) => $table->decimal('before_quantity', 15, 2)->default(0)->after('quantity'),
                    'after_quantity' => fn ($table) => $table->decimal('after_quantity', 15, 2)->default(0)->after('before_quantity'),
                    'unit_cost' => fn ($table) => $table->decimal('unit_cost', 15, 2)->default(0)->after('after_quantity'),
                    'total_cost' => fn ($table) => $table->decimal('total_cost', 15, 2)->default(0)->after('unit_cost'),
                    'location' => fn ($table) => $table->string('location')->nullable()->after('total_cost'),
                    'notes' => fn ($table) => $table->text('notes')->nullable()->after('location'),
                ];

                foreach ($addColumns as $column => $callback) {
                    if (!Schema::hasColumn('stock_movements', $column)) {
                        $callback($table);
                    }
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('stock_movements')) {
            Schema::dropIfExists('stock_movements');
        }
    }
};
