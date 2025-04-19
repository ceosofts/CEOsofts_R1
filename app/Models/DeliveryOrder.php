<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasCompanyScope;
use Illuminate\Support\Facades\Auth;

class DeliveryOrder extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'order_id',
        'customer_id',
        'delivery_number',
        'delivery_date',
        'delivery_status',
        'shipping_address',
        'shipping_contact',
        'shipping_method',
        'tracking_number',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
        'metadata'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'delivery_date' => 'date',
        'approved_at' => 'datetime',
        'metadata' => 'json',
    ];

    // บูทโมเดลเพื่อกำหนดค่า company_id อัตโนมัติ
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

    /**
     * Get the order associated with the delivery order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the customer associated with the delivery order.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the user who created the delivery order.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved the delivery order.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the items for the delivery order.
     */
    public function deliveryOrderItems()
    {
        return $this->hasMany(DeliveryOrderItem::class);
    }

    /**
     * Get the company associated with the delivery order.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get status text with proper formatting for display.
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'รอดำเนินการ',
            'processing' => 'กำลังดำเนินการ',
            'shipped' => 'จัดส่งแล้ว',
            'delivered' => 'ส่งมอบแล้ว',
            'partial_delivered' => 'ส่งมอบบางส่วน',
            'cancelled' => 'ยกเลิก',
        ];
        
        return $statuses[$this->delivery_status] ?? $this->delivery_status;
    }
    
    /**
     * Get status color for display.
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'gray',
            'processing' => 'blue',
            'shipped' => 'purple',
            'delivered' => 'green',
            'partial_delivered' => 'yellow',
            'cancelled' => 'red',
        ];
        
        return $colors[$this->delivery_status] ?? 'gray';
    }
}
