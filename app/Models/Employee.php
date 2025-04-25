<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // เปลี่ยนจากการ import App\Models\BelongsTo
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use App\Traits\HasCompanyScope;

class Employee extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

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

    /**
     * Handle JSON metadata field
     */
    protected $casts = [
        'birthdate' => 'datetime',
        'hire_date' => 'datetime',
        'termination_date' => 'datetime',
        'probation_end_date' => 'datetime',
        'work_permit_expiry' => 'datetime',
        'passport_expiry' => 'datetime',
        'visa_expiry' => 'datetime',
        'metadata' => 'array',
        'has_company_email' => 'boolean',
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
        $title = $this->title ? "{$this->title} " : "";
        return "{$title}{$this->first_name} {$this->last_name}";
    }

    /**
     * Get formatted birthdate
     */
    public function getFormattedBirthdateAttribute()
    {
        return $this->birthdate ? $this->birthdate->format('d/m/Y') : null;
    }

    /**
     * Get formatted hire date
     */
    public function getFormattedHireDateAttribute()
    {
        return $this->hire_date ? $this->hire_date->format('d/m/Y') : null;
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

    /**
     * Generate employee code for a new employee
     * Format: EMP-XX-YYY where XX is company ID and YYY is sequential number
     *
     * @param int $companyId
     * @return string
     */
    public static function generateEmployeeCode($companyId)
    {
        // แปลงเป็นรูปแบบ 2 หลัก เช่น 01, 02, 03...
        $companyPart = str_pad($companyId, 2, '0', STR_PAD_LEFT);
        
        try {
            // บังคับให้เป็นการค้นหาโดยตรงจากตาราง ไม่ใช้ scope
            $query = self::query()
                ->withoutGlobalScopes() // ยกเลิก global scopes ทั้งหมด
                ->withTrashed()        // รวมที่ถูกลบด้วย soft delete
                ->where('company_id', $companyId);
                
            // จำนวนพนักงานทั้งหมด
            $totalEmployees = (clone $query)->count();
            
            // ดึงรหัสทั้งหมดมาตรวจสอบ
            $allCodes = (clone $query)->pluck('employee_code')->toArray();
            
            // บันทึกข้อมูลสำหรับตรวจสอบ
            Log::info('Employee data for company ' . $companyId, [
                'total_employees' => $totalEmployees,
                'all_codes' => $allCodes,
                'query_sql' => $query->toSql()
            ]);
            
            // ค้นหารหัสที่มีหมายเลขสูงที่สุด
            $maxNumber = 0;
            foreach ($allCodes as $code) {
                // ตรวจสอบรูปแบบ EMP-XX-YYY
                if (preg_match('/EMP-' . $companyPart . '-(\d{3})/', $code ?? '', $matches)) {
                    $currentNumber = (int)$matches[1];
                    if ($currentNumber > $maxNumber) {
                        $maxNumber = $currentNumber;
                    }
                }
            }
            
            // กำหนดหมายเลขถัดไป
            $nextNumber = ($maxNumber > 0) ? $maxNumber + 1 : $totalEmployees + 1;
            
            // ถ้า maxNumber และ totalEmployees ไม่ตรงกัน
            if ($maxNumber > 0 && $nextNumber <= $totalEmployees) {
                $nextNumber = $totalEmployees + 1; // ป้องกันการซ้ำ
                Log::warning('Employee number mismatch', [
                    'max_number' => $maxNumber,
                    'total_employees' => $totalEmployees,
                    'next_number_adjusted' => $nextNumber
                ]);
            }
            
            $employeePart = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
            $newCode = "EMP-{$companyPart}-{$employeePart}";
            
            // เช็คซ้ำอีกครั้ง
            while (self::withoutGlobalScopes()->withTrashed()->where('employee_code', $newCode)->exists()) {
                $nextNumber++;
                $employeePart = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
                $newCode = "EMP-{$companyPart}-{$employeePart}";
            }
            
            Log::info('Generated employee code result', [
                'company_id' => $companyId,
                'max_number' => $maxNumber,
                'total_employees' => $totalEmployees,
                'next_number' => $nextNumber,
                'new_code' => $newCode
            ]);
            
            return $newCode;
            
        } catch (\Exception $e) {
            // กรณีเกิดข้อผิดพลาด ให้ใช้วิธีแบบง่าย
            $fallbackNumber = rand(100, 999); // สุ่มเลข 3 หลักในกรณีฉุกเฉิน
            $fallbackCode = "EMP-{$companyPart}-{$fallbackNumber}";
            
            Log::error('Error generating employee code', [
                'error' => $e->getMessage(),
                'fallback_code' => $fallbackCode
            ]);
            
            return $fallbackCode;
        }
    }

    /**
     * Get the quotations where this employee is the sales person
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'sales_person_id');
    }

    /**
     * Get the orders where this employee is the sales person
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'sales_person_id');
    }
    
    /**
     * Get the display name with employee code
     *
     * @return string
     */
    public function getDisplayNameAttribute()
    {
        return "{$this->employee_code} - {$this->first_name} {$this->last_name}";
    }
    
    /**
     * Scope to find sales employees
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSalesPeople($query)
    {
        // อาจจะปรับเปลี่ยนตาม position_id จริงในระบบ
        return $query->whereIn('position_id', [3, 4, 5])
                    ->orWhere(function($q) {
                        $q->where('metadata', 'like', '%"sales_target"%')
                          ->orWhere('metadata', 'like', '%"sales_area"%');
                    });
    }
    
    /**
     * Check if employee is a sales person
     *
     * @return bool
     */
    public function isSalesPerson()
    {
        // อาจจะปรับเปลี่ยนตาม position_id จริงในระบบ
        return in_array($this->position_id, [3, 4, 5]) 
               || (is_array($this->metadata) && 
                  (isset($this->metadata['sales_target']) || isset($this->metadata['sales_area'])));
    }
    
    /**
     * Get the total sales amount from all quotations
     *
     * @return float
     */
    public function getTotalSalesQuotationsAttribute()
    {
        return $this->quotations()
                    ->where('status', 'approved')
                    ->sum('total_amount');
    }
    
    /**
     * Get the total sales amount from all orders
     *
     * @return float
     */
    public function getTotalSalesOrdersAttribute()
    {
        return $this->orders()
                    ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
                    ->sum('total_amount');
    }
}
