<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\DocumentGeneration\Models\GeneratedDocument;
use App\Domain\Organization\Models\Company;
use App\Domain\DocumentGeneration\Models\DocumentTemplate;

class GeneratedDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();
        
        foreach ($companies as $company) {
            $this->createGeneratedDocumentsForCompany($company);
        }
    }

    private function createGeneratedDocumentsForCompany($company)
    {
        $templates = DocumentTemplate::where('company_id', $company->id)->get();
        
        if ($templates->isEmpty()) {
            return;
        }

        foreach ($templates as $template) {
            $documents = [
                [
                    'company_id' => $company->id,
                    'template_id' => $template->id,
                    'document_type' => $template->type,
                    'document_id' => rand(1000, 9999),
                    'filename' => strtoupper($template->type) . '-' . date('Ymd') . '-' . rand(100, 999) . '.pdf',
                    'disk' => 'local',
                    'path' => 'documents/' . $company->id . '/' . date('Y/m'),
                    'mime_type' => 'application/pdf',
                    'file_size' => rand(100000, 500000),
                    'is_signed' => false,
                    'is_sent' => false,
                    'created_by' => 1,
                    'metadata' => json_encode([
                        'generated_at' => now()->toIso8601String(),
                        'language' => 'th',
                        'version' => '1.0'
                    ])
                ],
                [
                    'company_id' => $company->id,
                    'template_id' => $template->id,
                    'document_type' => $template->type,
                    'document_id' => rand(1000, 9999),
                    'filename' => strtoupper($template->type) . '-' . date('Ymd') . '-' . rand(100, 999) . '.pdf',
                    'disk' => 'local',
                    'path' => 'documents/' . $company->id . '/' . date('Y/m'),
                    'mime_type' => 'application/pdf',
                    'file_size' => rand(100000, 500000),
                    'is_signed' => true,
                    'signature_data' => json_encode([
                        'signed_by' => 'John Doe',
                        'signed_at' => now()->subHours(2)->toIso8601String(),
                        'signature_type' => 'digital'
                    ]),
                    'is_sent' => true,
                    'sent_at' => now()->subHour(),
                    'sent_to' => 'customer@example.com',
                    'sent_by' => 1,
                    'created_by' => 1,
                    'metadata' => json_encode([
                        'generated_at' => now()->subHours(3)->toIso8601String(),
                        'language' => 'th',
                        'version' => '1.0'
                    ])
                ]
            ];

            foreach ($documents as $document) {
                GeneratedDocument::create($document);
            }
        }
    }
}
