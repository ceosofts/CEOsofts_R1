<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'customer_id',
        'quotation_no',
        'reference',
        'quotation_date',
        'valid_until',
        'payment_term',
        'tax_rate',
        'tax_amount',
        'discount_type',
        'discount_amount',
        'subtotal',
        'total_amount',
        'notes',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'quotation_date' => 'date',
        'valid_until' => 'date',
        'tax_rate' => 'float',
        'tax_amount' => 'float',
        'discount_amount' => 'float',
        'subtotal' => 'float',
        'total_amount' => 'float',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the company that owns the quotation.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the customer that owns the quotation.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the orders for the quotation.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
