<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Domain\Settings\Models\User;
use App\Domain\Organization\Models\Company;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $companies = Company::all();

        if ($users->isEmpty() || $companies->isEmpty()) {
            return;
        }

        $events = [
            'created',
            'updated',
            'deleted',
            'restored',
            'login',
            'logout',
            'password_changed'
        ];

        $sample_logs = [];
        
        foreach ($companies as $company) {
            foreach ($users->take(3) as $user) {
                for ($i = 0; $i < 5; $i++) {
                    $event = $events[array_rand($events)];
                    $created_at = now()->subDays(rand(0, 30))->subHours(rand(0, 23));

                    $sample_logs[] = [
                        'user_id' => $user->id,
                        'company_id' => $company->id,
                        'auditable_type' => 'App\Domain\Settings\Models\User',
                        'auditable_id' => $user->id,
                        'event' => $event,
                        'old_values' => json_encode([
                            'name' => 'Old Name',
                            'email' => 'old@example.com'
                        ]),
                        'new_values' => json_encode([
                            'name' => 'New Name',
                            'email' => 'new@example.com'
                        ]),
                        'url' => 'https://ceosofts.test/users/' . $user->id,
                        'ip_address' => '127.0.0.1',
                        'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                        'created_at' => $created_at
                    ];
                }
            }
        }

        // Insert logs in chunks to avoid memory issues
        foreach (array_chunk($sample_logs, 100) as $logs) {
            DB::table('activity_logs')->insert($logs);
        }
    }
}
