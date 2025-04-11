<?php

namespace App\Infrastructure\MultiTenancy\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MissingCompanyScopeException extends Exception
{
    /**
     * สร้าง Exception สำหรับกรณีที่ไม่มีการเลือกบริษัท
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = "Company scope required", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * รายงาน/บันทึก exception
     *
     * @return void
     */
    public function report(): void
    {
        // บันทึกข้อผิดพลาดถ้าจำเป็น
    }

    /**
     * แสดงผล exception ในรูปแบบ HTTP response
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function render(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response([
                'error' => 'company_required',
                'message' => 'กรุณาเลือกบริษัทก่อนดำเนินการ',
            ], 400);
        }

        // สำหรับการร้องขอ HTML ปกติ ให้เด้งไปหน้าเลือกบริษัท
        return response()->view('errors.company-required', [], 400);
    }
}
