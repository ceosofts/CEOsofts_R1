<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\DocumentGeneration\Models\DocumentTemplate;
use App\Domain\Organization\Models\Company;

class DocumentTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();
        
        foreach ($companies as $company) {
            $this->createTemplatesForCompany($company->id);
        }
    }

    private function createTemplatesForCompany($companyId)
    {
        $templates = [
            [
                'company_id' => $companyId,
                'name' => 'ใบเสนอราคามาตรฐาน',
                'type' => 'quotation',
                'layout' => json_encode([
                    'sections' => [
                        'header' => true,
                        'company_info' => true,
                        'customer_info' => true,
                        'items' => true,
                        'summary' => true,
                        'terms' => true,
                        'footer' => true
                    ],
                    'columns' => [
                        ['id' => 'no', 'label' => 'ลำดับ', 'width' => '5%'],
                        ['id' => 'code', 'label' => 'รหัส', 'width' => '15%'],
                        ['id' => 'description', 'label' => 'รายการ', 'width' => '40%'],
                        ['id' => 'quantity', 'label' => 'จำนวน', 'width' => '10%'],
                        ['id' => 'unit', 'label' => 'หน่วย', 'width' => '10%'],
                        ['id' => 'price', 'label' => 'ราคา', 'width' => '10%'],
                        ['id' => 'amount', 'label' => 'รวมเงิน', 'width' => '10%'],
                    ]
                ]),
                'header' => json_encode([
                    'show_logo' => true,
                    'logo_position' => 'left',
                    'show_company_info' => true
                ]),
                'footer' => json_encode([
                    'show_signature' => true,
                    'show_page_number' => true
                ]),
                'css' => 'body { font-family: "Sarabun", sans-serif; }',
                'orientation' => 'portrait',
                'paper_size' => 'a4',
                'is_default' => true,
                'is_active' => true,
                'created_by' => 1,
                'metadata' => json_encode([
                    'version' => '1.0',
                    'category' => 'sales'
                ])
            ],
            [
                'company_id' => $companyId,
                'name' => 'ใบแจ้งหนี้มาตรฐาน',
                'type' => 'invoice',
                'layout' => json_encode([
                    'sections' => [
                        'header' => true,
                        'company_info' => true,
                        'customer_info' => true,
                        'items' => true,
                        'summary' => true,
                        'payment_info' => true,
                        'footer' => true
                    ]
                ]),
                'header' => json_encode([
                    'show_logo' => true,
                    'logo_position' => 'left',
                    'show_company_info' => true
                ]),
                'footer' => json_encode([
                    'show_signature' => true,
                    'show_page_number' => true,
                    'show_terms' => true
                ]),
                'css' => 'body { font-family: "Sarabun", sans-serif; }',
                'orientation' => 'portrait',
                'paper_size' => 'a4',
                'is_default' => true,
                'is_active' => true,
                'created_by' => 1,
                'metadata' => json_encode([
                    'version' => '1.0',
                    'category' => 'finance'
                ])
            ]
        ];

        foreach ($templates as $template) {
            DocumentTemplate::firstOrCreate(
                [
                    'company_id' => $companyId,
                    'name' => $template['name'],
                    'type' => $template['type']
                ],
                $template
            );
        }
    }
}
