<div id="employee-data-container">
    <div class="overflow-x-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">รายการพนักงาน - {{ $currentCompany->name ?? '' }}</h3>
            <div class="flex items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400 mr-2">เรียงตาม:</span>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'direction' => 'asc']) }}"
                    class="{{ (!request()->has('sort') && !request()->has('direction')) || (request('sort') == 'id' && request('direction') == 'asc') ? 'text-blue-600 font-medium' : 'text-gray-600' }} text-sm mx-1">
                    ID ↑
                </a>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'direction' => 'desc']) }}"
                    class="{{ request('sort') == 'id' && request('direction') == 'desc' ? 'text-blue-600 font-medium' : 'text-gray-600' }} text-sm mx-1">
                    ID ↓
                </a>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'first_name', 'direction' => 'asc']) }}"
                    class="{{ request('sort') == 'first_name' && request('direction') == 'asc' ? 'text-blue-600 font-medium' : 'text-gray-600' }} text-sm mx-1">
                    ชื่อ ↑
                </a>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'first_name', 'direction' => 'desc']) }}"
                    class="{{ request('sort') == 'first_name' && request('direction') == 'desc' ? 'text-blue-600 font-medium' : 'text-gray-600' }} text-sm mx-1">
                    ชื่อ ↓
                </a>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'employee_code', 'direction' => 'asc']) }}"
                    class="{{ request('sort') == 'employee_code' && request('direction') == 'asc' ? 'text-blue-600 font-medium' : 'text-gray-600' }} text-sm mx-1">
                    รหัสพนักงาน ↑
                </a>
                <a href="{{ request()->fullUrlWithQuery(['sort' => 'employee_code', 'direction' => 'desc']) }}"
                    class="{{ request('sort') == 'employee_code' && request('direction') == 'desc' ? 'text-blue-600 font-medium' : 'text-gray-600' }} text-sm mx-1">
                    รหัสพนักงาน ↓
                </a>
            </div>
        </div>

        <!-- แสดงสรุปจำนวนพนักงาน -->
        <div class="mb-4">
            <p class="text-sm text-gray-600">
                แสดง {{ $employees->count() }} จาก {{ $employees->total() }} รายการ
                @if(request()->has('company_id') || isset($currentCompany))
                    - {{ isset($currentCompany) ? $currentCompany->name : 'บริษัทเลือก' }}
                @else
                    - ทุกบริษัท
                @endif
            </p>
        </div>

        @if($employees->isEmpty())
            <div class="text-center py-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-200">ไม่พบข้อมูลพนักงาน</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">เริ่มต้นโดยการเพิ่มพนักงานใหม่หรือเปลี่ยนตัวกรองการค้นหา</p>
                <div class="mt-6">
                    <a href="{{ route('employees.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 00-1 1v5H4a1 1 0 100 2h5v5a1 1 0 102 0v-5h5a1 1 0 100-2h-5V4a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        เพิ่มพนักงาน
                    </a>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="py-2 px-4 border-b text-gray-500 dark:text-gray-300 uppercase tracking-wider">รหัส</th>
                            <th scope="col" class="py-2 px-4 border-b text-gray-500 dark:text-gray-300 uppercase tracking-wider">ชื่อ-นามสกุล</th>
                            <th scope="col" class="py-2 px-4 border-b text-gray-500 dark:text-gray-300 uppercase tracking-wider">บริษัท</th>
                            <th scope="col" class="py-2 px-4 border-b text-gray-500 dark:text-gray-300 uppercase tracking-wider">แผนก</th>
                            <th scope="col" class="py-2 px-4 border-b text-gray-500 dark:text-gray-300 uppercase tracking-wider">ตำแหน่ง</th>
                            <th scope="col" class="py-2 px-4 border-b text-gray-500 dark:text-gray-300 uppercase tracking-wider">สถานะ</th>
                            <th scope="col" class="py-2 px-4 border-b text-gray-500 dark:text-gray-300 uppercase tracking-wider">การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($employees as $employee)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $employee->employee_code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center">
                                        @if($employee->profile_image)
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($employee->profile_image) }}" alt="{{ $employee->first_name }}">
                                            </div>
                                            <div class="ml-4">
                                                <a href="{{ route('employees.show', $employee->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">{{ $employee->title ?? '' }} {{ $employee->first_name }} {{ $employee->last_name }}</a>
                                                @if($employee->nickname)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">({{ $employee->nickname }})</div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="flex-shrink-0 h-10 w-10 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                                <span class="text-gray-500 dark:text-gray-400 text-sm">{{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}</span>
                                            </div>
                                            <div class="ml-4">
                                                <a href="{{ route('employees.show', $employee->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">{{ $employee->title ?? '' }} {{ $employee->first_name }} {{ $employee->last_name }}</a>
                                                @if($employee->nickname)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">({{ $employee->nickname }})</div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $employee->company->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ $employee->department->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    @php
                                        // แก้ไขส่วนนี้เพื่อให้แสดงชื่อตำแหน่งอย่างถูกต้องทุกกรณี
                                        $positionName = '-';
                                        
                                        if ($employee->position_id) {
                                            // วิธีที่ 1: ใช้ relationship ที่โหลดมาแล้ว
                                            if ($employee->position && $employee->position->name) {
                                                $positionName = $employee->position->name;
                                            } 
                                            // วิธีที่ 2: หากไม่มีข้อมูลจาก relation ให้ดึงข้อมูลโดยตรงจากฐานข้อมูล
                                            else {
                                                $position = App\Models\Position::withoutGlobalScopes()->find($employee->position_id);
                                                if ($position) {
                                                    $positionName = $position->name;
                                                } else {
                                                    $positionName = "ตำแหน่ง #" . $employee->position_id;
                                                }
                                            }
                                        }
                                    @endphp
                                    {{ $positionName }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $employee->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $employee->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('employees.show', $employee->id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('employees.edit', $employee->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบพนักงานนี้?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</div>
