<?php

namespace Database\Seeders;

use App\Domain\Settings\Models\FileAttachment;
use App\Domain\Organization\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class FileAttachmentSeeder extends Seeder
{
    public function run(): void
    {
        // ตรวจสอบว่ามีตาราง file_attachments หรือไม่
        if (!Schema::hasTable('file_attachments')) {
            $this->command->error('Table file_attachments not found. Please run migrations first.');
            return;
        }
        
        $companies = Company::all();
        
        foreach ($companies as $company) {
            $this->createAttachmentsForCompany($company->id);
        }
    }
    
    private function createAttachmentsForCompany($companyId)
    {
        try {
            // สร้างไดเรคทอรี่สำหรับเก็บไฟล์ (ถ้ายังไม่มี)
            $directory = "company_{$companyId}/files";
            Storage::disk('public')->makeDirectory($directory);
            
            // สร้างไฟล์ตัวอย่าง
            $this->createSampleTextFile($directory, $companyId);
            
            // ข้อมูลไฟล์แนบตัวอย่าง
            $attachments = [
                [
                    'company_id' => $companyId,
                    'attachable_type' => null,
                    'attachable_id' => null,
                    'name' => 'readme.txt',
                    'original_name' => 'readme.txt',
                    'disk' => 'public',
                    'path' => "{$directory}/readme.txt",
                    'mime_type' => 'text/plain',
                    'size' => 1024,
                    'created_by' => 1,
                    'metadata' => json_encode([
                        'description' => 'Sample text file',
                        'tags' => ['sample', 'text', 'readme']
                    ])
                ]
            ];
            
            foreach ($attachments as $attachment) {
                try {
                    // ใช้ DB query โดยตรงแทน model เพื่อหลีกเลี่ยงปัญหาเกี่ยวกับ SoftDeletes
                    $exists = DB::table('file_attachments')
                        ->where('company_id', $companyId)
                        ->where('path', $attachment['path'])
                        ->exists();
                    
                    if (!$exists) {
                        $attachment['created_at'] = now();
                        $attachment['updated_at'] = now();
                        
                        DB::table('file_attachments')->insert($attachment);
                        $this->command->info("Created file attachment: {$attachment['name']}");
                    } else {
                        $this->command->info("File attachment already exists: {$attachment['name']}");
                    }
                } catch (\Exception $e) {
                    $this->command->error("Error creating file attachment {$attachment['name']}: " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            $this->command->error("Error in createAttachmentsForCompany: " . $e->getMessage());
        }
    }
    
    /**
     * สร้างไฟล์ตัวอย่าง readme.txt
     */
    private function createSampleTextFile($directory, $companyId)
    {
        $content = <<<EOT
        CEOsofts Example File
        =====================
        
        This is a sample text file created for company ID: {$companyId}.
        
        It serves as an example of file attachment functionality.
        
        You can upload various files such as:
        - Documents (PDF, DOC, XLS, etc.)
        - Images (JPG, PNG, etc.)
        - Other file types
        
        These files can be attached to various entities in the system.
        
        Created: {$this->getCurrentDateTime()}
        EOT;
        
        Storage::disk('public')->put("{$directory}/readme.txt", $content);
    }
    
    /**
     * Get current date time formatted string
     */
    private function getCurrentDateTime()
    {
        return now()->format('Y-m-d H:i:s');
    }
}
