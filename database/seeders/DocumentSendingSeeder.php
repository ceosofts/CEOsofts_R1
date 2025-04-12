<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\DocumentGeneration\Models\DocumentSending;
use App\Domain\DocumentGeneration\Models\GeneratedDocument;
use App\Domain\Organization\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema; // เพิ่ม import สำหรับ Schema facade

class DocumentSendingSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $this->createDocumentSendingsForCompany($company);
        }
    }

    private function createDocumentSendingsForCompany($company)
    {
        // ตรวจสอบว่า GeneratedDocument รองรับ SoftDeletes หรือไม่
        $hasSoftDeletes = Schema::hasColumn('generated_documents', 'deleted_at');

        // ปรับ query ตามการมีหรือไม่มี SoftDeletes
        if ($hasSoftDeletes) {
            $documents = GeneratedDocument::where('company_id', $company->id)->get();
        } else {
            // ถ้าไม่มี deleted_at ให้ใช้ DB query โดยตรงแทน Model
            $documents = DB::table('generated_documents')
                ->where('company_id', $company->id)
                ->get();
        }

        if ($documents->isEmpty()) {
            return;
        }

        foreach ($documents as $document) {
            if (rand(0, 1)) {  // สุ่มว่าควรสร้าง DocumentSending หรือไม่
                $emailTo = 'customer' . rand(1, 100) . '@example.com';

                $documentSending = [
                    'company_id' => $company->id,
                    'document_id' => $document->id,
                    'document_type' => $document->document_type ?? 'invoice',
                    'sent_at' => now()->subHours(rand(1, 48)),
                    'sent_by' => 1,
                    'sent_to' => $emailTo,
                    'sent_cc' => 'accounting@company.com',
                    'sent_bcc' => 'management@company.com',
                    'subject' => $this->getSubject($document->document_type ?? 'invoice', $document->id),
                    'body' => $this->getBody($document->document_type ?? 'invoice', $emailTo),
                    'status' => $this->getStatus(),
                    'result' => 'sent',
                    'tracking_id' => 'track_' . uniqid(),
                    'error_message' => null,
                    'metadata' => json_encode([
                        'ip_address' => '192.168.1.' . rand(1, 255),
                        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/96.0.4664.' . rand(1, 99),
                    ]),
                ];

                try {
                    DocumentSending::create($documentSending);
                } catch (\Exception $e) {
                    // จดบันทึกข้อผิดพลาด - แก้ไขจาก \Log::error เป็น Log::error
                    Log::error("Error creating document sending: " . $e->getMessage());
                }
            }
        }
    }

    private function getSubject($documentType, $documentId)
    {
        $subjects = [
            'invoice' => 'ใบแจ้งหนี้เลขที่ INV-' . str_pad($documentId, 5, '0', STR_PAD_LEFT),
            'quotation' => 'ใบเสนอราคาเลขที่ QT-' . str_pad($documentId, 5, '0', STR_PAD_LEFT),
            'receipt' => 'ใบเสร็จรับเงินเลขที่ RC-' . str_pad($documentId, 5, '0', STR_PAD_LEFT),
            'contract' => 'สัญญาเลขที่ CT-' . str_pad($documentId, 5, '0', STR_PAD_LEFT),
            'default' => 'เอกสารเลขที่ DOC-' . str_pad($documentId, 5, '0', STR_PAD_LEFT),
        ];

        return $subjects[$documentType] ?? $subjects['default'];
    }

    private function getBody($documentType, $emailTo)
    {
        $intro = "เรียน คุณลูกค้า ({$emailTo})\n\n";

        $bodies = [
            'invoice' => $intro . "บริษัทได้แนบใบแจ้งหนี้มาในอีเมล์นี้ โปรดชำระเงินภายในกำหนดเวลา",
            'quotation' => $intro . "บริษัทได้แนบใบเสนอราคามาในอีเมล์นี้ หากมีข้อสงสัยประการใดโปรดติดต่อพนักงานขาย",
            'receipt' => $intro . "บริษัทได้แนบใบเสร็จรับเงินมาในอีเมล์นี้ ขอบคุณที่ใช้บริการ",
            'contract' => $intro . "บริษัทได้แนบสัญญามาในอีเมล์นี้ โปรดตรวจสอบและลงนาม",
            'default' => $intro . "บริษัทได้แนบเอกสารมาในอีเมล์นี้ หากมีข้อสงสัยประการใดโปรดติดต่อกลับ",
        ];

        return $bodies[$documentType] ?? $bodies['default'];
    }

    private function getStatus()
    {
        $statuses = ['sent', 'delivered', 'opened', 'clicked'];
        $weights = [60, 20, 15, 5];

        return $this->getRandomWeighted($statuses, $weights);
    }

    private function getRandomWeighted($items, $weights)
    {
        $i = 0;
        $n = count($items);
        $total = array_sum($weights);
        $rand = mt_rand(1, $total);
        $w = $weights[0];

        while ($w < $rand && $i < $n - 1) {
            $i++;
            $w += $weights[$i];
        }

        return $items[$i];
    }
}
