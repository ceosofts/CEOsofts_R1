<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
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
        'quotation_id',
        'order_no',
        'reference',
        'order_date',
        'due_date',
        'payment_term',
        'payment_status',
        'shipping_address',
        'shipping_method',
        'shipping_cost',
        'tax_rate',
        'tax_amount',
        'discount_type',
        'discount_amount',
        'subtotal',
        'total_amount',
        'notes',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'order_date' => 'date',
        'due_date' => 'date',
        'shipping_cost' => 'float',
        'tax_rate' => 'float',
        'tax_amount' => 'float',
        'discount_amount' => 'float',
        'subtotal' => 'float',
        'total_amount' => 'float',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the company that owns the order.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the customer that owns the order.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the quotation associated with the order.
     */
    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
