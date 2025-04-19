<?php

namespace App\Domain\Sales\Models;

use App\Domain\Organization\Models\Company;
use App\Domain\Settings\Models\User;
use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

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

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
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

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now())
                     ->where('status', 'active');
    }
    
    public function isExpired()
    {
        return $this->expiry_date < now();
    }

    public function approve($userId)
    {
        $this->status = 'approved';
        $this->approved_by = $userId;
        $this->approved_at = now();
        return $this->save();
    }

    public function reject($userId, $reason = null)
    {
        $this->status = 'rejected';
        $this->cancelled_by = $userId;
        $this->cancelled_at = now();
        
        if ($reason) {
            $metadata = $this->metadata ?? [];
            $metadata['rejection_reason'] = $reason;
            $this->metadata = $metadata;
        }
        
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

        $total = $this->tax_inclusive ? $afterDiscount : $afterDiscount + $taxAmount;

        $this->subtotal = $subtotal;
        $this->total_discount = $totalDiscount;
        $this->tax_amount = $taxAmount;
        $this->total = $total;

        return $this;
    }
}
