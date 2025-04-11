<?php

namespace App\Domain\Sales\Models;

use App\Domain\Inventory\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'order_item_id',
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
        'metadata' => 'json'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
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
}
