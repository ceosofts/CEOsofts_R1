<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ตัวอย่างข้อมูลในตาราง jobs
        DB::table('jobs')->insert([
            [
                'queue' => 'default',
                'payload' => json_encode(['job' => 'App\Jobs\ExampleJob', 'data' => ['example' => 'data']]),
                'attempts' => 0,
                'reserved_at' => null,
                'available_at' => now()->timestamp,
                'created_at' => now()->timestamp,
            ],
        ]);

        // ตัวอย่างข้อมูลในตาราง job_batches
        DB::table('job_batches')->insert([
            [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'name' => 'Example Batch Job',
                'total_jobs' => 10,
                'pending_jobs' => 5,
                'failed_jobs' => 2,
                'failed_job_ids' => json_encode([1, 2]),
                'options' => json_encode(['notify' => 'admin@example.com']),
                'cancelled_at' => null,
                'created_at' => now()->timestamp,
                'finished_at' => null,
            ],
        ]);

        // ตัวอย่างข้อมูลในตาราง failed_jobs
        DB::table('failed_jobs')->insert([
            [
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'connection' => 'database',
                'queue' => 'default',
                'payload' => json_encode(['job' => 'App\Jobs\FailedJob', 'data' => ['example' => 'data']]),
                'exception' => 'Example exception message',
                'failed_at' => now(),
            ],
        ]);
    }
}
