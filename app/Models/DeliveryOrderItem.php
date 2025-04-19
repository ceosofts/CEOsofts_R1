<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'delivery_order_id',
        'order_item_id',
        'product_id',
        'description',
        'quantity',
        'unit',
        'status',
        'notes',
        'metadata'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'decimal:2',
        'metadata' => 'json',
    ];

    /**
     * Get the delivery order that owns the item.
     */
    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    /**
     * Get the order item associated with the delivery item.
     */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Get the product associated with the delivery item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
