<?php

namespace App\Shared\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Models\Company;

trait HasCompanyScope
{
    // ยกเลิกการเพิ่ม global scope โดยสิ้นเชิง
    // ฟังก์ชันนี้จะไม่ทำอะไร - ปิดการใช้งาน scope
    protected static function bootHasCompanyScope()
    {
        // ไม่มีการเพิ่ม global scope
    }

    // คงไว้เพื่อความเข้ากันได้กับโค้ดที่เรียกใช้ method นี้
    public function scopeAllCompanies($query)
    {
        return $query;
    }

    // คงไว้เพื่อความเข้ากันได้กับโค้ดที่เรียกใช้ relationship นี้
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
