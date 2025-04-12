<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\FileStorage\Models\FileAttachment;
use App\Domain\Organization\Models\Company;
use Illuminate\Support\Facades\Log;

class FileAttachmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $this->createFileAttachmentsForCompany($company);
        }
    }

    private function createFileAttachmentsForCompany($company)
    {
        $attachments = [
            [
                'company_id' => $company->id,
                'attachable_type' => 'App\\Domain\\Organization\\Models\\Company',
                'attachable_id' => $company->id,
                'filename' => 'readme.txt', // แก้ไขจาก name เป็น filename
                'original_filename' => 'readme.txt', // แก้ไขจาก original_name เป็น original_filename
                'disk' => 'public',
                'file_path' => 'company_' . $company->id . '/files/readme.txt', // แก้ไขจาก path เป็น file_path
                'mime_type' => 'text/plain',
                'file_size' => 1024, // แก้ไขจาก size เป็น file_size
                'created_by' => 1,
                'metadata' => json_encode([
                    'description' => 'Sample text file',
                    'tags' => ['sample', 'text', 'readme']
                ])
            ]
        ];

        foreach ($attachments as $attachment) {
            try {
                FileAttachment::create($attachment);
            } catch (\Exception $e) {
                $this->command->error("Error creating file attachment {$attachment['filename']}: " . $e->getMessage());
            }
        }
    }
}
