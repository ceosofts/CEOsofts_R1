<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
        'location',
        'image',
        'is_active',
        'is_inventory_tracked',
        'is_service',
        'dimension',
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
        'min_stock',
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
        'available_to',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'float',
        'cost' => 'float',
        'stock_quantity' => 'integer',
        'current_stock' => 'integer',
        'is_active' => 'boolean',
        'is_inventory_tracked' => 'boolean',
        'is_service' => 'boolean',
        'dimension' => 'json',
        'list_price' => 'float',
        'wholesale_price' => 'float',
        'special_price' => 'float',
        'special_price_start_date' => 'datetime',
        'special_price_end_date' => 'datetime',
        'is_featured' => 'boolean',
        'is_bestseller' => 'boolean',
        'is_new' => 'boolean',
        'weight' => 'float',
        'length' => 'float',
        'width' => 'float',
        'height' => 'float',
        'min_stock' => 'integer',
        'max_stock' => 'integer',
        'allow_backorder' => 'boolean',
        'available_from' => 'datetime',
        'available_to' => 'datetime',
        'metadata' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the company that owns the product.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the category of the product.
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Get the unit relation of the product.
     */
    public function unitRelation()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Get the stock movements for the product.
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get formatted price with currency symbol.
     */
    public function getFormattedPriceAttribute()
    {
        return '฿' . number_format($this->price, 2);
    }
    
    /**
     * Get formatted cost with currency symbol.
     */
    public function getFormattedCostAttribute()
    {
        return '฿' . number_format($this->cost, 2);
    }

    /**
     * Scope a query to only include products.
     */
    public function scopeOnlyProducts($query)
    {
        return $query->where('is_service', false);
    }

    /**
     * Scope a query to only include services.
     */
    public function scopeOnlyServices($query)
    {
        return $query->where('is_service', true);
    }
    
    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if product stock is low.
     */
    public function getIsLowStockAttribute()
    {
        if (!$this->is_inventory_tracked) {
            return false;
        }
        
        return $this->current_stock <= $this->min_stock;
    }
    
    /**
     * Get product specifications from metadata.
     */
    public function getSpecificationsAttribute()
    {
        if (is_string($this->metadata)) {
            $decoded = json_decode($this->metadata, true);
            return isset($decoded['specifications']) ? $decoded['specifications'] : [];
        }
        
        return isset($this->metadata['specifications']) ? $this->metadata['specifications'] : [];
    }
}
