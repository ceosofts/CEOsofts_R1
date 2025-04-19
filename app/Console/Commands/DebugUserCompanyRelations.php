<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DebugUserCompanyRelations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:user-company';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ตรวจสอบและแสดงความสัมพันธ์ระหว่างผู้ใช้งานและบริษัท';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('เริ่มตรวจสอบความสัมพันธ์ระหว่างผู้ใช้งานและบริษัท');

        // ตรวจสอบจำนวนผู้ใช้งานทั้งหมด
        $totalUsers = User::count();
        $this->info("จำนวนผู้ใช้งานทั้งหมด: {$totalUsers}");

        // ตรวจสอบจำนวนบริษัททั้งหมด
        $totalCompanies = Company::count();
        $this->info("จำนวนบริษัททั้งหมด: {$totalCompanies}");

        // ตรวจสอบจำนวนความสัมพันธ์ในตาราง company_user
        $totalRelations = DB::table('company_user')->count();
        $this->info("จำนวนความสัมพันธ์ในตาราง company_user: {$totalRelations}");

        // แสดงข้อมูลผู้ใช้และบริษัทที่เชื่อมโยง
        $this->info("\nรายละเอียดความสัมพันธ์:");
        $this->table(
            ['User ID', 'User Email', 'Company ID', 'Company Name'],
            $this->getUserCompanyRelations()
        );

        // ตรวจสอบผู้ใช้ที่ไม่มีบริษัท
        $usersWithoutCompany = User::doesntHave('companies')->get();
        if ($usersWithoutCompany->count() > 0) {
            $this->warn("\nผู้ใช้ที่ไม่มีบริษัท:");
            foreach ($usersWithoutCompany as $user) {
                $this->line("- ID: {$user->id}, Email: {$user->email}");
            }
            
            // แนะนำวิธีแก้ไข
            $this->info("\nคำแนะนำในการแก้ไข:");
            $this->line("รันคำสั่งต่อไปนี้เพื่อเชื่อมโยงผู้ใช้กับบริษัท:");
            $this->line("php artisan tinker");
            $this->line("\\App\\Models\\User::find(USER_ID)->companies()->attach(COMPANY_ID);");
        } else {
            $this->info("\nทุกผู้ใช้มีการเชื่อมโยงกับบริษัทแล้ว");
        }

        return Command::SUCCESS;
    }

    /**
     * Get user-company relations.
     *
     * @return array
     */
    private function getUserCompanyRelations()
    {
        $relations = [];

        $users = User::with('companies')->get();
        foreach ($users as $user) {
            if ($user->companies->isEmpty()) {
                $relations[] = [
                    'User ID' => $user->id,
                    'User Email' => $user->email,
                    'Company ID' => 'ไม่มี',
                    'Company Name' => 'ไม่มี'
                ];
            } else {
                foreach ($user->companies as $company) {
                    $relations[] = [
                        'User ID' => $user->id,
                        'User Email' => $user->email,
                        'Company ID' => $company->id,
                        'Company Name' => $company->name
                    ];
                }
            }
        }

        return $relations;
    }
}
