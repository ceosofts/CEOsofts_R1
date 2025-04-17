<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800">
                {{ __('รายการพนักงาน') }}
                @if(isset($currentCompany))
                    <span class="text-xl font-medium text-gray-600">- {{ $currentCompany->name }}</span>
                @endif
            </h2>
            <div>
                <a href="{{ route('employees.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('เพิ่มพนักงานใหม่') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- แสดงตัวเลือกบริษัทแบบใหม่ที่อยู่ในหน้าเดียวกัน -->
            @if(isset($companies) && count($companies) > 0)
                <x-employee-company-selector :companies="$companies" />
            @endif

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

            <!-- เพิ่มการ์ดสำหรับการกรองข้อมูล -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">ค้นหาและกรองข้อมูล</h3>
                        <!-- เพิ่มปุ่มแสดงพนักงานทั้งหมด -->
                        <!-- <a href="{{ route('employees.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                            </svg>
                            แสดงพนักงานทั้งหมด
                        </a> -->
                    </div>

                    <form method="GET" action="{{ route('employees.index') }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label for="id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ID</label>
                                <input type="text" name="id" id="id" value="{{ request('id') }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="ค้นหาตาม ID">
                            </div>

                            <div>
                                <label for="employee_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">รหัสพนักงาน</label>
                                <input type="text" name="employee_code" id="employee_code" value="{{ request('employee_code') }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="ค้นหาตามรหัสพนักงาน">
                            </div>

                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ชื่อ</label>
                                <input type="text" name="first_name" id="first_name" value="{{ request('first_name') }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="ค้นหาตามชื่อพนักงาน">
                            </div>

                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">นามสกุล</label>
                                <input type="text" name="last_name" id="last_name" value="{{ request('last_name') }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="ค้นหาตามนามสกุลพนักงาน">
                            </div>

                            <div>
                                <label for="company_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">บริษัท</label>
                                <select name="company_id" id="company_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">-- ทั้งหมด --</option>
                                    @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">แผนก</label>
                                <select name="department_id" id="department_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">-- ทั้งหมด --</option>
                                    @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="position_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ตำแหน่ง</label>
                                <select name="position_id" id="position_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">-- ทั้งหมด --</option>
                                    @foreach($positions as $position)
                                    <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                                        {{ $position->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">สถานะ</label>
                                <select name="status" id="status"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">-- ทั้งหมด --</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>ใช้งาน</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end">
                            <a href="{{ route('employees.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-2 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                                รีเซ็ต
                            </a>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                ค้นหา
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- แสดงสรุปการค้นหา (ถ้ามีการค้นหา) -->
            @if(request()->anyFilled(['id', 'employee_code', 'first_name', 'last_name', 'company_id', 'department_id', 'position_id', 'status']))
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-md mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">
                                กำลังแสดงผลการค้นหา. <a href="{{ route('employees.index') }}" class="font-medium underline">คลิกที่นี่</a> เพื่อแสดงพนักงานทั้งหมด
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- แยกส่วนของตารางไปไว้ใน partial view -->
                    @include('organization.employees._employee_table')
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const companySelect = document.getElementById('company_id');
            const departmentSelect = document.getElementById('department_id');
            const positionSelect = document.getElementById('position_id');
            const branchOfficeSelect = document.getElementById('branch_office_id');
            
            // เก็บตัวเลือกทั้งหมดไว้
            const allDepartments = Array.from(departmentSelect.options);
            const allPositions = Array.from(positionSelect.options);
            const allBranchOffices = Array.from(branchOfficeSelect.options);
            
            // กรองแผนกตามบริษัท
            function filterDepartmentsByCompany(companyId) {
                // เก็บตัวเลือกแรก (-- ทุกแผนก --)
                const firstOption = departmentSelect.options[0];
                departmentSelect.innerHTML = '';
                departmentSelect.appendChild(firstOption);
                
                // เพิ่มเฉพาะแผนกที่อยู่ในบริษัทที่เลือก หรือทั้งหมดถ้าไม่ได้เลือกบริษัท
                allDepartments.forEach(option => {
                    if (option.value === '') return; // ข้ามตัวเลือกแรก
                    
                    if (!companyId || option.dataset.companyId === companyId) {
                        departmentSelect.appendChild(option.cloneNode(true));
                    }
                });
            }
            
            // กรองตำแหน่งตามแผนกและบริษัท
            function filterPositionsByDepartment(departmentId, companyId) {
                // เก็บตัวเลือกแรก (-- ทุกตำแหน่ง --)
                const firstOption = positionSelect.options[0];
                positionSelect.innerHTML = '';
                positionSelect.appendChild(firstOption);
                
                // เพิ่มเฉพาะตำแหน่งที่อยู่ในแผนกและบริษัทที่เลือก หรือทั้งหมดถ้าไม่ได้เลือก
                allPositions.forEach(option => {
                    if (option.value === '') return; // ข้ามตัวเลือกแรก
                    
                    // ตรวจสอบว่า dataset มีค่าหรือไม่ก่อนเปรียบเทียบ
                    const optCompanyId = option.dataset.companyId || '';
                    const optDepartmentId = option.dataset.departmentId || '';
                    
                    const matchesCompany = !companyId || optCompanyId === companyId;
                    const matchesDepartment = !departmentId || optDepartmentId === departmentId;
                    
                    if (matchesCompany && matchesDepartment) {
                        positionSelect.appendChild(option.cloneNode(true));
                    } else if (!departmentId && matchesCompany) {
                        // ถ้าไม่ได้เลือกแผนกแต่เลือกบริษัท ให้แสดงตำแหน่งที่อยู่ในบริษัทนั้น
                        positionSelect.appendChild(option.cloneNode(true));
                    }
                });
                
                // เพิ่ม debug log
                console.log('Filtered positions by department:', departmentId, 'company:', companyId);
            }
            
            // ...existing code...
        });
    </script>
    @endpush
</x-app-layout>
