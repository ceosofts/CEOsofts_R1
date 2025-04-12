<?php

namespace Database\Seeders;

use App\Domain\Settings\Models\ScheduledEvent;
use App\Domain\Organization\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class ScheduledEventSeeder extends Seeder
{
    public function run(): void
    {
        // ตรวจสอบว่าตาราง scheduled_events มีอยู่จริง
        if (!Schema::hasTable('scheduled_events')) {
            $this->command->error('Table scheduled_events does not exist.');
            return;
        }

        // รันการเพิ่มคอลัมน์โดยตรงโดยไม่พึ่ง migration
        $this->ensureRequiredColumns();

        // ตรวจสอบว่าตารางมีคอลัมน์ company_id หรือไม่
        if (!Schema::hasColumn('scheduled_events', 'company_id')) {
            $this->command->warn('Column company_id does not exist in scheduled_events table.');
            $this->command->info('Adding company_id column to scheduled_events table.');

            try {
                Schema::table('scheduled_events', function ($table) {
                    $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
                });
                $this->command->info("Added 'company_id' column to scheduled_events table.");
            } catch (\Exception $e) {
                $this->command->error("Failed to add company_id column: " . $e->getMessage());
                $this->command->info("Creating basic scheduled events without company_id...");

                // สร้าง events โดยไม่มี company_id
                $this->createBasicEvents();
                return;
            }
        }

        // ดำเนินการสร้าง events ตามปกติ หลังจากมั่นใจว่ามี company_id แล้ว
        $companies = Company::all();
        foreach ($companies as $company) {
            $this->createEventsForCompany($company->id);
        }
    }

    /**
     * สร้าง events โดยไม่มี company_id (fallback)
     */
    private function createBasicEvents()
    {
        $now = now();

        $events = [
            [
                'name' => 'System Daily Backup',
                'title' => 'สำรองข้อมูลระบบประจำวัน',
                'event_type' => 'system',
                'frequency' => 'daily',
                'start_date' => $now,
                'action' => 'backup_system',
            ],
            [
                'name' => 'Database Cleanup',
                'title' => 'ล้างข้อมูลชั่วคราวในฐานข้อมูล',
                'event_type' => 'maintenance',
                'frequency' => 'weekly',
                'start_date' => $now,
                'action' => 'cleanup_database',
            ]
        ];

        $columns = Schema::getColumnListing('scheduled_events');

        foreach ($events as $event) {
            try {
                // สร้างข้อมูล array สำหรับ insert
                $data = [
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // เพิ่มข้อมูลเฉพาะคอลัมน์ที่มีอยู่จริง
                foreach ($event as $key => $value) {
                    if (in_array($key, $columns)) {
                        $data[$key] = $value;
                    }
                }

                DB::table('scheduled_events')->insert($data);
                $this->command->info("Created basic scheduled event: {$event['name']}");
            } catch (\Exception $e) {
                $this->command->error("Error with basic event {$event['name']}: " . $e->getMessage());
            }
        }
    }

    /**
     * เพิ่มคอลัมน์ที่จำเป็นหากยังไม่มี
     */
    private function ensureRequiredColumns()
    {
        if (!Schema::hasColumn('scheduled_events', 'type')) {
            Schema::table('scheduled_events', function ($table) {
                $table->string('type')->default('default');
            });
            $this->command->info("Added 'type' column to scheduled_events table.");
        }

        if (!Schema::hasColumn('scheduled_events', 'schedule')) {
            Schema::table('scheduled_events', function ($table) {
                $table->string('schedule')->default('daily');
            });
            $this->command->info("Added 'schedule' column to scheduled_events table.");
        }

        if (!Schema::hasColumn('scheduled_events', 'title')) {
            Schema::table('scheduled_events', function ($table) {
                $table->string('title')->default('');
            });
            $this->command->info("Added 'title' column to scheduled_events table.");
        }

        if (!Schema::hasColumn('scheduled_events', 'event_type')) {
            Schema::table('scheduled_events', function ($table) {
                $table->string('event_type')->default('general');
            });
            $this->command->info("Added 'event_type' column to scheduled_events table.");
        }

        if (!Schema::hasColumn('scheduled_events', 'frequency')) {
            Schema::table('scheduled_events', function ($table) {
                $table->string('frequency')->default('daily');
            });
            $this->command->info("Added 'frequency' column to scheduled_events table.");
        }

        if (!Schema::hasColumn('scheduled_events', 'start_date')) {
            Schema::table('scheduled_events', function ($table) {
                $table->timestamp('start_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            });
            $this->command->info("Added 'start_date' column to scheduled_events table.");
        }

        if (!Schema::hasColumn('scheduled_events', 'action')) {
            Schema::table('scheduled_events', function ($table) {
                $table->string('action')->default('send_email');
            });
            $this->command->info("Added 'action' column to scheduled_events table.");
        }
    }

    private function createEventsForCompany($companyId)
    {
        // ตรวจสอบว่ามีคอลัมน์ที่จำเป็นครบถ้วนหรือไม่
        $columns = Schema::getColumnListing('scheduled_events');

        // ตรวจสอบว่ามี company_id หรือไม่
        if (!in_array('company_id', $columns)) {
            $this->command->warn("Column 'company_id' does not exist. Skipping company-specific events.");
            return;
        }

        $hasEventData = in_array('event_data', $columns);
        $hasTitle = in_array('title', $columns);
        $hasEventType = in_array('event_type', $columns);
        $hasFrequency = in_array('frequency', $columns);
        $hasStartDate = in_array('start_date', $columns);
        $hasAction = in_array('action', $columns);

        $now = now();

        $events = [
            [
                'name' => 'Daily Invoice Reminder',
                'title' => 'แจ้งเตือนใบแจ้งหนี้รายวัน',
                'type' => 'email_reminder',
                'event_type' => 'notification',
                'description' => 'แจ้งเตือนลูกค้าที่มีใบแจ้งหนี้ใกล้ครบกำหนด',
                'schedule' => 'daily_at_1am',
                'frequency' => 'daily',
                'start_date' => $now, // เพิ่ม start_date
                'action' => 'send_email', // เพิ่ม action
                'timezone' => 'Asia/Bangkok',
                'is_enabled' => true,
                'event_data' => $hasEventData ? json_encode([
                    'days_before_due' => 3,
                    'email_template' => 'invoice_reminder',
                    'include_attachment' => true
                ]) : null
            ],
            [
                'name' => 'Monthly Sales Report',
                'title' => 'รายงานยอดขายรายเดือน',
                'type' => 'report_generation',
                'event_type' => 'report',
                'description' => 'สร้างรายงานยอดขายประจำเดือน',
                'schedule' => 'monthly',
                'frequency' => 'monthly',
                'start_date' => $now, // เพิ่ม start_date
                'action' => 'generate_report', // เพิ่ม action
                'timezone' => 'Asia/Bangkok',
                'is_enabled' => true,
                'event_data' => $hasEventData ? json_encode([
                    'report_type' => 'sales',
                    'format' => 'pdf',
                    'recipients' => [
                        'management@company.com',
                        'accounting@company.com'
                    ]
                ]) : null
            ],
            [
                'name' => 'Weekly Inventory Check',
                'title' => 'ตรวจสอบสินค้าคงคลังประจำสัปดาห์',
                'type' => 'inventory_check',
                'event_type' => 'system',
                'description' => 'ตรวจสอบระดับสินค้าคงเหลือและแจ้งเตือนถ้ามีสินค้าใกล้หมด',
                'schedule' => 'weekly',
                'frequency' => 'weekly',
                'start_date' => $now, // เพิ่ม start_date
                'action' => 'check_inventory', // เพิ่ม action
                'timezone' => 'Asia/Bangkok',
                'is_enabled' => true,
                'event_data' => $hasEventData ? json_encode([
                    'minimum_threshold' => 10,
                    'notify' => [
                        'inventory@company.com',
                        'purchasing@company.com'
                    ]
                ]) : null
            ]
        ];

        foreach ($events as $event) {
            try {
                // สร้างข้อมูล array สำหรับ insert
                $data = [
                    'company_id' => $companyId,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                // เพิ่มข้อมูลเฉพาะคอลัมน์ที่มีอยู่จริง
                foreach ($event as $key => $value) {
                    if (in_array($key, $columns)) {
                        $data[$key] = $value;
                    }
                }

                // ถ้ามีคอลัมน์ title แต่ไม่มีข้อมูล ให้ใช้ name แทน
                if ($hasTitle && !isset($data['title'])) {
                    $data['title'] = $data['name'] ?? '';
                }

                // ถ้ามีคอลัมน์ event_type แต่ไม่มีข้อมูล ให้ใช้ type แทน
                if ($hasEventType && !isset($data['event_type'])) {
                    $data['event_type'] = $data['type'] ?? 'general';
                }

                // ถ้ามีคอลัมน์ frequency แต่ไม่มีข้อมูล ให้กำหนดจาก schedule
                if ($hasFrequency && !isset($data['frequency'])) {
                    $data['frequency'] = $this->getFrequencyFromSchedule($event['schedule'] ?? 'daily');
                }

                // ถ้ามีคอลัมน์ start_date แต่ไม่มีข้อมูล ให้ใช้เวลาปัจจุบัน
                if ($hasStartDate && !isset($data['start_date'])) {
                    $data['start_date'] = now();
                }

                // ถ้ามีคอลัมน์ action แต่ไม่มีข้อมูล ให้กำหนดค่า default
                if ($hasAction && !isset($data['action'])) {
                    $data['action'] = $this->getDefaultAction($event['type'] ?? '');
                }

                // เพิ่มคอลัมน์ next_run ถ้ามี
                if (in_array('next_run', $columns)) {
                    $data['next_run'] = $this->calculateNextRun($event['schedule'], $event['timezone'] ?? 'Asia/Bangkok');
                }

                // เพิ่มคอลัมน์ created_by ถ้ามี
                if (in_array('created_by', $columns)) {
                    $data['created_by'] = 1;
                }

                // ตรวจสอบว่ามีข้อมูลนี้อยู่แล้วหรือไม่
                $exists = DB::table('scheduled_events')
                    ->where('company_id', $companyId)
                    ->where('name', $event['name'])
                    ->exists();

                if (!$exists) {
                    DB::table('scheduled_events')->insert($data);
                    $this->command->info("Created scheduled event: {$event['name']}");
                } else {
                    $this->command->info("Scheduled event already exists: {$event['name']}");
                }
            } catch (\Exception $e) {
                $this->command->error("Error with scheduled event {$event['name']}: " . $e->getMessage());
                $this->command->error("SQL data: " . json_encode($data));
            }
        }
    }

    /**
     * กำหนด action ตาม type
     */
    private function getDefaultAction($type)
    {
        $actions = [
            'email_reminder' => 'send_email',
            'report_generation' => 'generate_report',
            'inventory_check' => 'check_inventory',
            'notification' => 'send_notification',
            'backup' => 'create_backup',
            'cleanup' => 'cleanup_data',
            'invoice' => 'generate_invoice',
        ];

        return $actions[$type] ?? 'execute_task';
    }

    /**
     * กำหนด frequency จาก schedule
     */
    private function getFrequencyFromSchedule($schedule)
    {
        if (str_contains($schedule, 'minute')) {
            return 'minute';
        } elseif (str_contains($schedule, 'hour')) {
            return 'hourly';
        } elseif (str_contains($schedule, 'daily')) {
            return 'daily';
        } elseif (str_contains($schedule, 'weekly')) {
            return 'weekly';
        } elseif (str_contains($schedule, 'monthly')) {
            return 'monthly';
        } elseif (str_contains($schedule, 'yearly')) {
            return 'yearly';
        }

        return 'daily'; // ค่าเริ่มต้น
    }

    /**
     * คำนวณวันและเวลาที่จะทำงานครั้งถัดไปตาม schedule ที่กำหนด
     */
    private function calculateNextRun($schedule, $timezone = 'UTC')
    {
        $now = now($timezone);

        $schedules = [
            'every_minute' => $now->copy()->addMinute(),
            'every_five_minutes' => $now->copy()->addMinutes(5),
            'every_ten_minutes' => $now->copy()->addMinutes(10),
            'every_fifteen_minutes' => $now->copy()->addMinutes(15),
            'every_thirty_minutes' => $now->copy()->addMinutes(30),
            'hourly' => $now->copy()->addHour(),
            'daily' => $now->copy()->addDay()->startOfDay(),
            'daily_at_1am' => $now->copy()->addDay()->startOfDay()->addHour(1),
            'daily_at_2am' => $now->copy()->addDay()->startOfDay()->addHour(2),
            'weekly' => $now->copy()->addWeek()->startOfWeek(),
            'monthly' => $now->copy()->addMonth()->startOfMonth(),
            'yearly' => $now->copy()->addYear()->startOfYear(),
        ];

        return $schedules[$schedule] ?? $now->copy()->addDay();
    }
}
