<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quotation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'customer_id',
        'quotation_number',
        'issue_date',
        'expiry_date',
        'status',
        'discount_type',
        'discount_amount',
        'tax_rate',
        'tax_amount',
        'subtotal',
        'total_amount',
        'notes',
        'reference_number',
        'created_by',
        'approved_by',
        'approved_at',
        'sales_person_id', // ตรวจสอบว่ามีฟิลด์นี้หรือไม่ ถ้าไม่มีให้เพิ่ม
        'payment_term_id',
        'shipping_method',
        'shipping_cost',
        'currency',
        'currency_rate',
        'metadata'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'tax_rate' => 'float',
        'tax_amount' => 'float',
        'discount_amount' => 'float',
        'subtotal' => 'float',
        'total_amount' => 'float',
        'shipping_cost' => 'float',
        'currency_rate' => 'float',
        'approved_at' => 'datetime',
        'metadata' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * รับข้อมูลบริษัทที่เป็นเจ้าของใบเสนอราคานี้
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * รับข้อมูลลูกค้าที่เป็นเจ้าของใบเสนอราคานี้
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * รับข้อมูลรายการสินค้าในใบเสนอราคา
     */
    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    /**
     * รับข้อมูลคำสั่งซื้อที่เชื่อมโยงกับใบเสนอราคานี้
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * รับข้อมูลผู้สร้าง
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * รับข้อมูลผู้อนุมัติ
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the sales person associated with this quotation
     */
    public function salesPerson()
    {
        return $this->belongsTo(Employee::class, 'sales_person_id');
    }

    /**
     * ตรวจสอบว่าใบเสนอราคาหมดอายุหรือไม่
     */
    public function isExpired()
    {
        return $this->expiry_date < now();
    }
    
    /**
     * Generate a unique quotation number with format QT + COMPANY_ID + YY + MM + SEQUENCE
     * Example: QT0125050001 (where 01=company_id, 25=year, 05=month, 0001=sequence)
     */
    public static function generateQuotationNumber()
    {
        $prefix = 'QT';
        $companyId = session('company_id', 1);
        $companyIdFormatted = str_pad($companyId, 2, '0', STR_PAD_LEFT); // รหัสบริษัท 2 หลัก (01, 02, ...)
        $year = date('y'); // ปี 2 หลักสุดท้าย (25 สำหรับ 2025)
        $month = date('m'); // เดือน 2 หลัก (05 สำหรับเดือนพฤษภาคม)
        
        // หาเลขลำดับสูงสุดของบริษัทในเดือนปีนี้
        $pattern = $prefix . $companyIdFormatted . $year . $month . '%';
        
        $latestQuotation = self::withTrashed()
            ->where('quotation_number', 'LIKE', $pattern)
            ->where('company_id', $companyId)
            ->orderBy('quotation_number', 'desc')
            ->first();
        
        $nextSequence = 1; // เริ่มต้นที่ 1 สำหรับเดือนใหม่
        
        if ($latestQuotation) {
            // ถ้ามีเลขล่าสุดในเดือนนี้ ดึง 4 หลักสุดท้ายและเพิ่มค่า
            $lastPart = substr($latestQuotation->quotation_number, -4);
            if (is_numeric($lastPart)) {
                $nextSequence = (int)$lastPart + 1;
            }
        }
        
        // สร้างเลขที่เอกสารในรูปแบบใหม่
        $quotationNumber = $prefix . $companyIdFormatted . $year . $month . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
        
        // ตรวจสอบซ้ำและเพิ่มลำดับจนกว่าจะไม่ซ้ำ
        while (self::withTrashed()->where('quotation_number', $quotationNumber)->exists()) {
            $nextSequence++;
            $quotationNumber = $prefix . $companyIdFormatted . $year . $month . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
        }
        
        return $quotationNumber;
    }
}
