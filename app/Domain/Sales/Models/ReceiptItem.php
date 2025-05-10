<?php

namespace App\Domain\Sales\Models;

use App\Domain\Inventory\Models\Product;
use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceiptItem extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'company_id',
        'receipt_id',
        'product_id',
        'description',
        'quantity',
        'unit_price',
        'unit',
        'discount_type',
        'discount_amount',
        'amount',
        'tax_rate',
        'tax_amount',
        'metadata'
    ];

    protected $casts = [
        'quantity' => 'float',
        'unit_price' => 'float',
        'discount_amount' => 'float',
        'amount' => 'float',
        'tax_rate' => 'float',
        'tax_amount' => 'float',
        'metadata' => 'json'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}