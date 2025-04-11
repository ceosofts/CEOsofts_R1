<?php

namespace App\Models;

use App\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class WorkShift extends Model
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
        'name_en',
        'code',
        'description',
        'start_time',
        'end_time',
        'break_start',
        'break_end',
        'total_hours',
        'days_of_week',
        'is_night_shift',
        'is_active',
        'uuid',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'break_start' => 'datetime:H:i',
        'break_end' => 'datetime:H:i',
        'total_hours' => 'decimal:2',
        'days_of_week' => 'array',
        'is_night_shift' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($workShift) {
            if (!$workShift->uuid) {
                $workShift->uuid = (string) Str::uuid();
            }
            
            // คำนวณชั่วโมงทำงานรวม
            if ($workShift->start_time && $workShift->end_time) {
                $startTime = \Carbon\Carbon::parse($workShift->start_time);
                $endTime = \Carbon\Carbon::parse($workShift->end_time);
                
                // จัดการกรณีข้ามวัน
                if ($workShift->is_night_shift && $endTime->lt($startTime)) {
                    $endTime->addDay();
                }
                
                $totalMinutes = $endTime->diffInMinutes($startTime);
                
                // หักเวลาพัก
                if ($workShift->break_start && $workShift->break_end) {
                    $breakStart = \Carbon\Carbon::parse($workShift->break_start);
                    $breakEnd = \Carbon\Carbon::parse($workShift->break_end);
                    $breakMinutes = $breakEnd->diffInMinutes($breakStart);
                    $totalMinutes -= $breakMinutes;
                }
                
                $workShift->total_hours = round($totalMinutes / 60, 2);
            }
        });
        
        static::updating(function ($workShift) {
            // คำนวณชั่วโมงทำงานรวมเมื่อมีการอัปเดตเวลา
            if ($workShift->isDirty(['start_time', 'end_time', 'break_start', 'break_end', 'is_night_shift'])) {
                $startTime = \Carbon\Carbon::parse($workShift->start_time);
                $endTime = \Carbon\Carbon::parse($workShift->end_time);
                
                // จัดการกรณีข้ามวัน
                if ($workShift->is_night_shift && $endTime->lt($startTime)) {
                    $endTime->addDay();
                }
                
                $totalMinutes = $endTime->diffInMinutes($startTime);
                
                // หักเวลาพัก
                if ($workShift->break_start && $workShift->break_end) {
                    $breakStart = \Carbon\Carbon::parse($workShift->break_start);
                    $breakEnd = \Carbon\Carbon::parse($workShift->break_end);
                    $breakMinutes = $breakEnd->diffInMinutes($breakStart);
                    $totalMinutes -= $breakMinutes;
                }
                
                $workShift->total_hours = round($totalMinutes / 60, 2);
            }
        });
    }

    /**
     * Get the company that owns the work shift.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the employees for the work shift.
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_work_shifts')
            ->withPivot('effective_date', 'end_date', 'is_active')
            ->withTimestamps();
    }

    /**
     * Format days of week for display
     *
     * @return string
     */
    public function getFormattedDaysAttribute()
    {
        if (!$this->days_of_week) {
            return 'ทุกวัน';
        }
        
        $dayNames = [
            0 => 'อาทิตย์',
            1 => 'จันทร์',
            2 => 'อังคาร',
            3 => 'พุธ',
            4 => 'พฤหัสบดี',
            5 => 'ศุกร์',
            6 => 'เสาร์',
        ];
        
        $days = [];
        foreach ($this->days_of_week as $day) {
            $days[] = $dayNames[$day] ?? $day;
        }
        
        return implode(', ', $days);
    }

    /**
     * Scope a query to only include active work shifts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
