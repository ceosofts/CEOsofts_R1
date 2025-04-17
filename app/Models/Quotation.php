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
        'total_amount',
        'status',
        'notes',
        'discount_amount',
        'discount_type',
        'tax_rate',
        'tax_amount',
        'subtotal',
        'reference_number',
        'created_by',
        'approved_by',
        'approved_at',
        'sales_person_id',
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
     * ตรวจสอบว่าใบเสนอราคาหมดอายุหรือไม่
     */
    public function isExpired()
    {
        return $this->expiry_date < now();
    }
}
