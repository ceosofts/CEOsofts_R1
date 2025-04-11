<?php

namespace App\Domain\Sales\Models;

use App\Domain\Organization\Models\Company;
use App\Domain\Settings\Models\User;
use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'company_id',
        'customer_id',
        'quotation_id',
        'order_number',
        'order_date',
        'delivery_date', // แก้ไขจาก expected_delivery_date เป็น delivery_date
        'shipping_address', // แก้ไขจาก delivery_address เป็น shipping_address
        'shipping_method', // เพิ่มฟิลด์ถ้าจำเป็น
        'payment_method', // เพิ่มฟิลด์ถ้าจำเป็น
        'payment_terms',
        'reference',
        'status',
        'currency',
        'exchange_rate',
        'discount_type',
        'discount_amount',
        'tax_inclusive',
        'tax_rate',
        'tax_amount',
        'shipping_fee',
        'subtotal',
        'total_discount',
        'total',
        'notes',
        'terms',
        'created_by',
        'approved_by',
        'approved_at',
        'delivered_at',
        'completed_at',
        'cancelled_by',
        'cancelled_at',
        'metadata'
    ];

    protected $casts = [
        'order_date' => 'date',
        'delivery_date' => 'date', // แก้ไขจาก expected_delivery_date เป็น delivery_date
        'exchange_rate' => 'float',
        'discount_amount' => 'float',
        'tax_inclusive' => 'boolean',
        'tax_rate' => 'float',
        'tax_amount' => 'float',
        'shipping_fee' => 'float',
        'subtotal' => 'float',
        'total_discount' => 'float',
        'total' => 'float',
        'approved_at' => 'datetime',
        'delivered_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'metadata' => 'json'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function canceller()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['cancelled', 'completed']);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function isOverdue()
    {
        return $this->delivery_date < now() && // แก้ไขจาก expected_delivery_date เป็น delivery_date
               !in_array($this->status, ['delivered', 'completed', 'cancelled']);
    }

    public function approve($userId)
    {
        $this->status = 'approved';
        $this->approved_by = $userId;
        $this->approved_at = now();
        return $this->save();
    }

    public function markDelivered()
    {
        $this->status = 'delivered';
        $this->delivered_at = now();
        return $this->save();
    }

    public function markCompleted()
    {
        $this->status = 'completed';
        $this->completed_at = now();
        return $this->save();
    }

    public function cancel($userId, $reason = null)
    {
        $this->status = 'cancelled';
        $this->cancelled_by = $userId;
        $this->cancelled_at = now();
        
        if ($reason) {
            $metadata = $this->metadata ?? [];
            $metadata['cancellation_reason'] = $reason;
            $this->metadata = $metadata;
        }
        
        return $this->save();
    }

    public function calculateTotals()
    {
        $subtotal = $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });

        $totalDiscount = 0;
        if ($this->discount_type === 'percentage') {
            $totalDiscount = $subtotal * ($this->discount_amount / 100);
        } elseif ($this->discount_type === 'fixed') {
            $totalDiscount = $this->discount_amount;
        }

        $afterDiscount = $subtotal - $totalDiscount;
        
        $taxAmount = 0;
        if ($this->tax_rate > 0) {
            if ($this->tax_inclusive) {
                // Tax is already included in the price
                $taxAmount = $afterDiscount - ($afterDiscount / (1 + ($this->tax_rate / 100)));
            } else {
                // Tax is additional
                $taxAmount = $afterDiscount * ($this->tax_rate / 100);
            }
        }

        $total = ($this->tax_inclusive ? $afterDiscount : $afterDiscount + $taxAmount) + ($this->shipping_fee ?? 0);

        $this->subtotal = $subtotal;
        $this->total_discount = $totalDiscount;
        $this->tax_amount = $taxAmount;
        $this->total = $total;

        return $this;
    }
}
