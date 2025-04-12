<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveType extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'name',
        'code',
        'description',
        'annual_allowance',
        'days_advance_notice',
        'requires_approval',
        'requires_attachment',
        'is_paid',
        'is_active',
        'color',
        'icon',
        'metadata',
        // คอลัมน์เพิ่มเติมจากการรวม migrations
        'max_consecutive_days',
        'allow_half_day',
        'min_notice_days',
        'approval_levels',
        'requires_documents',
        'carry_forward_days',
        'is_compensated_on_termination',
        'can_take_advance',
        'max_annual_carryover',
        'carryover_expiration_months',
        'accrual_type',
        'accrual_rate',
        'accrual_milestone_months',
        'policy_text',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'annual_allowance' => 'decimal:2',
        'accrual_rate' => 'decimal:4',
        'is_paid' => 'boolean',
        'is_active' => 'boolean',
        'requires_approval' => 'boolean',
        'requires_attachment' => 'boolean',
        'allow_half_day' => 'boolean',
        'requires_documents' => 'boolean',
        'is_compensated_on_termination' => 'boolean',
        'can_take_advance' => 'boolean',
        'metadata' => 'json',
        'approval_levels' => 'json'
    ];

    /**
     * บริษัทที่เกี่ยวข้องกับประเภทการลา
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * รายการลาที่เกี่ยวข้องกับประเภทการลานี้
     */
    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }
}
