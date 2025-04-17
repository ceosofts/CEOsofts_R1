<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Quotation;

class DebugQuotations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:quotations {limit=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'แสดงข้อมูลใบเสนอราคาเพื่อตรวจสอบ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->argument('limit');
        
        $this->info("กำลังตรวจสอบข้อมูลใบเสนอราคา {$limit} รายการแรก");
        
        try {
            // นับจำนวนทั้งหมด
            $totalCount = Quotation::count();
            $this->info("จำนวนใบเสนอราคาทั้งหมด: {$totalCount}");
            
            // ดึงข้อมูลพร้อม relationship
            $quotations = Quotation::with(['customer', 'items', 'creator', 'approver'])
                ->latest()
                ->limit($limit)
                ->get();
            
            $this->info("พบข้อมูล: {$quotations->count()} รายการ");
            
            // สรุปจำนวนตามสถานะ
            $draftCount = Quotation::where('status', 'draft')->count();
            $approvedCount = Quotation::where('status', 'approved')->count();
            $rejectedCount = Quotation::where('status', 'rejected')->count();
            
            $this->info("สรุปสถานะ:");
            $this->info("- ร่าง: {$draftCount} รายการ");
            $this->info("- อนุมัติแล้ว: {$approvedCount} รายการ");
            $this->info("- ปฏิเสธแล้ว: {$rejectedCount} รายการ");
            $this->info("- อื่นๆ: " . ($totalCount - ($draftCount + $approvedCount + $rejectedCount)) . " รายการ");
            
            // แสดงรายละเอียดแต่ละรายการ
            $this->info("\nรายละเอียดใบเสนอราคา:");
            
            foreach ($quotations as $index => $quotation) {
                $this->info("\nลำดับที่ " . ($index + 1));
                $this->info("- เลขที่: {$quotation->quotation_number}");
                $this->info("- วันที่: " . ($quotation->issue_date ? $quotation->issue_date->format('Y-m-d') : 'null'));
                $this->info("- บริษัท ID: {$quotation->company_id}");
                $this->info("- ลูกค้า: " . ($quotation->customer ? $quotation->customer->name : 'ไม่พบข้อมูลลูกค้า'));
                $this->info("- สถานะ: {$quotation->status}");
                $this->info("- ยอดรวม: " . number_format($quotation->total_amount, 2));
                $this->info("- จำนวนรายการสินค้า: " . $quotation->items->count());
                
                if ($quotation->creator) {
                    $this->info("- ผู้สร้าง: {$quotation->creator->name}");
                }
                
                if ($quotation->status == 'approved' && $quotation->approver) {
                    $this->info("- ผู้อนุมัติ: {$quotation->approver->name}");
                    $this->info("- อนุมัติเมื่อ: {$quotation->approved_at}");
                }
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error("เกิดข้อผิดพลาด: " . $e->getMessage());
            $this->error("ที่: " . $e->getFile() . ":" . $e->getLine());
            return 1;
        }
    }
}
