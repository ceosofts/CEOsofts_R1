<?php

namespace App\Domain\Settings\Models;

use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Translation extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'locale',
        'group',
        'key',
        'field',  // เพิ่มคอลัมน์ field
        'value',
        'translatable_type',
        'translatable_id',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'metadata' => 'json',
        'translatable_id' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // ตั้งค่าเริ่มต้นสำหรับ field
            if (empty($model->field)) {
                $model->field = 'general';
            }
            
            // ตั้งค่าเริ่มต้นสำหรับ translatable_type
            if (empty($model->translatable_type)) {
                $model->translatable_type = 'general';
            }
            
            // ตั้งค่าเริ่มต้นสำหรับ translatable_id
            if ($model->translatable_id === null) {
                $model->translatable_id = 0;
            }
        });
    }

    /**
     * Get the company that owns the translation.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the polymorphic relation.
     */
    public function translatable()
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include translations for a specific locale.
     */
    public function scopeLocale($query, $locale)
    {
        return $query->where('locale', $locale);
    }

    /**
     * Scope a query to only include translations for a specific group.
     */
    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Scope a query to only include translations for a specific field.
     */
    public function scopeField($query, $field)
    {
        return $query->where('field', $field);
    }

    /**
     * Scope a query to only include translations of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('translatable_type', $type);
    }

    /**
     * Get translations filtered by group and locale.
     *
     * @param int $companyId
     * @param string $locale
     * @param string $group
     * @return \Illuminate\Support\Collection
     */
    public static function getTranslations($companyId, $locale, $group)
    {
        return self::where('company_id', $companyId)
            ->where('locale', $locale)
            ->where('group', $group)
            ->get()
            ->pluck('value', 'key');
    }

    /**
     * Get all translations for a company as a structured array.
     *
     * @param int $companyId
     * @return array
     */
    public static function getAllTranslationsForCompany($companyId)
    {
        $translations = self::where('company_id', $companyId)->get();
        
        $result = [];
        foreach ($translations as $translation) {
            $result[$translation->locale][$translation->group][$translation->key] = $translation->value;
        }
        
        return $result;
    }

    /**
     * Get a translation value or fallback to default locale.
     *
     * @param int $companyId
     * @param string $locale
     * @param string $group
     * @param string $key
     * @param string|null $fallbackLocale
     * @return string|null
     */
    public static function getTranslation($companyId, $locale, $group, $key, $fallbackLocale = null)
    {
        // Try to get the translation in the requested locale
        $translation = self::where('company_id', $companyId)
            ->where('locale', $locale)
            ->where('group', $group)
            ->where('key', $key)
            ->first();
        
        if ($translation) {
            return $translation->value;
        }
        
        // If not found and fallback locale is provided, try with fallback
        if ($fallbackLocale && $fallbackLocale !== $locale) {
            $fallbackTranslation = self::where('company_id', $companyId)
                ->where('locale', $fallbackLocale)
                ->where('group', $group)
                ->where('key', $key)
                ->first();
            
            if ($fallbackTranslation) {
                return $fallbackTranslation->value;
            }
        }
        
        // Return the key as a last resort
        return $key;
    }

    /**
     * Update or create a translation.
     *
     * @param int $companyId
     * @param string $locale
     * @param string $group
     * @param string $key
     * @param string $value
     * @param string $field
     * @param string $type
     * @return Translation
     */
    public static function updateOrCreateTranslation($companyId, $locale, $group, $key, $value, $field = 'general', $type = 'general')
    {
        return self::updateOrCreate([
            'company_id' => $companyId,
            'locale' => $locale,
            'group' => $group,
            'key' => $key,
        ], [
            'field' => $field,
            'value' => $value,
            'translatable_type' => $type,
            'translatable_id' => 0,
        ]);
    }
}
