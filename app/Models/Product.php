<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Shared\Traits\HasCompanyScope;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'uuid',
        'company_id',
        'category_id',
        'unit_id',
        'name',
        'code',
        'description',
        'price',
        'cost',
        'unit',
        'sku',
        'barcode',
        'stock_quantity',
        'current_stock',
        'min_stock',
        'location',
        'image',
        'is_active',
        'is_inventory_tracked',
        'is_service',
        'dimension',
        'metadata',
        'list_price',
        'wholesale_price',
        'special_price',
        'special_price_start_date',
        'special_price_end_date',
        'is_featured',
        'is_bestseller',
        'is_new',
        'tax_class',
        'weight',
        'length',
        'width',
        'height',
        'weight_unit',
        'dimension_unit',
        'max_stock',
        'allow_backorder',
        'inventory_status',
        'brand_id',
        'vendor_id',
        'attributes',
        'tags',
        'warranty',
        'condition',
        'available_from',
        'available_to'
    ];

    protected $casts = [
        'metadata' => 'array',
        'dimension' => 'array',
        'attributes' => 'array',
        'tags' => 'array',
        'is_active' => 'boolean',
        'is_inventory_tracked' => 'boolean',
        'is_service' => 'boolean',
        'is_featured' => 'boolean',
        'is_bestseller' => 'boolean',
        'is_new' => 'boolean',
        'allow_backorder' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->uuid)) {
                $product->uuid = (string) Str::uuid();
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
