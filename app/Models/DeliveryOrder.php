<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasCompanyScope;

class DeliveryOrder extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;
    
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'order_id',
        'customer_id',
        'delivery_number',
        'delivery_date',
        'status',
        'delivery_status',  // เพิ่มฟิลด์นี้
        'delivery_address', 
        'shipping_address',  // เพิ่มฟิลด์นี้ (อาจเป็น alias ของ delivery_address)
        'shipping_method',   // เพิ่มฟิลด์นี้
        'receiver_name',
        'receiver_contact',
        'tracking_number',
        'carrier',
        'notes',
        'delivered_at',
        'created_by',
        'updated_by'
    ];

    /**
     * Cast attributes to native types.
     */
    protected $casts = [
        'delivery_date' => 'datetime',
        'delivered_at' => 'datetime',
    ];
    
    // ความสัมพันธ์กับตารางอื่นๆ
    
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    
    public function items()
    {
        return $this->hasMany(DeliveryOrderItem::class);
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
