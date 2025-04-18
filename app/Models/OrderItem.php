<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// ยกเลิกการใช้ SoftDeletes trait ชั่วคราว
// use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    // use HasFactory, SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'description',
        'quantity',
        'unit_price', // รองรับทั้ง unit_price
        'price',      // และ price
        'unit_id',    // เพิ่มฟิลด์ unit_id
        'sku',
        'total',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',  // ยืนยันว่า quantity สามารถเป็น decimal ได้
        'unit_price' => 'decimal:2',
        'price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // ความสัมพันธ์กับโมเดลอื่นๆ
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
