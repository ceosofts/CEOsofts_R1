<?php

namespace App\Domain\DocumentGeneration\Models;

use App\Domain\Organization\Models\Company;
use App\Domain\Settings\Models\User;
use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentTemplate extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'type',
        'layout',
        'header',
        'footer',
        'css',
        'orientation',
        'paper_size',
        'is_default',
        'is_active',
        'created_by',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'layout' => 'json',
        'header' => 'json',
        'footer' => 'json',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'metadata' => 'json',
    ];

    /**
     * Get the company that owns this template.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who created this template.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the generated documents using this template.
     */
    public function generatedDocuments()
    {
        return $this->hasMany(GeneratedDocument::class, 'template_id');
    }
    
    /**
     * Scope a query to only include templates of a specific type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
    
    /**
     * Scope a query to only include default templates.
     */
    public function scopeDefaultTemplates($query)
    {
        return $query->where('is_default', true);
    }
    
    /**
     * Set this template as the default for its type.
     * This will unset any other default template of the same type.
     */
    public function setAsDefault()
    {
        // Begin transaction
        \DB::beginTransaction();
        
        try {
            // Unset other default templates of the same type
            self::where('company_id', $this->company_id)
                ->where('type', $this->type)
                ->where('id', '!=', $this->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
            
            // Set this one as default
            $this->is_default = true;
            $this->save();
            
            // Commit transaction
            \DB::commit();
            
            return true;
        } catch (\Exception $e) {
            // Rollback in case of error
            \DB::rollBack();
            return false;
        }
    }
}
