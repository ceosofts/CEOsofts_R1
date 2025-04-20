<?php

namespace App\Console\Commands;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TestOrderNumberGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:test-number-generator {company_id?} {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ทดสอบการสร้างเลขที่ใบสั่งขายอัตโนมัติ';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $companyId = $this->argument('company_id') ?: session('company_id', 1);
        $date = $this->argument('date') ?: Carbon::now()->format('Y-m-d');

        $this->info('ทดสอบการสร้างเลขที่ใบสั่งขายอัตโนมัติ');
        $this->info('บริษัท ID: ' . $companyId);
        $this->info('วันที่: ' . $date);

        $orderNumber = Order::generateOrderNumber($companyId, $date);

        $this->info('เลขที่ใบสั่งขายที่สร้างขึ้น: ' . $orderNumber);
        
        // แสดงเลขที่ใบสั่งขายล่าสุด
        $latestOrder = Order::where('company_id', $companyId)
            ->latest('created_at')
            ->first();

        if ($latestOrder) {
            $this->info('เลขที่ใบสั่งขายล่าสุดในระบบ: ' . $latestOrder->order_number);
        } else {
            $this->info('ไม่พบเลขที่ใบสั่งขายล่าสุดในระบบ');
        }
    }
}
