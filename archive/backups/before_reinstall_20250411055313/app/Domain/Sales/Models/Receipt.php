<?php

namespace App\Domain\Sales\Models;

use App\Domain\Organization\Models\Company;
use App\Domain\Settings\Models\User;
use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receipt extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'company_id',
        'customer_id',
        'invoice_id',
        'receipt_number',
        'receipt_date',
        'payment_method',
        'payment_reference',
        'amount',
        'currency',
        'exchange_rate',
        'notes',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'cancelled_by',
        'cancelled_at',
        'metadata'
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'exchange_rate' => 'float',
        'amount' => 'float',
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

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
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

    public function items()
    {
        return $this->hasMany(ReceiptItem::class);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function approve($userId)
    {
        $this->status = 'approved';
        $this->approved_by = $userId;
        $this->approved_at = now();
        
        // Update the related invoice
        if ($this->invoice) {
            $this->invoice->amount_paid += $this->amount;
            if ($this->invoice->amount_paid >= $this->invoice->total) {
                $this->invoice->markAsPaid();
            } else {
                $this->invoice->markAsPartiallyPaid($this->invoice->amount_paid);
            }
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
        
        // Update the related invoice if receipt was approved
        if ($this->status === 'approved' && $this->invoice) {
            $this->invoice->amount_paid -= $this->amount;
            $this->invoice->recalculateAmountDue();
        }
        
        return $this->save();
    }
}
