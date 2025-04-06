<?php

namespace App\Domain\Sales\Models;

use App\Domain\Organization\Models\Company;
use App\Domain\Settings\Models\User;
use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'company_id',
        'customer_id',
        'order_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'reference',
        'status',
        'currency',
        'exchange_rate',
        'discount_type',
        'discount_amount',
        'tax_inclusive',
        'tax_rate',
        'tax_amount',
        'subtotal',
        'total_discount',
        'total',
        'amount_paid',
        'amount_due',
        'notes',
        'terms',
        'created_by',
        'approved_by',
        'approved_at',
        'cancelled_by',
        'cancelled_at',
        'metadata'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'exchange_rate' => 'float',
        'discount_amount' => 'float',
        'tax_inclusive' => 'boolean',
        'tax_rate' => 'float',
        'tax_amount' => 'float',
        'subtotal' => 'float',
        'total_discount' => 'float',
        'total' => 'float',
        'amount_paid' => 'float',
        'amount_due' => 'float',
        'approved_at' => 'datetime',
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

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
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

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopePartiallyPaid($query)
    {
        return $query->where('status', 'partially_paid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'paid')
                     ->where('status', '!=', 'cancelled')
                     ->where('due_date', '<', now());
    }
    
    public function isOverdue()
    {
        return $this->due_date < now() && 
               !in_array($this->status, ['paid', 'cancelled']);
    }

    public function approve($userId)
    {
        $this->status = 'approved';
        $this->approved_by = $userId;
        $this->approved_at = now();
        return $this->save();
    }

    public function markAsPaid()
    {
        $this->status = 'paid';
        $this->amount_paid = $this->total;
        $this->amount_due = 0;
        return $this->save();
    }

    public function markAsPartiallyPaid($amountPaid)
    {
        $this->status = 'partially_paid';
        $this->amount_paid = $amountPaid;
        $this->amount_due = $this->total - $amountPaid;
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
        $this->amount_due = $total - ($this->amount_paid ?? 0);

        return $this;
    }

    public function recalculateAmountDue()
    {
        $this->amount_due = $this->total - $this->amount_paid;

        if ($this->amount_due <= 0) {
            $this->status = 'paid';
        } elseif ($this->amount_paid > 0) {
            $this->status = 'partially_paid';
        }

        return $this->save();
    }
}
