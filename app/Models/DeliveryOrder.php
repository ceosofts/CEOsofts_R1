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
    
    /**
     * Get the user who approved the delivery order.
     */
    public function approver()
    {
        // ความสัมพันธ์กับผู้อนุมัติ (ถ้ามีคอลัมน์ approved_by)
        // หากไม่มีคอลัมน์ approved_by จริงๆ ให้ใช้ updated_by แทน
        return $this->belongsTo(User::class, 'updated_by');
    }
    
    /**
     * Get the company that owns the delivery order.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    /**
     * Get the status color for the delivery order status.
     */
    public function getStatusColorAttribute()
    {
        // ใช้สถานะจัดส่งเป็นหลัก หากไม่มี ใช้สถานะทั่วไป
        $status = $this->delivery_status ?? $this->status ?? 'pending';
        
        return match ($status) {
            'pending' => 'yellow',
            'processing' => 'blue',
            'shipped' => 'indigo',
            'delivered' => 'green',
            'partial_delivered' => 'amber',
            'returned' => 'rose',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }
    
    /**
     * Get the human-readable status text for the delivery order status.
     */
    public function getStatusTextAttribute()
    {
        // ใช้สถานะจัดส่งเป็นหลัก หากไม่มี ใช้สถานะทั่วไป
        $status = $this->delivery_status ?? $this->status ?? 'pending';
        
        return match ($status) {
            'pending' => 'รอดำเนินการ',
            'processing' => 'กำลังดำเนินการ',
            'shipped' => 'จัดส่งแล้ว',
            'delivered' => 'ส่งมอบแล้ว',
            'partial_delivered' => 'ส่งมอบบางส่วน',
            'returned' => 'ส่งคืนแล้ว',
            'cancelled' => 'ยกเลิก',
            default => 'ไม่ระบุสถานะ',
        };
    }
    
    /**
     * Generate a unique delivery number with format DO + COMPANY_ID + YY + MM + SEQUENCE
     * Example: DO0125050001 (where 01=company_id, 25=year, 05=month, 0001=sequence)
     */
    public static function generateDeliveryNumber()
    {
        $prefix = 'DO';
        $companyId = session('company_id', 1);
        $companyIdFormatted = str_pad($companyId, 2, '0', STR_PAD_LEFT); // รหัสบริษัท 2 หลัก (01, 02, ...)
        $year = date('y'); // ปี 2 หลักสุดท้าย (25 สำหรับ 2025)
        $month = date('m'); // เดือน 2 หลัก (05 สำหรับเดือนพฤษภาคม)
        
        // หาเลขลำดับสูงสุดของบริษัทในเดือนปีนี้
        $pattern = $prefix . $companyIdFormatted . $year . $month . '%';
        
        $latestDeliveryOrder = self::withTrashed()
            ->where('delivery_number', 'LIKE', $pattern)
            ->where('company_id', $companyId)
            ->orderBy('delivery_number', 'desc')
            ->first();
        
        $nextSequence = 1; // เริ่มต้นที่ 1 สำหรับเดือนใหม่
        
        if ($latestDeliveryOrder) {
            // ถ้ามีเลขล่าสุดในเดือนนี้ ดึง 4 หลักสุดท้ายและเพิ่มค่า
            $lastPart = substr($latestDeliveryOrder->delivery_number, -4);
            if (is_numeric($lastPart)) {
                $nextSequence = (int)$lastPart + 1;
            }
        }
        
        // สร้างเลขที่เอกสารในรูปแบบใหม่
        $deliveryNumber = $prefix . $companyIdFormatted . $year . $month . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
        
        // ตรวจสอบซ้ำและเพิ่มลำดับจนกว่าจะไม่ซ้ำ
        while (self::withTrashed()->where('delivery_number', $deliveryNumber)->exists()) {
            $nextSequence++;
            $deliveryNumber = $prefix . $companyIdFormatted . $year . $month . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
        }
        
        return $deliveryNumber;
    }
}
