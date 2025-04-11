<?php

namespace App\Domain\DocumentGeneration\Models;

use App\Domain\Organization\Models\Company;
use App\Domain\Settings\Models\User;
use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class GeneratedDocument extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'template_id',
        'document_type',
        'document_id',
        'filename',
        'disk',
        'path',
        'mime_type',
        'file_size',
        'is_signed',
        'signature_data',
        'is_sent',
        'sent_at',
        'sent_to',
        'sent_by',
        'created_by',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'signature_data' => 'json',
        'metadata' => 'json',
        'is_signed' => 'boolean',
        'is_sent' => 'boolean',
        'sent_at' => 'datetime',
        'file_size' => 'integer',
    ];

    /**
     * Get the company that owns this document.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the template used to generate this document.
     */
    public function template()
    {
        return $this->belongsTo(DocumentTemplate::class, 'template_id');
    }

    /**
     * Get the user who created this document.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Get the file URL.
     *
     * @return string
     */
    public function getFileUrlAttribute()
    {
        return Storage::disk($this->disk)->url($this->path);
    }
    
    /**
     * Get the file contents.
     *
     * @return string|null
     */
    public function getContents()
    {
        if (Storage::disk($this->disk)->exists($this->path)) {
            return Storage::disk($this->disk)->get($this->path);
        }
        
        return null;
    }
    
    /**
     * Download the file.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|null
     */
    public function download()
    {
        if (Storage::disk($this->disk)->exists($this->path)) {
            return Storage::disk($this->disk)->download(
                $this->path, 
                $this->filename, 
                ['Content-Type' => $this->mime_type ?? 'application/pdf']
            );
        }
        
        return null;
    }
    
    /**
     * Delete the file from storage.
     *
     * @return bool
     */
    public function deleteFile()
    {
        if (Storage::disk($this->disk)->exists($this->path)) {
            return Storage::disk($this->disk)->delete($this->path);
        }
        
        return false;
    }
    
    /**
     * Scope a query to only include documents of a specific type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('document_type', $type);
    }
    
    /**
     * Mark this document as sent.
     *
     * @param string|null $sentTo
     * @param string|null $sentBy
     * @return bool
     */
    public function markAsSent($sentTo = null, $sentBy = null)
    {
        $this->is_sent = true;
        $this->sent_at = now();
        $this->sent_to = $sentTo;
        $this->sent_by = $sentBy;
        
        return $this->save();
    }
    
    /**
     * Set document as signed with signature data.
     *
     * @param array $signatureData
     * @return bool
     */
    public function sign(array $signatureData)
    {
        $this->is_signed = true;
        $this->signature_data = $signatureData;
        
        return $this->save();
    }
}
