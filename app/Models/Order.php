<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Schema; // เพิ่ม import Schema facade
use App\Traits\HasCompanyScope;
use Carbon\Carbon;

class Order extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id', 
        'customer_id', 
        'quotation_id', 
        'order_number', 
        'order_date',
        'delivery_date', 
        'total_amount', 
        'status', 
        'metadata', 
        'subtotal',
        'discount_type',
        'discount_amount', 
        'tax_amount', 
        'customer_po_number',
        'notes', 
        'payment_terms', 
        'shipping_address', 
        'shipping_method', 
        'shipping_cost',
        'created_by', 
        'confirmed_by', 
        'confirmed_at', 
        'processed_by', 
        'processed_at',
        'shipped_by', 
        'shipped_at', 
        'delivered_by', 
        'delivered_at', 
        'cancelled_by', 
        'cancelled_at',
        'cancellation_reason',
        'sales_person_id',
        // เพิ่ม tax_rate ใน $fillable
        'tax_rate',
        'shipping_cost', // เพิ่ม shipping_cost ใน $fillable
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'order_date' => 'datetime',
        'delivery_date' => 'datetime',
        'confirmed_at' => 'datetime',
        'processed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'metadata' => 'array',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
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
            
            // เพิ่มการตรวจสอบคอลัมน์ tax_rate (ใช้ Schema facade โดยไม่มี \ ข้างหน้า)
            if (!Schema::hasColumn('orders', 'tax_rate')) {
                unset($model->tax_rate);
            }

            // เพิ่มการตรวจสอบคอลัมน์ shipping_cost
            if (!Schema::hasColumn('orders', 'shipping_cost')) {
                unset($model->shipping_cost);
            }
        });
    }

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function shippedBy()
    {
        return $this->belongsTo(User::class, 'shipped_by');
    }

    public function deliveredBy()
    {
        return $this->belongsTo(User::class, 'delivered_by');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Get the company associated with the order.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the sales person associated with the order.
     */
    public function salesPerson()
    {
        return $this->belongsTo(Employee::class, 'sales_person_id');
    }

    /**
     * Get the delivery orders associated with the order.
     */
    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    /**
     * Get invoices associated with this order
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    // Accessor สำหรับสถานะแบบอ่านง่าย
    public function getStatusTextAttribute()
    {
        $statuses = [
            'draft' => 'ร่าง',
            'confirmed' => 'ยืนยันแล้ว',
            'processing' => 'กำลังดำเนินการ',
            'shipped' => 'จัดส่งแล้ว',
            'delivered' => 'ส่งมอบแล้ว',
            'cancelled' => 'ยกเลิก',
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }
    
    // Accessor สำหรับสีของสถานะ
    public function getStatusColorAttribute()
    {
        $colors = [
            'draft' => 'gray',
            'confirmed' => 'blue',
            'processing' => 'yellow',
            'shipped' => 'purple',
            'delivered' => 'green',
            'cancelled' => 'red',
        ];
        
        return $colors[$this->status] ?? 'gray';
    }

    public function getDiscountValueAttribute()
    {
        if ($this->discount_type === 'percentage') {
            return $this->subtotal * ($this->discount_amount / 100);
        }
        
        return $this->discount_amount;
    }

    public function getNetTotalAttribute()
    {
        return $this->subtotal - $this->discount_value;
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * สร้างเลขที่ใบสั่งขายอัตโนมัติในรูปแบบ SO + COMPANY_ID + YY + MM + SEQUENCE
     * Example: SO0125050001 (where 01=company_id, 25=year, 05=month, 0001=sequence)
     */
    public static function generateOrderNumber(?int $companyId = null, ?string $date = null): string
    {
        if (!$companyId) {
            $companyId = session('company_id', 1);
        }
        
        $now = $date ? \Carbon\Carbon::parse($date) : \Carbon\Carbon::now();
        $year = $now->format('y'); // ปี 2 หลักสุดท้าย (25 สำหรับ 2025)
        $month = $now->format('m'); // เดือน 2 หลัก (05 สำหรับเดือนพฤษภาคม)
        $companyIdFormatted = str_pad($companyId, 2, '0', STR_PAD_LEFT); // รหัสบริษัท 2 หลัก (01, 02, ...)
        
        $prefix = 'SO';
        
        // หาเลขลำดับสูงสุดของบริษัทในเดือนปีนี้
        $pattern = $prefix . $companyIdFormatted . $year . $month . '%';
        
        $latestOrder = self::withTrashed()
            ->where('order_number', 'LIKE', $pattern)
            ->where('company_id', $companyId)
            ->orderBy('order_number', 'desc')
            ->first();
        
        $nextSequence = 1; // เริ่มต้นที่ 1 สำหรับเดือนใหม่
        
        if ($latestOrder) {
            // ถ้ามีเลขล่าสุดในเดือนนี้ ดึง 4 หลักสุดท้ายและเพิ่มค่า
            $lastPart = substr($latestOrder->order_number, -4);
            if (is_numeric($lastPart)) {
                $nextSequence = (int)$lastPart + 1;
            }
        }
        
        // สร้างเลขที่เอกสารในรูปแบบใหม่
        $orderNumber = $prefix . $companyIdFormatted . $year . $month . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
        
        // ตรวจสอบซ้ำและเพิ่มลำดับจนกว่าจะไม่ซ้ำ
        while (self::withTrashed()->where('order_number', $orderNumber)->exists()) {
            $nextSequence++;
            $orderNumber = $prefix . $companyIdFormatted . $year . $month . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
        }
        
        return $orderNumber;
    }
}
