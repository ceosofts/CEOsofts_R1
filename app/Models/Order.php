<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // เพิ่มการ import DB Facade
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
        'discount_type', // เพิ่มฟิลด์นี้
        'discount_amount', 
        'tax_rate', 
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
        'sales_person_id', // เพิ่มฟิลด์พนักงานขาย
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
    
    // เพิ่ม boot เพื่อกำหนดค่า company_id อัตโนมัติ
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
     * สร้างเลขที่ใบสั่งขายอัตโนมัติในรูปแบบ SO{YYYY}{MM}{NNNN}
     * พร้อมตรวจสอบความซ้ำซ้อนรวมถึงรายการที่ถูก Soft Delete แล้ว
     */
    public static function generateOrderNumber(?int $companyId = null, ?string $date = null): string
    {
        // ใช้ DB transaction เพื่อป้องกัน race condition
        return DB::transaction(function() use ($companyId, $date) {
            if (!$companyId) {
                $companyId = session('company_id', 1);
            }
            
            $now = $date ? \Carbon\Carbon::parse($date) : \Carbon\Carbon::now();
            $year = $now->format('Y');
            $month = $now->format('m');
            
            // ดึงเลขที่ล่าสุดของเดือนและปีที่กำหนด รวมถึงรายการที่ถูก soft delete
            $latestOrder = self::withTrashed()
                ->where('company_id', $companyId)
                ->where('order_number', 'like', "SO{$year}{$month}%")
                ->orderByRaw('LENGTH(order_number) DESC')
                ->orderBy('order_number', 'desc')
                ->lockForUpdate() // ใช้ lock เพื่อป้องกัน race condition
                ->first();
            
            if ($latestOrder) {
                // ถ้ามีเลขที่ใบสั่งขายในเดือนนี้แล้ว จะดึงตัวเลขที่ต่อจาก SO{YYYY}{MM} และเพิ่มขึ้น 1
                $lastNumber = (int) substr($latestOrder->order_number, 8);
                $nextNumber = $lastNumber + 1;
            } else {
                // ถ้าไม่มีเลขที่ใบสั่งขายในเดือนนี้ จะเริ่มที่ 0001
                $nextNumber = 1;
            }
            
            // สร้างเลขที่ใบสั่งขายรูปแบบ SO{YYYY}{MM}{NNNN}
            $orderNumber = 'SO' . $year . $month . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            
            // ตรวจสอบว่าเลขที่สร้างใหม่ซ้ำหรือไม่ (ป้องกัน race condition) รวมถึงรายการที่ถูก soft delete
            while (self::withTrashed()->where('order_number', $orderNumber)->exists()) {
                $nextNumber++;
                $orderNumber = 'SO' . $year . $month . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }
            
            return $orderNumber;
        });
    }
}
