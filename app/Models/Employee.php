<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // เปลี่ยนจากการ import App\Models\BelongsTo
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use App\Traits\CompanyScope;

class Employee extends Model
{
    use HasFactory, SoftDeletes, CompanyScope;

    protected $fillable = [
        'uuid',
        'company_id',
        'email',
        'company_email',
        'has_company_email',
        'department_id',
        'position_id',
        'branch_office_id',
        'employee_code',
        'first_name',
        'last_name',
        'phone',
        'address',
        'id_card_number',
        'hire_date',
        'termination_date',
        'status',
        'profile_image',
        'manager_id',
        
        // ข้อมูลส่วนตัว
        'title',
        'nickname',
        'gender',
        'birthdate',
        'nationality',
        'religion',
        'blood_type',
        'height',
        'weight',
        'marital_status',
        'medical_conditions',
        
        // ข้อมูลการศึกษาและประสบการณ์
        'education_level',
        'education_institute',
        'education_major',
        'years_experience',
        'skills',
        'certificates',
        'previous_employment',
        
        // ข้อมูลติดต่อฉุกเฉิน
        'emergency_contact_name',
        'emergency_contact_phone',
        
        // ข้อมูลธนาคารและภาษี
        'bank_name',
        'bank_account',
        'tax_id',
        'tax_filing_status',
        'social_security_number',
        
        // ข้อมูลการทำงาน
        'employee_type',
        'probation_end_date',
        
        // ข้อมูลเอกสาร
        'passport_number',
        'passport_expiry',
        'work_permit_number',
        'work_permit_expiry',
        'visa_type',
        'visa_expiry',
        
        // ข้อมูล metadata
        'metadata',
    ];

    protected $casts = [
        'hire_date' => 'datetime',
        'termination_date' => 'datetime',
        'birthdate' => 'datetime',
        'probation_end_date' => 'datetime',
        'passport_expiry' => 'datetime',
        'work_permit_expiry' => 'datetime',
        'visa_expiry' => 'datetime',
        'has_company_email' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Get the company that owns the employee.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the department that owns the employee.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the position that owns the employee.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Get the branch office that owns the employee.
     */
    public function branchOffice(): BelongsTo
    {
        return $this->belongsTo(BranchOffice::class, 'branch_office_id');
    }

    /**
     * Get the employee that manages this employee.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    /**
     * Get the subordinates for the employee.
     */
    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }
    
    /**
     * Get the employee's full name.
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Set the employee's metadata.
     */
    public function setMetadataAttribute($value)
    {
        if (is_string($value) && !empty($value)) {
            try {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $this->attributes['metadata'] = json_encode($decoded);
                    return;
                }
            } catch (\Exception $e) {
                Log::error('Error decoding employee metadata: ' . $e->getMessage());
            }
        }
        
        $this->attributes['metadata'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Get the employee's metadata.
     */
    public function getMetadataAttribute($value)
    {
        if (empty($value)) return [];
        
        try {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        } catch (\Exception $e) {
            Log::error('Error decoding employee metadata: ' . $e->getMessage());
        }
        
        return [];
    }
}
