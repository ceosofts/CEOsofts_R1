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
}
