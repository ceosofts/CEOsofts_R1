<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasCompanyScope;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
        'expected_delivery_date',
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
        'expected_delivery_date' => 'date',
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
     * สร้างเลขที่ใบส่งสินค้าอัตโนมัติในรูปแบบ DO+ปี+เดือน+running number 4 หลัก
     * ตัวอย่าง: DO2025040001
     */
    public static function generateDeliveryNumber()
    {
        $prefix = 'DO';
        $currentDate = Carbon::now();
        $year = $currentDate->format('Y');
        $month = $currentDate->format('m');
        
        // หาเลขที่ใบส่งสินค้าล่าสุดของเดือนนี้
        $latestDeliveryOrder = self::where('delivery_number', 'like', $prefix . $year . $month . '%')
            ->orderBy('delivery_number', 'desc')
            ->first();
            
        // ถ้ามีเลขที่ใบส่งสินค้าของเดือนนี้อยู่แล้ว ให้เพิ่มเลขลำดับต่อไป
        if ($latestDeliveryOrder) {
            $lastNumber = (int) substr($latestDeliveryOrder->delivery_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            // ถ้ายังไม่มีเลขที่ใบส่งสินค้าของเดือนนี้ ให้เริ่มต้นที่ 1
            $newNumber = 1;
        }
        
        // สร้างเลขที่ใบส่งสินค้าใหม่
        $formattedNumber = str_pad($newNumber, 4, '0', STR_PAD_LEFT);
        return $prefix . $year . $month . $formattedNumber;
    }

    /**
     * ตรวจสอบว่าเลขที่ใบส่งสินค้ามีอยู่ในระบบแล้วหรือไม่
     */
    public static function isDeliveryNumberExists($deliveryNumber)
    {
        return self::where('delivery_number', $deliveryNumber)->exists();
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
