<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array
     */
    protected $except = [
        //
    ];

    /**
     * แทนที่วิธีการเข้ารหัสคุกกี้ด้วยวิธีที่แก้ไขแล้ว
     * 
     * @param  string  $value
     * @return string
     */
    protected function encryptCookie($value)
    {
        try {
            return parent::encryptCookie($value);
        } catch (\Exception $e) {
            // ถ้าเกิดข้อผิดพลาด ให้ใช้วิธีเข้ารหัสอื่น
            if (function_exists('custom_encrypt')) {
                return custom_encrypt($value, false);
            } 
            
            // หรือใช้ base64 แทนถ้าไม่ต้องการเข้ารหัส
            return 'base64:' . base64_encode($value);
        }
    }

    /**
     * แทนที่วิธีการถอดรหัสคุกกี้ด้วยวิธีที่แก้ไขแล้ว
     * 
     * @param  string  $value
     * @return string
     */
    protected function decryptCookie($value)
    {
        try {
            return parent::decryptCookie($value);
        } catch (\Exception $e) {
            // ถ้าเกิดข้อผิดพลาด ให้ตรวจสอบว่าเป็นรูปแบบ base64 หรือไม่
            if (strpos($value, 'base64:') === 0) {
                return base64_decode(substr($value, 7));
            }
            
            // ลองใช้ custom_decrypt ถ้ามี
            if (function_exists('custom_decrypt')) {
                try {
                    return custom_decrypt($value, false);
                } catch (\Exception $e) {
                    // ถ้ายังมีข้อผิดพลาด ให้คืนค่าเดิม
                    return $value;
                }
            }
            
            // ถ้าไม่มีทางเลือกอื่น ให้คืนค่าเดิม
            return $value;
        }
    }
}
