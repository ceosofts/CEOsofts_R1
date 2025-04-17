<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800">
                {{ __('เพิ่มพนักงานใหม่') }}
            </h2>
            <div>
                <a href="{{ route('employees.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('กลับไปยังรายการพนักงาน') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">เกิดข้อผิดพลาด!</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data">
                @csrf
                
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-bold mb-4 border-b-2 pb-2">ข้อมูลพื้นฐาน</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="profile_image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">รูปโปรไฟล์</label>
                                <input type="file" name="profile_image" id="profile_image" class="block w-full text-sm text-gray-500">
                            </div>
                            <div>
                                <label for="employee_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">รหัสพนักงาน</label>
                                <input type="text" name="employee_code" id="employee_code" value="{{ old('employee_code') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" placeholder="ระบบจะสร้างรหัสอัตโนมัติถ้าไม่กรอก">
                                <div id="employee_code_preview" class="mt-1 text-sm text-blue-600">กรุณาเลือกบริษัทเพื่อดูรหัสพนักงานที่จะใช้</div>
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">สถานะ<span class="text-red-500">*</span></label>
                                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>พนักงานปัจจุบัน</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>ไม่ได้ปฏิบัติงาน</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="company_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">บริษัท<span class="text-red-500">*</span></label>
                                <select name="company_id" id="company_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                    <option value="">-- เลือกบริษัท --</option>
                                    @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">แผนก<span class="text-red-500">*</span></label>
                                <select name="department_id" id="department_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                    <option value="">-- เลือกแผนก --</option>
                                    @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }} data-company-id="{{ $department->company_id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="position_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ตำแหน่ง<span class="text-red-500">*</span></label>
                                <select name="position_id" id="position_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                    <option value="">-- เลือกตำแหน่ง --</option>
                                    @foreach($positions as $position)
                                    <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }} data-department-id="{{ $position->department_id }}" data-company-id="{{ $position->department->company_id ?? '' }}">{{ $position->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="branch_office_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">สาขา<span class="text-red-500">*</span></label>
                                <select name="branch_office_id" id="branch_office_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                                    <option value="">-- เลือกสาขา --</option>
                                    @foreach($branchOffices as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_office_id') == $branch->id ? 'selected' : '' }} data-company-id="{{ $branch->company_id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="manager_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ผู้จัดการ</label>
                                <select name="manager_id" id="manager_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="">-- ไม่มีผู้จัดการ --</option>
                                    @foreach($managers as $manager)
                                    <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>{{ $manager->first_name }} {{ $manager->last_name }} ({{ $manager->position->name ?? '' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="employee_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ประเภทพนักงาน</label>
                                <select name="employee_type" id="employee_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="">-- เลือกประเภทพนักงาน --</option>
                                    <option value="พนักงานประจำ" {{ old('employee_type') == 'พนักงานประจำ' ? 'selected' : '' }}>พนักงานประจำ</option>
                                    <option value="พนักงานชั่วคราว" {{ old('employee_type') == 'พนักงานชั่วคราว' ? 'selected' : '' }}>พนักงานชั่วคราว</option>
                                    <option value="พนักงานพาร์ทไทม์" {{ old('employee_type') == 'พนักงานพาร์ทไทม์' ? 'selected' : '' }}>พนักงานพาร์ทไทม์</option>
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
                                    <option value="นาย" {{ old('title') == 'นาย' ? 'selected' : '' }}>นาย</option>
                                    <option value="นาง" {{ old('title') == 'นาง' ? 'selected' : '' }}>นาง</option>
                                    <option value="นางสาว" {{ old('title') == 'นางสาว' ? 'selected' : '' }}>นางสาว</option>
                                    <option value="ดร." {{ old('title') == 'ดร.' ? 'selected' : '' }}>ดร.</option>
                                </select>
                            </div>
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ชื่อ<span class="text-red-500">*</span></label>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">นามสกุล<span class="text-red-500">*</span></label>
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="nickname" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ชื่อเล่น</label>
                                <input type="text" name="nickname" id="nickname" value="{{ old('nickname') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">เพศ</label>
                                <select name="gender" id="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="">-- เลือกเพศ --</option>
                                    <option value="ชาย" {{ old('gender') == 'ชาย' ? 'selected' : '' }}>ชาย</option>
                                    <option value="หญิง" {{ old('gender') == 'หญิง' ? 'selected' : '' }}>หญิง</option>
                                    <option value="อื่นๆ" {{ old('gender') == 'อื่นๆ' ? 'selected' : '' }}>อื่นๆ</option>
                                </select>
                            </div>
                            <div>
                                <label for="birthdate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">วันเกิด</label>
                                <input type="date" name="birthdate" id="birthdate" value="{{ old('birthdate') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="id_card_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">เลขบัตรประชาชน</label>
                                <input type="text" name="id_card_number" id="id_card_number" value="{{ old('id_card_number') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="nationality" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">สัญชาติ</label>
                                <input type="text" name="nationality" id="nationality" value="{{ old('nationality', 'ไทย') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="religion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ศาสนา</label>
                                <select name="religion" id="religion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="">-- เลือกศาสนา --</option>
                                    <option value="พุทธ" {{ old('religion') == 'พุทธ' ? 'selected' : '' }}>พุทธ</option>
                                    <option value="คริสต์" {{ old('religion') == 'คริสต์' ? 'selected' : '' }}>คริสต์</option>
                                    <option value="อิสลาม" {{ old('religion') == 'อิสลาม' ? 'selected' : '' }}>อิสลาม</option>
                                    <option value="ฮินดู" {{ old('religion') == 'ฮินดู' ? 'selected' : '' }}>ฮินดู</option>
                                    <option value="ซิกข์" {{ old('religion') == 'ซิกข์' ? 'selected' : '' }}>ซิกข์</option>
                                    <option value="อื่นๆ" {{ old('religion') == 'อื่นๆ' ? 'selected' : '' }}>อื่นๆ</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="marital_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">สถานภาพ</label>
                                <select name="marital_status" id="marital_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="">-- เลือกสถานภาพ --</option>
                                    <option value="โสด" {{ old('marital_status') == 'โสด' ? 'selected' : '' }}>โสด</option>
                                    <option value="สมรส" {{ old('marital_status') == 'สมรส' ? 'selected' : '' }}>สมรส</option>
                                    <option value="หย่าร้าง" {{ old('marital_status') == 'หย่าร้าง' ? 'selected' : '' }}>หย่าร้าง</option>
                                    <option value="หม้าย" {{ old('marital_status') == 'หม้าย' ? 'selected' : '' }}>หม้าย</option>
                                </select>
                            </div>
                            <div>
                                <label for="blood_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">กรุ๊ปเลือด</label>
                                <select name="blood_type" id="blood_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="">-- เลือกกรุ๊ปเลือด --</option>
                                    <option value="A" {{ old('blood_type') == 'A' ? 'selected' : '' }}>A</option>
                                    <option value="B" {{ old('blood_type') == 'B' ? 'selected' : '' }}>B</option>
                                    <option value="AB" {{ old('blood_type') == 'AB' ? 'selected' : '' }}>AB</option>
                                    <option value="O" {{ old('blood_type') == 'O' ? 'selected' : '' }}>O</option>
                                </select>
                            </div>
                            <div>
                                <label for="medical_conditions" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">โรคประจำตัว</label>
                                <input type="text" name="medical_conditions" id="medical_conditions" value="{{ old('medical_conditions') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="height" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ส่วนสูง (ซม.)</label>
                                <input type="number" step="0.01" name="height" id="height" value="{{ old('height') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">น้ำหนัก (กก.)</label>
                                <input type="number" step="0.01" name="weight" id="weight" value="{{ old('weight') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ที่อยู่</label>
                            <textarea name="address" id="address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">{{ old('address') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-bold mb-4 border-b-2 pb-2">ข้อมูลการติดต่อ</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">อีเมล</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">โทรศัพท์</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="has_company_email" class="flex items-center">
                                    <input type="checkbox" name="has_company_email" id="has_company_email" value="1" {{ old('has_company_email') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">มีอีเมลบริษัท</span>
                                </label>
                            </div>
                            <div id="company_email_field" style="{{ old('has_company_email') ? '' : 'display: none;' }}">
                                <label for="company_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">อีเมลบริษัท</label>
                                <input type="email" name="company_email" id="company_email" value="{{ old('company_email') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ชื่อผู้ติดต่อฉุกเฉิน</label>
                                <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">เบอร์โทรผู้ติดต่อฉุกเฉิน</label>
                                <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
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
                                <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                <p class="mt-1 text-sm text-gray-500">หากไม่ระบุ ระบบจะกำหนดเป็นวันที่ปัจจุบัน</p>
                            </div>
                            <div>
                                <label for="probation_end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">วันสิ้นสุดทดลองงาน</label>
                                <input type="date" name="probation_end_date" id="probation_end_date" value="{{ old('probation_end_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
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
                                    <option value="ปริญญาเอก" {{ old('education_level') == 'ปริญญาเอก' ? 'selected' : '' }}>ปริญญาเอก</option>
                                    <option value="ปริญญาโท" {{ old('education_level') == 'ปริญญาโท' ? 'selected' : '' }}>ปริญญาโท</option>
                                    <option value="ปริญญาตรี" {{ old('education_level') == 'ปริญญาตรี' ? 'selected' : '' }}>ปริญญาตรี</option>
                                    <option value="อนุปริญญา" {{ old('education_level') == 'อนุปริญญา' ? 'selected' : '' }}>อนุปริญญา</option>
                                    <option value="ปวส." {{ old('education_level') == 'ปวส.' ? 'selected' : '' }}>ปวส.</option>
                                    <option value="ปวช." {{ old('education_level') == 'ปวช.' ? 'selected' : '' }}>ปวช.</option>
                                    <option value="มัธยมศึกษาตอนปลาย" {{ old('education_level') == 'มัธยมศึกษาตอนปลาย' ? 'selected' : '' }}>มัธยมศึกษาตอนปลาย</option>
                                    <option value="มัธยมศึกษาตอนต้น" {{ old('education_level') == 'มัธยมศึกษาตอนต้น' ? 'selected' : '' }}>มัธยมศึกษาตอนต้น</option>
                                    <option value="ประถมศึกษา" {{ old('education_level') == 'ประถมศึกษา' ? 'selected' : '' }}>ประถมศึกษา</option>
                                </select>
                            </div>
                            <div>
                                <label for="education_institute" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">สถาบันการศึกษา</label>
                                <input type="text" name="education_institute" id="education_institute" value="{{ old('education_institute') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="education_major" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">สาขาวิชา</label>
                                <input type="text" name="education_major" id="education_major" value="{{ old('education_major') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="years_experience" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ประสบการณ์ทำงาน (ปี)</label>
                                <input type="number" name="years_experience" id="years_experience" value="{{ old('years_experience') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="skills" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ทักษะ</label>
                                <input type="text" name="skills" id="skills" value="{{ old('skills') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="certificates" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ประกาศนียบัตร</label>
                                <input type="text" name="certificates" id="certificates" value="{{ old('certificates') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label for="previous_employment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">การทำงานที่ผ่านมา</label>
                            <textarea name="previous_employment" id="previous_employment" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">{{ old('previous_employment') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-xl font-bold mb-4 border-b-2 pb-2">ข้อมูลการเงิน</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ธนาคาร</label>
                                <select name="bank_name" id="bank_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="">-- เลือกธนาคาร --</option>
                                    <option value="ธนาคารกรุงเทพ" {{ old('bank_name') == 'ธนาคารกรุงเทพ' ? 'selected' : '' }}>ธนาคารกรุงเทพ</option>
                                    <option value="ธนาคารกสิกรไทย" {{ old('bank_name') == 'ธนาคารกสิกรไทย' ? 'selected' : '' }}>ธนาคารกสิกรไทย</option>
                                    <option value="ธนาคารกรุงไทย" {{ old('bank_name') == 'ธนาคารกรุงไทย' ? 'selected' : '' }}>ธนาคารกรุงไทย</option>
                                    <option value="ธนาคารไทยพาณิชย์" {{ old('bank_name') == 'ธนาคารไทยพาณิชย์' ? 'selected' : '' }}>ธนาคารไทยพาณิชย์</option>
                                    <option value="ธนาคารกรุงศรีอยุธยา" {{ old('bank_name') == 'ธนาคารกรุงศรีอยุธยา' ? 'selected' : '' }}>ธนาคารกรุงศรีอยุธยา</option>
                                    <option value="ธนาคารทหารไทยธนชาต" {{ old('bank_name') == 'ธนาคารทหารไทยธนชาต' ? 'selected' : '' }}>ธนาคารทหารไทยธนชาต</option>
                                </select>
                            </div>
                            <div>
                                <label for="bank_account" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">เลขที่บัญชี</label>
                                <input type="text" name="bank_account" id="bank_account" value="{{ old('bank_account') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="tax_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">เลขประจำตัวผู้เสียภาษี</label>
                                <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="tax_filing_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">สถานะการยื่นภาษี</label>
                                <select name="tax_filing_status" id="tax_filing_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                                    <option value="">-- เลือกสถานะ --</option>
                                    <option value="โสด" {{ old('tax_filing_status') == 'โสด' ? 'selected' : '' }}>โสด</option>
                                    <option value="มีคู่สมรส" {{ old('tax_filing_status') == 'มีคู่สมรส' ? 'selected' : '' }}>มีคู่สมรส</option>
                                    <option value="มีคู่สมรสและบุตร" {{ old('tax_filing_status') == 'มีคู่สมรสและบุตร' ? 'selected' : '' }}>มีคู่สมรสและบุตร</option>
                                </select>
                            </div>
                            <div>
                                <label for="social_security_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">เลขที่ประกันสังคม</label>
                                <input type="text" name="social_security_number" id="social_security_number" value="{{ old('social_security_number') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
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
                                <input type="text" name="passport_number" id="passport_number" value="{{ old('passport_number') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="passport_expiry" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">วันหมดอายุหนังสือเดินทาง</label>
                                <input type="date" name="passport_expiry" id="passport_expiry" value="{{ old('passport_expiry') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="work_permit_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">เลขที่ใบอนุญาตทำงาน</label>
                                <input type="text" name="work_permit_number" id="work_permit_number" value="{{ old('work_permit_number') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="work_permit_expiry" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">วันหมดอายุใบอนุญาตทำงาน</label>
                                <input type="date" name="work_permit_expiry" id="work_permit_expiry" value="{{ old('work_permit_expiry') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="visa_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ประเภทวีซ่า</label>
                                <input type="text" name="visa_type" id="visa_type" value="{{ old('visa_type') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                            <div>
                                <label for="visa_expiry" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">วันหมดอายุวีซ่า</label>
                                <input type="date" name="visa_expiry" id="visa_expiry" value="{{ old('visa_expiry') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 mb-6">
                    <a href="{{ route('employees.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ __('ยกเลิก') }}
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        {{ __('บันทึกข้อมูล') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ตั้งค่า event listener สำหรับช่อง has_company_email
            const hasCompanyEmailCheckbox = document.getElementById('has_company_email');
            const companyEmailField = document.getElementById('company_email_field');
            
            // แสดง/ซ่อนฟิลด์ company_email เมื่อมีการคลิกที่ checkbox
            hasCompanyEmailCheckbox.addEventListener('change', function() {
                companyEmailField.style.display = this.checked ? 'block' : 'none';
            });
            
            // ส่วนของการโหลดรหัสพนักงาน
            const companySelect = document.getElementById('company_id');
            const employeeCodeInput = document.getElementById('employee_code');
            const employeeCodePreview = document.getElementById('employee_code_preview');
            
            // กำหนดค่าเริ่มต้นสำหรับกรองแผนกและตำแหน่ง
            const departmentSelect = document.getElementById('department_id');
            const positionSelect = document.getElementById('position_id');
            const branchOfficeSelect = document.getElementById('branch_office_id');
            
            // เก็บข้อมูลตัวเลือกทั้งหมดไว้
            const allDepartmentOptions = Array.from(departmentSelect.options);
            const allPositionOptions = Array.from(positionSelect.options);
            const allBranchOptions = Array.from(branchOfficeSelect.options);
            
            // เมื่อเปลี่ยนบริษัท
            companySelect.addEventListener('change', function() {
                const companyId = this.value;
                
                // กรองแผนกตามบริษัท
                filterDepartmentsByCompany(companyId);
                
                // รีเซ็ตตำแหน่งและสาขา
                filterPositionsByDepartment('');
                filterBranchesByCompany(companyId);
                
                // ดึงรหัสพนักงานใหม่
                if (companyId) {
                    fetchEmployeeCode(companyId);
                } else {
                    employeeCodePreview.textContent = 'กรุณาเลือกบริษัทเพื่อดูรหัสพนักงานที่จะใช้';
                    employeeCodeInput.placeholder = 'EMP-XX-XXX';
                }
            });
            
            // เมื่อเปลี่ยนแผนก
            departmentSelect.addEventListener('change', function() {
                filterPositionsByDepartment(this.value);
            });
            
            // ฟังก์ชันกรองแผนกตามบริษัท
            function filterDepartmentsByCompany(companyId) {
                // ลบตัวเลือกแผนกทั้งหมดยกเว้นตัวแรก
                while (departmentSelect.options.length > 1) {
                    departmentSelect.remove(1);
                }
                
                if (!companyId) return;
                
                // เพิ่มตัวเลือกแผนกที่ตรงกับบริษัทที่เลือก
                allDepartmentOptions.forEach(option => {
                    if (option.value && option.dataset.companyId === companyId) {
                        departmentSelect.add(option.cloneNode(true));
                    }
                });
                
                departmentSelect.selectedIndex = 0;
            }
            
            // ฟังก์ชันกรองตำแหน่งตามแผนก
            function filterPositionsByDepartment(departmentId) {
                // ลบตัวเลือกตำแหน่งทั้งหมดยกเว้นตัวแรก
                while (positionSelect.options.length > 1) {
                    positionSelect.remove(1);
                }
                
                if (!departmentId) return;
                
                // เพิ่มตัวเลือกตำแหน่งที่ตรงกับแผนกที่เลือก
                allPositionOptions.forEach(option => {
                    if (option.value && option.dataset.departmentId === departmentId) {
                        positionSelect.add(option.cloneNode(true));
                    }
                });
                
                positionSelect.selectedIndex = 0;
            }
            
            // ฟังก์ชันกรองสาขาตามบริษัท
            function filterBranchesByCompany(companyId) {
                // ลบตัวเลือกสาขาทั้งหมดยกเว้นตัวแรก
                while (branchOfficeSelect.options.length > 1) {
                    branchOfficeSelect.remove(1);
                }
                
                if (!companyId) return;
                
                // เพิ่มตัวเลือกสาขาที่ตรงกับบริษัทที่เลือก
                allBranchOptions.forEach(option => {
                    if (option.value && option.dataset.companyId === companyId) {
                        branchOfficeSelect.add(option.cloneNode(true));
                    }
                });
                
                branchOfficeSelect.selectedIndex = 0;
            }
            
            // ฟังก์ชันดึงรหัสพนักงานใหม่
            function fetchEmployeeCode(companyId) {
                employeeCodePreview.innerHTML = '<span class="text-gray-500">กำลังโหลดรหัสพนักงาน...</span>';
                
                fetch(`/api/generate-employee-code/${companyId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('เกิดข้อผิดพลาดในการดึงรหัสพนักงาน');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.code) {
                            employeeCodePreview.innerHTML = `รหัสพนักงานถัดไป: <strong>${data.code}</strong>`;
                            employeeCodeInput.placeholder = data.code;
                        } else {
                            employeeCodePreview.innerHTML = '<span class="text-red-500">ไม่สามารถดึงรหัสพนักงานได้</span>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        employeeCodePreview.innerHTML = `<span class="text-red-500">เกิดข้อผิดพลาด: ${error.message}</span>`;
                    });
            }
            
            // ตั้งค่าเริ่มต้น ถ้ามีการเลือกบริษัทอยู่แล้ว
            if (companySelect.value) {
                filterDepartmentsByCompany(companySelect.value);
                if (departmentSelect.value) {
                    filterPositionsByDepartment(departmentSelect.value);
                }
                filterBranchesByCompany(companySelect.value);
                fetchEmployeeCode(companySelect.value);
            }
        });
    </script>
    @endpush
</x-app-layout>
