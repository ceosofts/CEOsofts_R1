<?php

namespace App\Domain\Settings\Models;

use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileAttachment extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'attachable_type',
        'attachable_id',
        'name',
        'original_name',
        'disk',
        'path',
        'mime_type',
        'size',
        'created_by',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'metadata' => 'json',
        'size' => 'integer',
    ];

    /**
     * Get the company that owns this file.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the parent attachable model.
     */
    public function attachable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user who uploaded this file.
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
    public function getUrlAttribute()
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * Get the file extension.
     *
     * @return string
     */
    public function getExtensionAttribute()
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    /**
     * Get formatted file size.
     *
     * @return string
     */
    public function getFormattedSizeAttribute()
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $size = $this->size;
        $i = 0;
        
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        
        return round($size, 2) . ' ' . $units[$i];
    }

    /**
     * Check if file is an image.
     *
     * @return bool
     */
    public function getIsImageAttribute()
    {
        return Str::startsWith($this->mime_type, 'image/');
    }

    /**
     * Check if file is a document (PDF, doc, etc.).
     *
     * @return bool
     */
    public function getIsDocumentAttribute()
    {
        $documentTypes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ];
        
        return in_array($this->mime_type, $documentTypes);
    }

    /**
     * Download the file.
     *
     * @param string|null $filename
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|null
     */
    public function download($filename = null)
    {
        if (Storage::disk($this->disk)->exists($this->path)) {
            return Storage::disk($this->disk)->download(
                $this->path,
                $filename ?: $this->original_name,
                ['Content-Type' => $this->mime_type]
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
     * Handle model deleted event.
     */
    protected static function booted()
    {
        parent::boot();
        
        static::deleting(function ($fileAttachment) {
            // When model is being deleted, also delete the physical file
            if (!$fileAttachment->isForceDeleting()) {
                return;
            }
            
            $fileAttachment->deleteFile();
        });
    }
}
