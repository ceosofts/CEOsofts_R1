<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employee_code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('employees')->ignore($this->employee),
            ],
            'title' => 'nullable|string|max:20',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:100',
            'gender' => 'nullable|string|in:male,female,other',
            'birthdate' => 'nullable|date',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'company_id' => 'required|exists:companies,id',
            'branch_office_id' => 'required|exists:branch_offices,id',
            'department_id' => 'required|exists:departments,id',
            'position_id' => 'required|exists:positions,id',
            'manager_id' => [
                'nullable',
                'exists:employees,id',
                function ($attribute, $value, $fail) {
                    if ($value == $this->employee->id) {
                        $fail('ไม่สามารถกำหนดให้ตนเองเป็นหัวหน้าได้');
                    }
                },
            ],
            'hire_date' => 'required|date',
            'probation_end_date' => 'nullable|date',
            'termination_date' => 'nullable|date',
            'employee_type' => 'nullable|string|in:full_time,part_time,contract,temporary,intern',
            'status' => 'required|string|in:active,inactive',
            'id_card_number' => 'nullable|string|max:20',
            'tax_id' => 'nullable|string|max:20',
            'social_security_number' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:20',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'company_email' => 'nullable|email|max:255',
            'has_company_email' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'employee_code' => 'รหัสพนักงาน',
            'title' => 'คำนำหน้า',
            'first_name' => 'ชื่อ',
            'last_name' => 'นามสกุล',
            'nickname' => 'ชื่อเล่น',
            'gender' => 'เพศ',
            'birthdate' => 'วันเกิด',
            'email' => 'อีเมลส่วนตัว',
            'phone' => 'เบอร์โทรศัพท์',
            'address' => 'ที่อยู่',
            'company_id' => 'บริษัท',
            'branch_office_id' => 'สำนักงาน/สาขา',
            'department_id' => 'แผนก',
            'position_id' => 'ตำแหน่ง',
            'manager_id' => 'หัวหน้า/ผู้จัดการ',
            'hire_date' => 'วันที่เริ่มทำงาน',
            'probation_end_date' => 'วันที่สิ้นสุดทดลองงาน',
            'termination_date' => 'วันที่ลาออก',
            'employee_type' => 'ประเภทพนักงาน',
            'status' => 'สถานะการทำงาน',
            'id_card_number' => 'เลขบัตรประชาชน',
            'tax_id' => 'เลขประจำตัวผู้เสียภาษี',
            'social_security_number' => 'เลขประกันสังคม',
            'bank_name' => 'ธนาคาร',
            'bank_account' => 'เลขที่บัญชีธนาคาร',
            'emergency_contact_name' => 'ชื่อผู้ติดต่อฉุกเฉิน',
            'emergency_contact_phone' => 'เบอร์โทรผู้ติดต่อฉุกเฉิน',
            'profile_image' => 'รูปประจำตัว',
            'company_email' => 'อีเมลบริษัท',
            'has_company_email' => 'มีอีเมลบริษัท',
        ];
    }
}
