<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasCompanyScope;
use Carbon\Carbon;

class Invoice extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id', 
        'customer_id', 
        'order_id', 
        'invoice_number', 
        'reference_number',
        'invoice_date',
        'due_date', 
        'payment_terms',
        'status',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_type',
        'discount_amount', 
        'discount_value',
        'total_amount', 
        'notes', 
        'shipping_address', 
        'shipping_method', 
        'shipping_cost',
        'sales_person_id',
        'created_by', 
        'issued_at',
        'issued_by',
        'paid_at',
        'paid_by',
        'void_at',
        'void_by',
        'void_reason'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'issued_at' => 'datetime',
        'paid_at' => 'datetime',
        'void_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
    ];
    
    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!$model->company_id && Auth::check()) {
                $model->company_id = session('current_company_id') ?? Auth::user()->company_id ?? 1;
            }
            if (!$model->created_by && Auth::check()) {
                $model->created_by = Auth::id();
            }
        });
    }

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function voidBy()
    {
        return $this->belongsTo(User::class, 'void_by');
    }

    public function salesPerson()
    {
        return $this->belongsTo(Employee::class, 'sales_person_id');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeIssued($query)
    {
        return $query->where('status', 'issued');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeVoid($query)
    {
        return $query->where('status', 'void');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'void');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'issued')
            ->where('due_date', '<', now());
    }

    // Helper methods
    public function isOverdue()
    {
        return $this->status === 'issued' && $this->due_date && $this->due_date < Carbon::now();
    }

    public function issue($userId)
    {
        $this->status = 'issued';
        $this->issued_by = $userId;
        $this->issued_at = now();
        return $this->save();
    }

    public function markAsPaid($userId)
    {
        $this->status = 'paid';
        $this->paid_by = $userId;
        $this->paid_at = now();
        return $this->save();
    }

    public function voidInvoice($userId, $reason = null)
    {
        $this->status = 'void';
        $this->void_by = $userId;
        $this->void_at = now();
        $this->void_reason = $reason;
        return $this->save();
    }

    // Generate a unique invoice number
    public static function generateInvoiceNumber($companyId = null)
    {
        $companyId = $companyId ?? (session('company_id') ?? 1);
        $prefix = 'INV';
        $companyIdFormatted = str_pad($companyId, 2, '0', STR_PAD_LEFT); // รหัสบริษัท 2 หลัก (01, 02, ...)
        $year = date('y'); // ปี 2 หลักสุดท้าย (25 สำหรับ 2025)
        $month = date('m'); // เดือน 2 หลัก (05 สำหรับเดือนพฤษภาคม)
        
        // หาเลขลำดับสูงสุดของบริษัทในเดือนปีนี้
        $pattern = $prefix . $companyIdFormatted . $year . $month . '%';
        
        $latestInvoice = self::withTrashed()
            ->where('invoice_number', 'LIKE', $pattern)
            ->where('company_id', $companyId)
            ->orderBy('invoice_number', 'desc')
            ->first();
        
        $nextSequence = 1; // เริ่มต้นที่ 1 สำหรับเดือนใหม่
        
        if ($latestInvoice) {
            // ถ้ามีเลขล่าสุดในเดือนนี้ ดึง 4 หลักสุดท้ายและเพิ่มค่า
            $lastPart = substr($latestInvoice->invoice_number, -4);
            if (is_numeric($lastPart)) {
                $nextSequence = (int)$lastPart + 1;
            }
        }
        
        // สร้างเลขที่เอกสารในรูปแบบใหม่
        $invoiceNumber = $prefix . $companyIdFormatted . $year . $month . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
        
        // ตรวจสอบซ้ำและเพิ่มลำดับจนกว่าจะไม่ซ้ำ
        while (self::withTrashed()->where('invoice_number', $invoiceNumber)->exists()) {
            $nextSequence++;
            $invoiceNumber = $prefix . $companyIdFormatted . $year . $month . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
        }
        
        return $invoiceNumber;
    }
}
