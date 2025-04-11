<?php

namespace App\Domain\Settings\Models;

use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduledEvent extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'title',
        'type',
        'event_type', // เพิ่มฟิลด์ event_type
        'description',
        'schedule',
        'frequency', // เพิ่มฟิลด์ frequency
        'start_date', // เพิ่มฟิลด์ start_date
        'action', // เพิ่ม action
        'timezone',
        'is_enabled',
        'last_run',
        'next_run',
        'event_data',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_enabled' => 'boolean',
        'last_run' => 'datetime',
        'next_run' => 'datetime',
        'start_date' => 'datetime', // เพิ่ม cast สำหรับ start_date
        'event_data' => 'json',
    ];

    /**
     * Get the company that owns this event.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who created this event.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include enabled events.
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /**
     * Scope a query to only include events of a specific type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include events that are due to run.
     */
    public function scopeDue($query)
    {
        return $query->where('is_enabled', true)
                    ->where('next_run', '<=', now());
    }

    /**
     * Mark this event as run.
     */
    public function markAsRun()
    {
        $this->last_run = now();
        $this->calculateNextRun();
        return $this->save();
    }

    /**
     * Enable this event.
     */
    public function enable()
    {
        $this->is_enabled = true;
        $this->calculateNextRun();
        return $this->save();
    }

    /**
     * Disable this event.
     */
    public function disable()
    {
        $this->is_enabled = false;
        return $this->save();
    }

    /**
     * Calculate the next run time based on the schedule.
     */
    public function calculateNextRun()
    {
        // If no schedule is defined, don't update next_run
        if (empty($this->schedule)) {
            return $this;
        }
        
        // Get the cron expression
        $cron = $this->getCronExpression();
        
        // Calculate next run
        if ($cron) {
            $timezone = $this->timezone ?? config('app.timezone');
            $this->next_run = $this->getCronNextRunDate($cron, $timezone);
        }
        
        return $this;
    }
    
    /**
     * Get the cron expression from the schedule.
     */
    protected function getCronExpression()
    {
        // Simple schedule type translation to cron expressions
        $schedules = [
            'every_minute' => '* * * * *',
            'every_five_minutes' => '*/5 * * * *',
            'every_ten_minutes' => '*/10 * * * *',
            'every_fifteen_minutes' => '*/15 * * * *',
            'every_thirty_minutes' => '*/30 * * * *',
            'hourly' => '0 * * * *',
            'daily' => '0 0 * * *',
            'daily_at_1am' => '0 1 * * *',
            'daily_at_2am' => '0 2 * * *',
            'weekly' => '0 0 * * 0',
            'monthly' => '0 0 1 * *',
            'yearly' => '0 0 1 1 *',
        ];
        
        // If this is a recognized simple schedule, return the cron expression
        if (isset($schedules[$this->schedule])) {
            return $schedules[$this->schedule];
        }
        
        // If this looks like a cron expression already, return it
        if (preg_match('/^(\*|[0-9\-\,\/]+)\s+(\*|[0-9\-\,\/]+)\s+(\*|[0-9\-\,\/]+)\s+(\*|[0-9\-\,\/]+)\s+(\*|[0-9\-\,\/]+)$/', $this->schedule)) {
            return $this->schedule;
        }
        
        // Otherwise, return null
        return null;
    }
    
    /**
     * Get the next run date for a cron expression.
     */
    protected function getCronNextRunDate($cron, $timezone)
    {
        try {
            $cronExpression = new \Cron\CronExpression($cron);
            return $cronExpression->getNextRunDate(now($timezone))->setTimezone(new \DateTimeZone($timezone));
        } catch (\Exception $e) {
            // Log the error
            \Log::error("Error calculating next run date for cron expression: " . $e->getMessage());
            
            // Return a default (1 day later)
            return now($timezone)->addDay();
        }
    }
    
    /**
     * Check if the event is overdue.
     */
    public function isOverdue()
    {
        return $this->is_enabled && $this->next_run && $this->next_run->isPast();
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Before saving, set values for required fields if not set
        static::saving(function ($model) {
            // Set frequency based on schedule if not set
            if (empty($model->frequency)) {
                $model->frequency = $model->getFrequencyFromSchedule();
            }

            // Set start_date if not set
            if (empty($model->start_date)) {
                $model->start_date = now();
            }

            // Set action if not set
            if (empty($model->action)) {
                $model->action = $model->getDefaultAction();
            }
        });
    }

    /**
     * Get frequency from schedule
     */
    public function getFrequencyFromSchedule()
    {
        $schedule = $this->schedule ?? 'daily';

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
     * Get default action based on event type
     */
    public function getDefaultAction()
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
        
        return $actions[$this->type] ?? 'execute_task';
    }
}
