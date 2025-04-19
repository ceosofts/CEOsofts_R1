<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

trait CompanyScope
{
    // ไม่มีการเรียกใช้ addGlobalScope ใดๆ ในส่วนนี้
    protected static function bootCompanyScope()
    {
        // ปิดการใช้งาน global scope company ทั้งหมด
    }

    // เพิ่มความสัมพันธ์กับบริษัทโดยไม่ใช้ scope
    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}
