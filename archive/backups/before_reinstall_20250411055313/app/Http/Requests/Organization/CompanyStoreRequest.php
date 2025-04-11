<?php

namespace App\Http\Requests\Organization;

use Illuminate\Foundation\Http\FormRequest;

class CompanyStoreRequest extends FormRequest
{
    /**
     * ตรวจสอบว่าผู้ใช้มีสิทธิ์ใช้ฟอร์มนี้หรือไม่
     */
    public function authorize(): bool
    {
        return true; // แก้ไขเป็นการตรวจสอบสิทธิ์จริงในระบบ
    }

    /**
     * กฎการตรวจสอบข้อมูล
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'tax_id' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|max:2048', // รูปภาพขนาดไม่เกิน 2MB
            'is_active' => 'nullable|boolean',
            'settings' => 'nullable|array',
            'create_default_departments' => 'nullable|boolean',
        ];
    }

    /**
     * ข้อความแสดงข้อผิดพลาด
     */
    public function messages(): array
    {
        return [
            'name.required' => 'กรุณาระบุชื่อบริษัท',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'website.url' => 'รูปแบบเว็บไซต์ไม่ถูกต้อง',
            'logo.image' => 'โลโก้ต้องเป็นรูปภาพเท่านั้น',
            'logo.max' => 'โลโก้ต้องมีขนาดไม่เกิน 2MB',
        ];
    }
}
