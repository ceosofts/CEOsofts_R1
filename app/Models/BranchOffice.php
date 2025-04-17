<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log; // เพิ่มการ import Log Facade

class BranchOffice extends Model
{
    use HasFactory, SoftDeletes; // , CompanyScope;  // <--- เอา CompanyScope ออก
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'code',
        'name_th',
        'name_en',
        'name',
        'address_th',
        'address_en',
        'tax_id',
        'phone',
        'fax',
        'email',
        'website',
        'is_headquarters',
        'status',
        'is_active',
        'metadata',
        'manager_id'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_headquarters' => 'boolean',
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];
    
    /**
     * Get the company that owns the branch office.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
    
    /**
     * Get the manager for the branch office.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    /**
     * Get the employees for the branch office.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the departments for the branch office.
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Get the name attribute
     * 
     * @return string
     */
    public function getNameAttribute($value)
    {
        // เพิ่ม debug เพื่อตรวจสอบค่า
        Log::debug('BranchOffice name value: ' . $value); 
        return $this->name_th ?? $this->name_en ?? $value ?? '';
    }

    /**
     * อัพเดท field metadata ให้เป็น JSON
     */
    public function setMetadataAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['metadata'] = json_encode($value);
        } else {
            $this->attributes['metadata'] = $value;
        }
    }
    
    /**
     * แปลงค่า metadata จาก JSON เป็น array
     */
    public function getMetadataAttribute($value)
    {
        if (!$value) return [];
        
        // กรณีที่ metadata เป็น double-encoded JSON string
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_string($decoded)) {
                return json_decode($decoded, true) ?? [];
            }
            return $decoded ?? [];
        }
        
        return $value;
    }
    
    /**
     * Scope สำหรับการกรองสาขาที่เป็นสำนักงานใหญ่
     */
    public function scopeHeadquarters($query)
    {
        return $query->where('is_headquarters', true);
    }

    /**
     * Generate a unique branch code based on company ID
     * Format: BRA-{company_id}-{sequence}
     *
     * @param int $companyId
     * @return string
     */
    public static function generateBranchCode($companyId): string
    {
        // ปรับ company_id ให้มี 2 หลัก (เช่น 1 -> 01)
        $companyPrefix = str_pad($companyId, 2, '0', STR_PAD_LEFT);
        
        // นับจำนวนสาขาในบริษัทนี้และบวก 1
        $count = self::where('company_id', $companyId)->count() + 1;
        
        // สร้างรหัสเป็น BRA-01-001 (3 หลักสุดท้าย)
        $sequence = str_pad($count, 3, '0', STR_PAD_LEFT);
        
        return "BRA-{$companyPrefix}-{$sequence}";
    }
    
    /**
     * Get the formatted code attribute.
     * 
     * @return string
     */
    public function getFormattedCodeAttribute(): string
    {
        if ($this->is_headquarters) {
            $companyPrefix = str_pad($this->company_id, 2, '0', STR_PAD_LEFT);
            return "HQ-{$companyPrefix}";
        }
        
        return $this->code ?? '';
    }

    // ตัวอย่าง
    // public function scopeForCurrentCompany($query) { ... }
}
