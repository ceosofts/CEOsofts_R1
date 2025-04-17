<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('ข้อมูลสาขา') }}: {{ $branchOffice->name }}
                </h2>
                <p class="mt-1 text-sm text-gray-600">
                    {{ $branchOffice->company->name ?? 'ไม่ระบุบริษัท' }}
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('branch-offices.index') }}" 
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gray-500 border border-gray-500 rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    กลับไปรายการสาขา
                </a>
                <a href="{{ route('branch-offices.edit', $branchOffice) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ __('แก้ไขข้อมูล') }}
                </a>
                <form action="{{ route('branch-offices.destroy', $branchOffice) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบสาขานี้?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition ease-in-out duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        {{ __('ลบสาขา') }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- ข้อมูลทั่วไป -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">ข้อมูลทั่วไป</h3>
                        
                        <div class="space-y-4">
                            <div class="flex flex-col sm:flex-row">
                                <span class="w-full sm:w-1/3 font-medium text-gray-700 dark:text-gray-300">รหัสสาขา:</span>
                                <span class="w-full sm:w-2/3 text-gray-900 dark:text-gray-100">{{ $branchOffice->formatted_code ?? $branchOffice->code }}</span>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row">
                                <span class="w-full sm:w-1/3 font-medium text-gray-700 dark:text-gray-300">ชื่อสาขา:</span>
                                <span class="w-full sm:w-2/3 text-gray-900 dark:text-gray-100">{{ $branchOffice->name }}</span>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row">
                                <span class="w-full sm:w-1/3 font-medium text-gray-700 dark:text-gray-300">บริษัท:</span>
                                <span class="w-full sm:w-2/3 text-gray-900 dark:text-gray-100">{{ $branchOffice->company->name ?? '-' }}</span>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row">
                                <span class="w-full sm:w-1/3 font-medium text-gray-700 dark:text-gray-300">ที่อยู่:</span>
                                <span class="w-full sm:w-2/3 text-gray-900 dark:text-gray-100">{{ $branchOffice->address }}</span>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row">
                                <span class="w-full sm:w-1/3 font-medium text-gray-700 dark:text-gray-300">เบอร์โทรศัพท์:</span>
                                <span class="w-full sm:w-2/3 text-gray-900 dark:text-gray-100">{{ $branchOffice->phone ?? '-' }}</span>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row">
                                <span class="w-full sm:w-1/3 font-medium text-gray-700 dark:text-gray-300">อีเมล:</span>
                                <span class="w-full sm:w-2/3 text-gray-900 dark:text-gray-100">{{ $branchOffice->email ?? '-' }}</span>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row">
                                <span class="w-full sm:w-1/3 font-medium text-gray-700 dark:text-gray-300">ประเภท:</span>
                                <span class="w-full sm:w-2/3">
                                    @if($branchOffice->is_headquarters)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            สำนักงานใหญ่
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            สาขาย่อย
                                        </span>
                                    @endif
                                </span>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row">
                                <span class="w-full sm:w-1/3 font-medium text-gray-700 dark:text-gray-300">สถานะ:</span>
                                <span class="w-full sm:w-2/3">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $branchOffice->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $branchOffice->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ข้อมูลเพิ่มเติม -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">ข้อมูลเพิ่มเติม</h3>
                        
                        <div class="space-y-4">
                            @if(isset($branchOffice->metadata['region']))
                                <div class="flex flex-col sm:flex-row">
                                    <span class="w-full sm:w-1/3 font-medium text-gray-700 dark:text-gray-300">ภูมิภาค:</span>
                                    <span class="w-full sm:w-2/3 text-gray-900 dark:text-gray-100">{{ $branchOffice->metadata['region'] ?? '-' }}</span>
                                </div>
                            @endif
                            
                            @if(isset($branchOffice->metadata['tax_branch_id']))
                                <div class="flex flex-col sm:flex-row">
                                    <span class="w-full sm:w-1/3 font-medium text-gray-700 dark:text-gray-300">รหัสสาขากรมสรรพากร:</span>
                                    <span class="w-full sm:w-2/3 text-gray-900 dark:text-gray-100">{{ $branchOffice->metadata['tax_branch_id'] ?? '-' }}</span>
                                </div>
                            @endif
                            
                            @if(isset($branchOffice->metadata['opening_date']))
                                <div class="flex flex-col sm:flex-row">
                                    <span class="w-full sm:w-1/3 font-medium text-gray-700 dark:text-gray-300">วันที่เปิดสาขา:</span>
                                    <span class="w-full sm:w-2/3 text-gray-900 dark:text-gray-100">
                                        {{ $branchOffice->metadata['opening_date'] ? date('d/m/Y', strtotime($branchOffice->metadata['opening_date'])) : '-' }}
                                    </span>
                                </div>
                            @endif
                            
                            <!-- แสดง metadata อื่นๆ -->
                            @foreach($branchOffice->metadata as $key => $value)
                                @if(!in_array($key, ['region', 'tax_branch_id', 'opening_date']) && !is_array($value))
                                    <div class="flex flex-col sm:flex-row">
                                        <span class="w-full sm:w-1/3 font-medium text-gray-700 dark:text-gray-300">{{ $key }}:</span>
                                        <span class="w-full sm:w-2/3 text-gray-900 dark:text-gray-100">{{ $value }}</span>
                                    </div>
                                @endif
                            @endforeach
                            
                            <div class="flex flex-col sm:flex-row">
                                <span class="w-full sm:w-1/3 font-medium text-gray-700 dark:text-gray-300">ผู้จัดการสาขา:</span>
                                <span class="w-full sm:w-2/3 text-gray-900 dark:text-gray-100">
                                    @if($branchOffice->manager)
                                        <a href="{{ route('employees.show', $branchOffice->manager->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                            {{ $branchOffice->manager->first_name }} {{ $branchOffice->manager->last_name }} ({{ $branchOffice->manager->employee_code }})
                                        </a>
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row">
                                <span class="w-full sm:w-1/3 font-medium text-gray-700 dark:text-gray-300">วันที่สร้าง:</span>
                                <span class="w-full sm:w-2/3 text-gray-900 dark:text-gray-100">{{ $branchOffice->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row">
                                <span class="w-full sm:w-1/3 font-medium text-gray-700 dark:text-gray-300">อัพเดทล่าสุด:</span>
                                <span class="w-full sm:w-2/3 text-gray-900 dark:text-gray-100">{{ $branchOffice->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ส่วนของพนักงานในสาขา -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">พนักงานในสาขา</h3>
                    
                    <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                            <div class="flex">
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">พนักงานทั้งหมด</p>
                                    <p class="text-2xl font-semibold text-blue-800">{{ $branchOffice->employees->count() }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
                            <div class="flex">
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">พนักงานที่ใช้งาน</p>
                                    <p class="text-2xl font-semibold text-green-800">{{ $activeEmployees }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
                            <div class="flex">
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">พนักงานที่ไม่ใช้งาน</p>
                                    <p class="text-2xl font-semibold text-red-800">{{ $inactiveEmployees }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($branchOffice->employees->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="py-2 px-4 border-b text-left">รหัสพนักงาน</th>
                                    <th class="py-2 px-4 border-b text-left">ชื่อ-นามสกุล</th>
                                    <th class="py-2 px-4 border-b text-left">ตำแหน่ง</th>
                                    <th class="py-2 px-4 border-b text-left">แผนก</th>
                                    <th class="py-2 px-4 border-b text-center">สถานะ</th>
                                    <th class="py-2 px-4 border-b text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($branchOffice->employees->take(5) as $employee)
                                    <tr class="hover:bg-gray-50 border-b">
                                        <td class="py-2 px-4">{{ $employee->employee_code }}</td>
                                        <td class="py-2 px-4">{{ $employee->first_name }} {{ $employee->last_name }}</td>
                                        <td class="py-2 px-4">{{ $employee->position->title ?? 'ไม่ระบุ' }}</td>
                                        <td class="py-2 px-4">{{ $employee->department->name ?? 'ไม่ระบุ' }}</td>
                                        <td class="py-2 px-4 text-center">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $employee->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $employee->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 text-center">
                                            <a href="{{ route('employees.show', $employee->id) }}" class="text-indigo-600 hover:text-indigo-900">ดูรายละเอียด</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($branchOffice->employees->count() > 5)
                        <div class="mt-4 text-center">
                            <a href="{{ route('employees.index', ['branch_office_id' => $branchOffice->id]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                ดูรายชื่อพนักงานทั้งหมดในสาขานี้
                            </a>
                        </div>
                    @endif
                    @else
                        <p class="text-gray-500">ไม่พบพนักงานในสาขานี้</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
