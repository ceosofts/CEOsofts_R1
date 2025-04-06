<?php

namespace App\Domain\Settings\Models;

use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'key',
        'value',
        'type',
        'group',
        'editable',
        'description',
        'options',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'value' => 'json',
        'options' => 'json',
        'editable' => 'boolean',
    ];

    /**
     * Get the company that owns this setting.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope a query to only include settings in a specific group.
     */
    public function scopeInGroup($query, $group)
    {
        return $query->where('group', $group);
    }
    
    /**
     * Scope a query to only include editable settings.
     */
    public function scopeEditable($query)
    {
        return $query->where('editable', true);
    }
    
    /**
     * Get a specific setting value by key for a company.
     *
     * @param int $companyId
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue($companyId, $key, $default = null)
    {
        $setting = self::where('company_id', $companyId)
                        ->where('key', $key)
                        ->first();
                        
        return $setting ? $setting->value : $default;
    }
    
    /**
     * Set a specific setting value by key for a company.
     *
     * @param int $companyId
     * @param string $key
     * @param mixed $value
     * @param string|null $group
     * @return Setting
     */
    public static function setValue($companyId, $key, $value, $group = null)
    {
        $setting = self::firstOrNew([
            'company_id' => $companyId,
            'key' => $key,
        ]);
        
        $setting->value = $value;
        
        if ($group && !$setting->exists) {
            $setting->group = $group;
        }
        
        $setting->save();
        
        return $setting;
    }
    
    /**
     * Get all settings for a specific company, optionally filtered by group.
     *
     * @param int $companyId
     * @param string|null $group
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllForCompany($companyId, $group = null)
    {
        $query = self::where('company_id', $companyId);
        
        if ($group) {
            $query->where('group', $group);
        }
        
        return $query->get();
    }
    
    /**
     * Convert to an appropriate PHP value based on the setting type.
     *
     * @return mixed
     */
    public function getConvertedValueAttribute()
    {
        if ($this->type === 'boolean') {
            return (bool) $this->value;
        } elseif ($this->type === 'integer') {
            return (int) $this->value;
        } elseif ($this->type === 'float') {
            return (float) $this->value;
        } elseif ($this->type === 'json') {
            return json_decode($this->value, true);
        }
        
        return $this->value;
    }
}
