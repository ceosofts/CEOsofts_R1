<?php

namespace App\Domain\Inventory\Models;

use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'uuid',
        'company_id',
        'category_id',
        'unit_id',
        'tax_id',
        'code',
        'name',
        'description',
        'sku',
        'barcode',
        'price',
        'cost',
        'min_stock',
        'max_stock',
        'current_stock',
        'is_active',
        'is_sellable',
        'is_purchasable',
        'metadata'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'min_stock' => 'decimal:2',
        'max_stock' => 'decimal:2',
        'current_stock' => 'decimal:2',
        'is_active' => 'boolean',
        'is_sellable' => 'boolean',
        'is_purchasable' => 'boolean',
        'metadata' => 'json'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ?? (string) Str::uuid();
            if (empty($model->sku)) {
                $model->sku = Str::upper(Str::random(8));
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

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSellable($query)
    {
        return $query->where('is_sellable', true);
    }

    public function scopePurchasable($query)
    {
        return $query->where('is_purchasable', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('current_stock', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('current_stock <= min_stock');
    }

    public function updateStock($quantity, $type = 'addition')
    {
        $this->current_stock = $type === 'addition' 
            ? $this->current_stock + $quantity
            : $this->current_stock - $quantity;
        
        return $this->save();
    }

    public function getPriceWithTaxAttribute()
    {
        if ($this->tax) {
            return $this->price * (1 + ($this->tax->rate / 100));
        }
        return $this->price;
    }

    public function getStockStatusAttribute()
    {
        if ($this->current_stock <= $this->min_stock) {
            return 'low';
        } elseif ($this->current_stock >= $this->max_stock) {
            return 'over';
        }
        return 'normal';
    }
}
