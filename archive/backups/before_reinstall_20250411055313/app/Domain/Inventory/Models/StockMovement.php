<?php

namespace App\Domain\Inventory\Models;

use App\Domain\Organization\Models\Company;
use App\Domain\Settings\Models\User;
use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMovement extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'company_id',
        'product_id',
        'type',
        'quantity',
        'unit_price',
        'total_price',
        'before_quantity',
        'after_quantity',
        'reference_type',
        'reference_id',
        'location_from',
        'location_to',
        'processed_by',
        'processed_at',
        'notes',
        'metadata'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'before_quantity' => 'decimal:2',
        'after_quantity' => 'decimal:2',
        'processed_at' => 'datetime',
        'metadata' => 'json'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByReference($query, $type, $id = null)
    {
        $query->where('reference_type', $type);
        
        if ($id) {
            $query->where('reference_id', $id);
        }
        
        return $query;
    }

    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('processed_at', [$from, $to]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->processed_at) {
                $model->processed_at = now();
            }
            
            // คำนวณราคารวม
            if ($model->quantity && $model->unit_price) {
                $model->total_price = $model->quantity * $model->unit_price;
            }
        });

        static::created(function ($model) {
            // อัปเดตจำนวนสินค้าคงเหลือ
            $product = $model->product;
            if ($product) {
                $model->before_quantity = $product->current_stock;
                
                if ($model->type === 'receive') {
                    $product->current_stock += $model->quantity;
                } elseif ($model->type === 'issue') {
                    $product->current_stock -= $model->quantity;
                }
                
                $model->after_quantity = $product->current_stock;
                $model->saveQuietly();
                $product->save();
            }
        });
    }
}
