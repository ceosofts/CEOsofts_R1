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
        'movement_type',
        'reference_type',
        'reference_id',
        'quantity',
        'before_quantity',
        'after_quantity',
        'unit_cost',
        'total_cost',
        'location',
        'notes',
        'processed_by',
        'processed_at',
        'status',
        'metadata',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'before_quantity' => 'decimal:2',
        'after_quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'processed_at' => 'datetime',
        'metadata' => 'json',
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

            // ถ้าไม่ได้กำหนดค่า before_quantity ให้กำหนดเป็น 0
            if ($model->before_quantity === null) {
                $model->before_quantity = 0;
            }
        });

        static::created(function ($model) {
            // อัปเดตจำนวนสินค้าคงเหลือ
            $product = $model->product;
            if ($product) {
                // แก้ไขโดยกำหนดค่าเริ่มต้นถ้า current_stock เป็น null
                $model->before_quantity = $product->current_stock ?? 0;

                if ($model->type === 'receive') {
                    $product->current_stock = ($product->current_stock ?? 0) + $model->quantity;
                } elseif ($model->type === 'issue') {
                    $product->current_stock = ($product->current_stock ?? 0) - $model->quantity;
                }

                $model->after_quantity = $product->current_stock ?? 0;
                $model->saveQuietly();
                $product->save();
            }
        });

        static::updating(function ($model) {
            // ป้องกันการตั้งค่า before_quantity เป็น null
            if ($model->before_quantity === null) {
                $model->before_quantity = 0;
            }
        });
    }
}
