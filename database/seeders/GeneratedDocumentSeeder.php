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
                    'document_template_id' => $template->id,
                    'template_id' => $template->id, // ยังคงใส่ค่า template_id สำหรับความเข้ากันได้
                    'documentable_type' => 'App\\Domain\\' . ucfirst($template->type) . '\\Models\\' . ucfirst($template->type),
                    'documentable_id' => rand(1000, 9999),
                    'filename' => strtoupper($template->type) . '-' . date('Ymd') . '-' . rand(100, 999) . '.pdf',
                    'display_name' => 'เอกสาร ' . ucfirst($template->type) . ' #' . rand(1000, 9999),
                    'file_path' => 'documents/' . $company->id . '/' . date('Y/m'),
                    'file_type' => 'pdf',
                    'status' => 'generated',
                    'created_by' => 1,
                    'metadata' => json_encode([
                        'generated_at' => now()->toIso8601String(),
                        'language' => 'th',
                        'version' => '1.0'
                    ])
                ],
                [
                    'company_id' => $company->id,
                    'document_template_id' => $template->id,
                    'template_id' => $template->id, // ยังคงใส่ค่า template_id สำหรับความเข้ากันได้
                    'documentable_type' => 'App\\Domain\\' . ucfirst($template->type) . '\\Models\\' . ucfirst($template->type),
                    'documentable_id' => rand(1000, 9999),
                    'filename' => strtoupper($template->type) . '-' . date('Ymd') . '-' . rand(100, 999) . '.pdf',
                    'display_name' => 'เอกสาร ' . ucfirst($template->type) . ' #' . rand(1000, 9999),
                    'file_path' => 'documents/' . $company->id . '/' . date('Y/m'),
                    'file_type' => 'pdf',
                    'status' => 'sent',
                    'created_by' => 1,
                    'metadata' => json_encode([
                        'generated_at' => now()->subHours(3)->toIso8601String(),
                        'sent_at' => now()->subHour()->toIso8601String(),
                        'sent_to' => 'customer@example.com',
                        'sent_by' => 'User Admin',
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
