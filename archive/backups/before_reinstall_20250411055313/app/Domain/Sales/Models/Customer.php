<?php

namespace App\Domain\Sales\Models;

use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'company_id',
        'customer_code',
        'name',
        'contact_name',
        'email',
        'phone',
        'address',
        'tax_id',
        'credit_limit',
        'payment_terms',
        'status',
        'notes',
        'metadata'
    ];

    protected $casts = [
        'credit_limit' => 'float',
        'status' => 'string',
        'metadata' => 'json'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}
