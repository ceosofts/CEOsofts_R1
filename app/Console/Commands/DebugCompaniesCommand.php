<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DebugCompaniesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:companies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ตรวจสอบข้อมูลในตาราง companies และแสดงข้อมูลสำหรับดีบั๊ก';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Debug Companies Information ===');

        // ตรวจสอบว่าตาราง companies มีอยู่หรือไม่
        if (!Schema::hasTable('companies')) {
            $this->error('ไม่พบตาราง companies ในฐานข้อมูล');
            return 1;
        }

        // แสดงโครงสร้างของตาราง companies
        $columns = Schema::getColumnListing('companies');
        $this->info('Columns in companies table: ' . implode(', ', $columns));

        // ตรวจสอบจำนวนข้อมูลในตาราง
        $totalCompanies = Company::count();
        $this->info("จำนวนบริษัททั้งหมด: {$totalCompanies}");

        // แสดงข้อมูลบริษัททั้งหมด
        if ($totalCompanies > 0) {
            $this->info("รายการข้อมูลบริษัท:");

            $companies = Company::all();
            foreach ($companies as $index => $company) {
                // แก้ไขบรรทัดที่มีปัญหา - ใช้การต่อสตริงแทนการใช้ตัวแปรในสตริง
                $this->info("--- Company #" . ($index + 1) . " ---");
                $this->info("ID: {$company->id}");
                $this->info("Name: {$company->name}");
                $this->info("Email: {$company->email}");
                $this->info("Status: " . ($company->status ?? 'N/A'));
                $this->info("Is Active: " . ($company->is_active ? 'Yes' : 'No'));
                $this->info("Created At: {$company->created_at}");
                $this->line('');
            }
        } else {
            $this->warn("ไม่พบข้อมูลบริษัทในฐานข้อมูล");

            // เช็คข้อมูลโดยตรงจาก DB query
            $rawCompanies = DB::table('companies')->get();
            if ($rawCompanies->count() > 0) {
                $this->warn("แต่พบข้อมูลเมื่อใช้ DB query โดยตรง: {$rawCompanies->count()} รายการ");
                $this->warn("อาจเกิดปัญหาที่ Model ไม่สามารถดึงข้อมูลได้");
            }
        }

        // ตรวจสอบ class Company
        $this->info('=== Model Information ===');
        $this->info('Model class: ' . get_class(new Company()));
        $this->info('Table name: ' . (new Company())->getTable());
        $this->info('Key name: ' . (new Company())->getKeyName());

        return 0;
    }
}
