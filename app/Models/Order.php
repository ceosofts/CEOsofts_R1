<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasCompanyScope;

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
        'cancellation_reason'
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
}
