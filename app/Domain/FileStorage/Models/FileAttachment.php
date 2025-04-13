<?php

namespace App\Domain\FileStorage\Models;

use App\Domain\Organization\Models\Company;
use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileAttachment extends Model
{
    use HasFactory, SoftDeletes, HasCompanyScope;

    /**
     * ชื่อตารางในฐานข้อมูล
     */
    protected $table = 'file_attachments';

    /**
     * คุณสมบัติที่สามารถกำหนดค่าได้
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
     * คุณสมบัติที่ควรแปลงเป็นชนิดข้อมูลต่างๆ
     */
    protected $casts = [
        'metadata' => 'json',
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * ความสัมพันธ์กับ Company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * ความสัมพันธ์กับ model ที่แนบไฟล์นี้
     */
    public function attachable()
    {
        return $this->morphTo();
    }

    /**
     * ความสัมพันธ์กับผู้ใช้ที่สร้างไฟล์นี้
     */
    public function createdBy()
    {
        return $this->belongsTo(\App\Domain\Settings\Models\User::class, 'created_by');
    }

    /**
     * สร้าง directory สำหรับโฟลเดอร์ที่จะจัดเก็บไฟล์
     */
    public static function ensureDirectoryExists($path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
    }

    /**
     * ดึงข้อมูล URL ของไฟล์
     */
    public function getUrlAttribute()
    {
        return \Storage::disk($this->disk)->url($this->file_path);
    }
}
