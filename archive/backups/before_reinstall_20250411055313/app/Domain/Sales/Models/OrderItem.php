<?php

namespace App\Domain\Sales\Models;

use App\Domain\Inventory\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'quotation_item_id',
        'product_id',
        'description',
        'quantity',
        'unit',
        'unit_price',
        'discount_type',
        'discount_amount',
        'tax_rate',
        'tax_amount',
        'subtotal',
        'total',
        'delivered_quantity',
        'notes',
        'metadata'
    ];

    protected $casts = [
        'quantity' => 'float',
        'unit_price' => 'float',
        'discount_amount' => 'float',
        'tax_rate' => 'float',
        'tax_amount' => 'float',
        'subtotal' => 'float',
        'total' => 'float',
        'delivered_quantity' => 'float',
        'metadata' => 'json'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function quotationItem()
    {
        return $this->belongsTo(QuotationItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function calculateTotals()
    {
        $subtotal = $this->quantity * $this->unit_price;

        $discountAmount = 0;
        if ($this->discount_type === 'percentage') {
            $discountAmount = $subtotal * ($this->discount_amount / 100);
        } elseif ($this->discount_type === 'fixed') {
            $discountAmount = $this->discount_amount;
        }

        $afterDiscount = $subtotal - $discountAmount;
        
        $taxAmount = 0;
        if ($this->tax_rate > 0) {
            $taxAmount = $afterDiscount * ($this->tax_rate / 100);
        }

        $total = $afterDiscount + $taxAmount;

        $this->subtotal = $subtotal;
        $this->tax_amount = $taxAmount;
        $this->total = $total;

        return $this;
    }

    public function getRemainingQuantityAttribute()
    {
        return $this->quantity - ($this->delivered_quantity ?? 0);
    }

    public function getIsFullyDeliveredAttribute()
    {
        return $this->delivered_quantity >= $this->quantity;
    }
}
