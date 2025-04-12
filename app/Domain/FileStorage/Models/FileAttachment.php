<?php

namespace App\Domain\FileStorage\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domain\Organization\Models\Company;
use App\Domain\Settings\Models\User;
use App\Domain\Shared\Traits\HasCompanyScope;

class FileAttachment extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string, mixed>
     */
    protected $fillable = [
        'company_id',
        'attachable_type',
        'attachable_id',
        'filename',
        'original_filename',
        'disk',
        'file_path',
        'mime_type',
        'file_size',
        'created_by',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, mixed>
     */
    protected $casts = [
        'metadata' => 'json',
        'file_size' => 'integer',
    ];

    /**
     * Get the company that owns the file attachment.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user that created the file attachment.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the parent attachable model.
     */
    public function attachable()
    {
        return $this->morphTo();
    }

    /**
     * Get the full URL for the file.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url(\Storage::disk($this->disk)->url($this->file_path));
    }

    /**
     * Get the formatted file size.
     *
     * @return string
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;

        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Scope query to files of a specific mime type.
     */
    public function scopeOfMimeType($query, $type)
    {
        return $query->where('mime_type', 'like', $type . '%');
    }

    /**
     * Scope query to files of specific attachable type.
     */
    public function scopeOfAttachableType($query, $type)
    {
        return $query->where('attachable_type', $type);
    }
}
