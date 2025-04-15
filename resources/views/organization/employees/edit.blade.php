<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800">
                {{ __('แก้ไขข้อมูลพนักงาน') }}: {{ $employee->first_name }} {{ $employee->last_name }}
            </h2>
            <div>
                <a href="{{ route('employees.show', $employee) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('ยกเลิก') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif

            @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-bold mb-4 border-b-2 pb-2">ข้อมูลพื้นฐาน</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="profile_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">รูปโปรไฟล์</label>
                                <div class="mb-2">
                                    @if($employee->profile_image)
                                    <img src="{{ Storage::url($employee->profile_image) }}" alt="{{ $employee->first_name }}" class="w-32 h-32 object-cover rounded-md">
                                    @else
                                    <div class="w-32 h-32 rounded-md bg-gray-200 flex items-center justify-center">
                                        <span class="text-3xl font-bold text-gray-500">{{ substr($employee->first_name, 0, 1) }}</span>
                                    </div>
                                    @endif
                                </div>
                                <input type="file" name="profile_image" id="profile_image" class="block w-full text-sm text-gray-500">
                            </div>
                            <div>
                                <label for="employee_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">รหัสพนักงาน<span class="text-red-500">*</span></label>
                                <input type="text" name="employee_code" id="employee_code" value="{{ old('employee_code', $employee->employee_code) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">สถานะ<span class="text-red-500">*</span></label>
                                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="active" {{ $employee->status == 'active' ? 'selected' : '' }}>พนักงานปัจจุบัน</option>
                                    <option value="inactive" {{ $employee->status == 'inactive' ? 'selected' : '' }}>ไม่ได้ปฏิบัติงาน</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="company_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">บริษัท<span class="text-red-500">*</span></label>
                                <select name="company_id" id="company_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                    @foreach(\App\Models\Company::all() as $company)
                                    <option value="{{ $company->id }}" {{ $employee->company_id == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">แผนก<span class="text-red-500">*</span></label>
                                <select name="department_id" id="department_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                    @foreach(\App\Models\Department::where('company_id', $employee->company_id)->get() as $department)
                                    <option value="{{ $department->id }}" {{ $employee->department_id == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="position_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ตำแหน่ง<span class="text-red-500">*</span></label>
                                <select name="position_id" id="position_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                    @foreach(\App\Models\Position::where('company_id', $employee->company_id)->get() as $position)
                                    <option value="{{ $position->id }}" {{ $employee->position_id == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="branch_office_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">สาขา<span class="text-red-500">*</span></label>
                                <select name="branch_office_id" id="branch_office_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                    @foreach(\App\Models\BranchOffice::all() as $branch)
                                    <option value="{{ $branch->id }}" {{ $employee->branch_office_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="manager_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ผู้จัดการ</label>
                                <select name="manager_id" id="manager_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="">-- ไม่มีผู้จัดการ --</option>
                                    @foreach(\App\Models\Employee::where('company_id', $employee->company_id)->where('id', '!=', $employee->id)->get() as $manager)
                                    <option value="{{ $manager->id }}" {{ $employee->manager_id == $manager->id ? 'selected' : '' }}>{{ $manager->first_name }} {{ $manager->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="employee_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ประเภทพนักงาน</label>
                                <select name="employee_type" id="employee_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="">-- เลือกประเภทพนักงาน --</option>
                                    <option value="พนักงานประจำ" {{ $employee->employee_type == 'พนักงานประจำ' ? 'selected' : '' }}>พนักงานประจำ</option>
                                    <option value="พนักงานชั่วคราว" {{ $employee->employee_type == 'พนักงานชั่วคราว' ? 'selected' : '' }}>พนักงานชั่วคราว</option>
                                    <option value="พนักงานพาร์ทไทม์" {{ $employee->employee_type == 'พนักงานพาร์ทไทม์' ? 'selected' : '' }}>พนักงานพาร์ทไทม์</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-bold mb-4 border-b-2 pb-2">ข้อมูลส่วนตัว</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">คำนำหน้า</label>
                                <select name="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="">-- เลือกคำนำหน้า --</option>
                                    <option value="นาย" {{ $employee->title == 'นาย' ? 'selected' : '' }}>นาย</option>
                                    <option value="นาง" {{ $employee->title == 'นาง' ? 'selected' : '' }}>นาง</option>
                                    <option value="นางสาว" {{ $employee->title == 'นางสาว' ? 'selected' : '' }}>นางสาว</option>
                                    <option value="ดร." {{ $employee->title == 'ดร.' ? 'selected' : '' }}>ดร.</option>
                                </select>
                            </div>
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ชื่อ<span class="text-red-500">*</span></label>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $employee->first_name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">นามสกุล<span class="text-red-500">*</span></label>
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $employee->last_name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="nickname" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ชื่อเล่น</label>
                                <input type="text" name="nickname" id="nickname" value="{{ old('nickname', $employee->nickname) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">เพศ</label>
                                <select name="gender" id="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="">-- เลือกเพศ --</option>
                                    <option value="ชาย" {{ $employee->gender == 'ชาย' ? 'selected' : '' }}>ชาย</option>
                                    <option value="หญิง" {{ $employee->gender == 'หญิง' ? 'selected' : '' }}>หญิง</option>
                                    <option value="อื่นๆ" {{ $employee->gender == 'อื่นๆ' ? 'selected' : '' }}>อื่นๆ</option>
                                </select>
                            </div>
                            <div>
                                <label for="birthdate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">วันเกิด</label>
                                <input type="date" name="birthdate" id="birthdate" value="{{ old('birthdate', $employee->birthdate ? $employee->birthdate->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="id_card_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">เลขบัตรประชาชน</label>
                                <input type="text" name="id_card_number" id="id_card_number" value="{{ old('id_card_number', $employee->id_card_number) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="nationality" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">สัญชาติ</label>
                                <input type="text" name="nationality" id="nationality" value="{{ old('nationality', $employee->nationality) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="religion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ศาสนา</label>
                                <input type="text" name="religion" id="religion" value="{{ old('religion', $employee->religion) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="marital_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">สถานภาพ</label>
                                <select name="marital_status" id="marital_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="">-- เลือกสถานภาพ --</option>
                                    <option value="โสด" {{ $employee->marital_status == 'โสด' ? 'selected' : '' }}>โสด</option>
                                    <option value="สมรส" {{ $employee->marital_status == 'สมรส' ? 'selected' : '' }}>สมรส</option>
                                    <option value="หย่าร้าง" {{ $employee->marital_status == 'หย่าร้าง' ? 'selected' : '' }}>หย่าร้าง</option>
                                    <option value="หม้าย" {{ $employee->marital_status == 'หม้าย' ? 'selected' : '' }}>หม้าย</option>
                                </select>
                            </div>
                            <div>
                                <label for="blood_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">กรุ๊ปเลือด</label>
                                <select name="blood_type" id="blood_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="">-- เลือกกรุ๊ปเลือด --</option>
                                    <option value="A" {{ $employee->blood_type == 'A' ? 'selected' : '' }}>A</option>
                                    <option value="B" {{ $employee->blood_type == 'B' ? 'selected' : '' }}>B</option>
                                    <option value="AB" {{ $employee->blood_type == 'AB' ? 'selected' : '' }}>AB</option>
                                    <option value="O" {{ $employee->blood_type == 'O' ? 'selected' : '' }}>O</option>
                                </select>
                            </div>
                            <div>
                                <label for="medical_conditions" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">โรคประจำตัว</label>
                                <input type="text" name="medical_conditions" id="medical_conditions" value="{{ old('medical_conditions', $employee->medical_conditions) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="height" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ส่วนสูง (ซม.)</label>
                                <input type="number" step="0.01" name="height" id="height" value="{{ old('height', $employee->height) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">น้ำหนัก (กก.)</label>
                                <input type="number" step="0.01" name="weight" id="weight" value="{{ old('weight', $employee->weight) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ที่อยู่</label>
                            <textarea name="address" id="address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">{{ old('address', $employee->address) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-bold mb-4 border-b-2 pb-2">ข้อมูลการติดต่อ</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">อีเมล<span class="text-red-500">*</span></label>
                                <input type="email" name="email" id="email" value="{{ old('email', $employee->email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">โทรศัพท์</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $employee->phone) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="has_company_email" class="flex items-center">
                                    <input type="checkbox" name="has_company_email" id="has_company_email" value="1" {{ $employee->has_company_email ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">มีอีเมลบริษัท</span>
                                </label>
                            </div>
                            <div>
                                <label for="company_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">อีเมลบริษัท</label>
                                <input type="email" name="company_email" id="company_email" value="{{ old('company_email', $employee->company_email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ชื่อผู้ติดต่อฉุกเฉิน</label>
                                <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">เบอร์โทรผู้ติดต่อฉุกเฉิน</label>
                                <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-bold mb-4 border-b-2 pb-2">ข้อมูลการทำงาน</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="hire_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">วันที่เริ่มงาน</label>
                                <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="probation_end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">วันสิ้นสุดทดลองงาน</label>
                                <input type="date" name="probation_end_date" id="probation_end_date" value="{{ old('probation_end_date', $employee->probation_end_date ? $employee->probation_end_date->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="termination_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">วันที่สิ้นสุดการทำงาน</label>
                                <input type="date" name="termination_date" id="termination_date" value="{{ old('termination_date', $employee->termination_date ? $employee->termination_date->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-bold mb-4 border-b-2 pb-2">ข้อมูลการศึกษาและประสบการณ์</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="education_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ระดับการศึกษา</label>
                                <select name="education_level" id="education_level" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="">-- เลือกระดับการศึกษา --</option>
                                    <option value="ปริญญาเอก" {{ $employee->education_level == 'ปริญญาเอก' ? 'selected' : '' }}>ปริญญาเอก</option>
                                    <option value="ปริญญาโท" {{ $employee->education_level == 'ปริญญาโท' ? 'selected' : '' }}>ปริญญาโท</option>
                                    <option value="ปริญญาตรี" {{ $employee->education_level == 'ปริญญาตรี' ? 'selected' : '' }}>ปริญญาตรี</option>
                                    <option value="อนุปริญญา" {{ $employee->education_level == 'อนุปริญญา' ? 'selected' : '' }}>อนุปริญญา</option>
                                    <option value="ปวส." {{ $employee->education_level == 'ปวส.' ? 'selected' : '' }}>ปวส.</option>
                                    <option value="ปวช." {{ $employee->education_level == 'ปวช.' ? 'selected' : '' }}>ปวช.</option>
                                    <option value="มัธยมศึกษาตอนปลาย" {{ $employee->education_level == 'มัธยมศึกษาตอนปลาย' ? 'selected' : '' }}>มัธยมศึกษาตอนปลาย</option>
                                    <option value="มัธยมศึกษาตอนต้น" {{ $employee->education_level == 'มัธยมศึกษาตอนต้น' ? 'selected' : '' }}>มัธยมศึกษาตอนต้น</option>
                                    <option value="ประถมศึกษา" {{ $employee->education_level == 'ประถมศึกษา' ? 'selected' : '' }}>ประถมศึกษา</option>
                                </select>
                            </div>
                            <div>
                                <label for="education_institute" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">สถาบันการศึกษา</label>
                                <input type="text" name="education_institute" id="education_institute" value="{{ old('education_institute', $employee->education_institute) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="education_major" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">สาขาวิชา</label>
                                <input type="text" name="education_major" id="education_major" value="{{ old('education_major', $employee->education_major) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="years_experience" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ประสบการณ์ทำงาน (ปี)</label>
                                <input type="number" name="years_experience" id="years_experience" value="{{ old('years_experience', $employee->years_experience) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="skills" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ทักษะ</label>
                                <input type="text" name="skills" id="skills" value="{{ old('skills', $employee->skills) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="certificates" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ประกาศนียบัตร</label>
                                <input type="text" name="certificates" id="certificates" value="{{ old('certificates', $employee->certificates) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label for="previous_employment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">การทำงานที่ผ่านมา</label>
                            <textarea name="previous_employment" id="previous_employment" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">{{ old('previous_employment', $employee->previous_employment) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-bold mb-4 border-b-2 pb-2">ข้อมูลการเงิน</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ธนาคาร</label>
                                <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $employee->bank_name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="bank_account" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">เลขที่บัญชี</label>
                                <input type="text" name="bank_account" id="bank_account" value="{{ old('bank_account', $employee->bank_account) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="tax_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">เลขประจำตัวผู้เสียภาษี</label>
                                <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id', $employee->tax_id) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="tax_filing_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">สถานะการยื่นภาษี</label>
                                <input type="text" name="tax_filing_status" id="tax_filing_status" value="{{ old('tax_filing_status', $employee->tax_filing_status) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="social_security_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">เลขที่ประกันสังคม</label>
                                <input type="text" name="social_security_number" id="social_security_number" value="{{ old('social_security_number', $employee->social_security_number) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-bold mb-4 border-b-2 pb-2">ข้อมูลเอกสาร</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="passport_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">เลขที่หนังสือเดินทาง</label>
                                <input type="text" name="passport_number" id="passport_number" value="{{ old('passport_number', $employee->passport_number) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="passport_expiry" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">วันหมดอายุหนังสือเดินทาง</label>
                                <input type="date" name="passport_expiry" id="passport_expiry" value="{{ old('passport_expiry', $employee->passport_expiry ? $employee->passport_expiry->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="work_permit_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">เลขที่ใบอนุญาตทำงาน</label>
                                <input type="text" name="work_permit_number" id="work_permit_number" value="{{ old('work_permit_number', $employee->work_permit_number) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="work_permit_expiry" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">วันหมดอายุใบอนุญาตทำงาน</label>
                                <input type="date" name="work_permit_expiry" id="work_permit_expiry" value="{{ old('work_permit_expiry', $employee->work_permit_expiry ? $employee->work_permit_expiry->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="visa_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ประเภทวีซ่า</label>
                                <input type="text" name="visa_type" id="visa_type" value="{{ old('visa_type', $employee->visa_type) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="visa_expiry" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">วันหมดอายุวีซ่า</label>
                                <input type="date" name="visa_expiry" id="visa_expiry" value="{{ old('visa_expiry', $employee->visa_expiry ? $employee->visa_expiry->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-bold mb-4 border-b-2 pb-2">ข้อมูลเพิ่มเติม (Metadata)</h3>
                        
                        @if($employee->metadata && is_array($employee->metadata))
                        <div class="mb-6">
                            @foreach($employee->metadata as $key => $value)
                            <div class="mb-4">
                                <label for="metadata_{{ $key }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ ucfirst(str_replace('_', ' ', $key)) }}</label>
                                <input type="text" name="metadata[{{ $key }}]" id="metadata_{{ $key }}" value="{{ old('metadata.'.$key, $value) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            @endforeach
                        </div>
                        @endif
                        
                        <div>
                            <h4 class="mb-2 font-medium text-sm text-gray-600 dark:text-gray-400">เพิ่มข้อมูลใหม่</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="new_metadata_key" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ชื่อข้อมูล</label>
                                    <input type="text" name="new_metadata_key" id="new_metadata_key" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                </div>
                                <div>
                                    <label for="new_metadata_value" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">มูลค่า</label>
                                    <input type="text" name="new_metadata_value" id="new_metadata_value" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('employees.show', $employee) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ __('ยกเลิก') }}
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ __('บันทึกข้อมูล') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
