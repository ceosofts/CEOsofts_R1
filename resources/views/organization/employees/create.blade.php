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

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- ข้อมูลองค์กร -->
                            <div class="col-span-1 md:col-span-2 lg:col-span-3">
                                <h3 class="text-lg font-medium pb-2 mb-4 border-b border-gray-200 dark:border-gray-700">ข้อมูลองค์กร</h3>
                            </div>

                            <div>
                                <label for="company_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1 required">บริษัท</label>
                                <select id="company_id" name="company_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                                    <option value="">-- เลือกบริษัท --</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="department_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1 required">แผนก</label>
                                <select id="department_id" name="department_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                                    <option value="">-- เลือกแผนก --</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="position_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1 required">ตำแหน่ง</label>
                                <select id="position_id" name="position_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                                    <option value="">-- เลือกตำแหน่ง --</option>
                                    @foreach($positions as $position)
                                        <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="branch_office_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">สาขา</label>
                                <select id="branch_office_id" name="branch_office_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="">-- เลือกสาขา --</option>
                                    @foreach($branchOffices as $office)
                                        <option value="{{ $office->id }}" {{ old('branch_office_id') == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="employee_code" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">รหัสพนักงาน</label>
                                <input type="text" name="employee_code" id="employee_code" value="{{ old('employee_code') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="EMP001">
                            </div>

                            <div>
                                <label for="manager_id" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">ผู้จัดการ</label>
                                <select id="manager_id" name="manager_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="">-- เลือกผู้จัดการ --</option>
                                    @foreach($managers as $manager)
                                        <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>{{ $manager->first_name }} {{ $manager->last_name }} ({{ $manager->position->name ?? '' }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="status" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">สถานะ</label>
                                <select id="status" name="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>ใช้งาน</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน</option>
                                </select>
                            </div>

                            <!-- ข้อมูลส่วนตัว -->
                            <div class="col-span-1 md:col-span-2 lg:col-span-3">
                                <h3 class="text-lg font-medium pb-2 mb-4 border-b border-gray-200 dark:border-gray-700">ข้อมูลส่วนตัว</h3>
                            </div>

                            <div>
                                <label for="title" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">คำนำหน้า</label>
                                <select id="title" name="title" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="">-- เลือกคำนำหน้า --</option>
                                    <option value="นาย" {{ old('title') == 'นาย' ? 'selected' : '' }}>นาย</option>
                                    <option value="นาง" {{ old('title') == 'นาง' ? 'selected' : '' }}>นาง</option>
                                    <option value="นางสาว" {{ old('title') == 'นางสาว' ? 'selected' : '' }}>นางสาว</option>
                                </select>
                            </div>

                            <div>
                                <label for="first_name" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1 required">ชื่อ</label>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                            </div>

                            <div>
                                <label for="last_name" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1 required">นามสกุล</label>
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                            </div>

                            <div>
                                <label for="nickname" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">ชื่อเล่น</label>
                                <input type="text" name="nickname" id="nickname" value="{{ old('nickname') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>

                            <div>
                                <label for="gender" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">เพศ</label>
                                <select id="gender" name="gender" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="">-- เลือกเพศ --</option>
                                    <option value="ชาย" {{ old('gender') == 'ชาย' ? 'selected' : '' }}>ชาย</option>
                                    <option value="หญิง" {{ old('gender') == 'หญิง' ? 'selected' : '' }}>หญิง</option>
                                    <option value="อื่นๆ" {{ old('gender') == 'อื่นๆ' ? 'selected' : '' }}>อื่นๆ</option>
                                </select>
                            </div>

                            <div>
                                <label for="birthdate" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">วันเกิด</label>
                                <input type="date" name="birthdate" id="birthdate" value="{{ old('birthdate') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>

                            <!-- ข้อมูลการติดต่อ -->
                            <div class="col-span-1 md:col-span-2 lg:col-span-3">
                                <h3 class="text-lg font-medium pb-2 mb-4 border-b border-gray-200 dark:border-gray-700">ข้อมูลการติดต่อ</h3>
                            </div>

                            <div>
                                <label for="email" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">อีเมล</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>

                            <div>
                                <label for="phone" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">เบอร์โทรศัพท์</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>

                            <div>
                                <label for="address" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">ที่อยู่</label>
                                <textarea name="address" id="address" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('address') }}</textarea>
                            </div>

                            <div>
                                <label for="emergency_contact_name" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">ชื่อผู้ติดต่อฉุกเฉิน</label>
                                <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>

                            <div>
                                <label for="emergency_contact_phone" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">เบอร์โทรผู้ติดต่อฉุกเฉิน</label>
                                <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>

                            <!-- ข้อมูลการทำงาน -->
                            <div class="col-span-1 md:col-span-2 lg:col-span-3">
                                <h3 class="text-lg font-medium pb-2 mb-4 border-b border-gray-200 dark:border-gray-700">ข้อมูลการทำงาน</h3>
                            </div>

                            <div>
                                <label for="hire_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">วันที่เริ่มงาน</label>
                                <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>

                            <div>
                                <label for="probation_end_date" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">วันสิ้นสุดทดลองงาน</label>
                                <input type="date" name="probation_end_date" id="probation_end_date" value="{{ old('probation_end_date') }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>

                            <!-- รูปโปรไฟล์ -->
                            <div class="col-span-1 md:col-span-2 lg:col-span-3">
                                <h3 class="text-lg font-medium pb-2 mb-4 border-b border-gray-200 dark:border-gray-700">รูปโปรไฟล์</h3>
                            </div>

                            <div class="col-span-1 md:col-span-2 lg:col-span-3">
                                <label for="profile_image" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">รูปโปรไฟล์</label>
                                <input type="file" name="profile_image" id="profile_image" class="block mt-1 w-full border border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md p-2">
                                <p class="mt-1 text-sm text-gray-500">ไฟล์ภาพขนาดไม่เกิน 2MB</p>
                            </div>

                            <div class="col-span-1 md:col-span-2 lg:col-span-3 text-right">
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    บันทึกข้อมูล
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
