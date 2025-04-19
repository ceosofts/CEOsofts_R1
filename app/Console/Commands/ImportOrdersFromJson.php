<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportOrdersFromJson extends Command
{
    protected $signature = 'orders:import {file}';
    protected $description = 'นำเข้าข้อมูลใบสั่งขายจากไฟล์ JSON';

    public function handle()
    {
        $filePath = $this->argument('file');
        
        if (!File::exists($filePath)) {
            $this->error("ไม่พบไฟล์: {$filePath}");
            return 1;
        }
        
        $jsonContent = File::get($filePath);
        $ordersData = json_decode($jsonContent, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('ไฟล์ JSON ไม่ถูกต้อง: ' . json_last_error_msg());
            return 1;
        }
        
        $this->info("พบข้อมูลใบสั่งขาย " . count($ordersData) . " รายการ");
        
        $bar = $this->output->createProgressBar(count($ordersData));
        $bar->start();
        
        $imported = 0;
        $skipped = 0;
        
        foreach ($ordersData as $orderData) {
            // ตรวจสอบว่ามีใบสั่งขายนี้แล้วหรือไม่
            if (Order::where('order_number', $orderData['order_number'] ?? '')->exists()) {
                $skipped++;
                $bar->advance();
                continue;
            }
            
            try {
                Order::create($orderData);
                $imported++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("เกิดข้อผิดพลาดในการนำเข้าข้อมูล: " . $e->getMessage());
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        
        $this->info("นำเข้าข้อมูลใบสั่งขายสำเร็จ {$imported} รายการ, ข้ามไป {$skipped} รายการ");
        
        return 0;
    }
}
