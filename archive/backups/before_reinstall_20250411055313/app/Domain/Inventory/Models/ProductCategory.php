<?php

namespace App\Domain\Inventory\Models;

use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ProductCategory extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'description',
        'parent_id',
        'slug',
        'is_active',
        'level',
        'path',
        'metadata'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'level' => 'integer',
        'metadata' => 'json'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->slug = $model->slug ?? Str::slug($model->name);
            
            if ($model->parent_id) {
                $parent = static::find($model->parent_id);
                $model->level = $parent->level + 1;
                $model->path = $parent->path . '/' . $model->id;
            } else {
                $model->level = 0;
                $model->path = (string) $model->id;
            }
        });
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function getAllChildren()
    {
        return $this->children()->with('children');
    }

    public function getAncestors()
    {
        $ancestors = collect();
        $category = $this;

        while ($category->parent) {
            $ancestors->push($category->parent);
            $category = $category->parent;
        }

        return $ancestors->reverse();
    }

    public function getBreadcrumbAttribute()
    {
        return $this->getAncestors()->pluck('name')->push($this->name)->implode(' > ');
    }
}
