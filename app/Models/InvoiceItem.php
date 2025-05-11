<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_id',
        'product_id',
        'description',
        'quantity',
        'unit_price',
        'unit_id',
        'discount_percentage',
        'discount_amount',
        'tax_percentage',
        'tax_amount',
        'total'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    /**
     * Get the invoice that this item belongs to.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the product associated with this invoice item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the unit for this invoice item.
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Calculate totals for this item.
     */
    public function calculateTotals()
    {
        $subtotal = $this->quantity * $this->unit_price;
        
        // Calculate discount
        $discountAmount = 0;
        if ($this->discount_percentage > 0) {
            $discountAmount = $subtotal * ($this->discount_percentage / 100);
        }
        $this->discount_amount = $discountAmount;
        
        // Calculate tax
        $afterDiscount = $subtotal - $discountAmount;
        $taxAmount = 0;
        if ($this->tax_percentage > 0) {
            $taxAmount = $afterDiscount * ($this->tax_percentage / 100);
        }
        $this->tax_amount = $taxAmount;
        
        // Calculate total
        $this->total = $afterDiscount + $taxAmount;
        
        return $this;
    }
}
