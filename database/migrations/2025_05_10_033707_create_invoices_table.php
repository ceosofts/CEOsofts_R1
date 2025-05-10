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
        // ปรับเปลี่ยนจาก create เป็น table เพื่อปรับปรุงตารางที่มีอยู่แล้ว
        Schema::table('invoices', function (Blueprint $table) {
            // เพิ่มฟิลด์ใหม่เข้าไปในตาราง
            if (!Schema::hasColumn('invoices', 'currency')) {
                $table->string('currency', 10)->default('THB')->after('status');
            }
            
            if (!Schema::hasColumn('invoices', 'exchange_rate')) {
                $table->decimal('exchange_rate', 10, 2)->default(1.00)->after('currency');
            }
            
            if (!Schema::hasColumn('invoices', 'tax_inclusive')) {
                $table->boolean('tax_inclusive')->default(false)->after('discount_amount');
            }
            
            if (!Schema::hasColumn('invoices', 'total_discount')) {
                $table->decimal('total_discount', 15, 2)->default(0)->after('subtotal');
            }
            
            if (!Schema::hasColumn('invoices', 'total')) {
                $table->decimal('total', 15, 2)->default(0)->after('total_discount');
            }
            
            if (!Schema::hasColumn('invoices', 'amount_paid')) {
                $table->decimal('amount_paid', 15, 2)->default(0)->after('total');
            }
            
            if (!Schema::hasColumn('invoices', 'amount_due')) {
                $table->decimal('amount_due', 15, 2)->default(0)->after('amount_paid');
            }
            
            if (!Schema::hasColumn('invoices', 'terms')) {
                $table->text('terms')->nullable()->after('notes');
            }
            
            if (!Schema::hasColumn('invoices', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete()->after('created_by');
            }
            
            if (!Schema::hasColumn('invoices', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            
            if (!Schema::hasColumn('invoices', 'cancelled_by')) {
                $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete()->after('approved_at');
            }
            
            if (!Schema::hasColumn('invoices', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('cancelled_by');
            }
            
            if (!Schema::hasColumn('invoices', 'metadata')) {
                $table->json('metadata')->nullable()->after('cancelled_at');
            }
            
            // อัพเดทค่า enum ของฟิลด์ status หากต้องการเพิ่มเติมตัวเลือก
            // (แต่อาจจะไม่สามารถทำได้โดยตรงใน SQLite ต้องใช้วิธีอื่น)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
