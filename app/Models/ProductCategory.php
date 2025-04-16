<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Shared\Traits\HasCompanyScope;

class ProductCategory extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'company_id',
        'is_active',
        'parent_id',
        'metadata',
        'slug',
        'level',
        'path',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'metadata' => 'json',
    ];

    /**
     * Get the company that owns the product category.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the parent product category.
     */
    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    /**
     * Get the child product categories.
     */
    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    /**
     * Get the products for the category.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    /**
     * Get category code attribute alias
     */
    public function getCategoryCodeAttribute()
    {
        return $this->code;
    }

    /**
     * Get category name attribute alias
     */
    public function getCategoryNameAttribute()
    {
        return $this->name;
    }

    /**
     * Get the formatted code for the product category.
     *
     * @return string
     */
    public function getFormattedCodeAttribute()
    {
        $id = str_pad($this->id, 3, '0', STR_PAD_LEFT);
        $companyId = $this->company_id;
        $code = $this->code ?: 'XXX';
        
        return "PC-{$id}-{$companyId}-{$code}";
    }
}
