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

        <table class="min-w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600">
            <thead>
                <tr class="bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200">
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">ID</th>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">รูปโปรไฟล์</th>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">รหัสพนักงาน</th>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">ชื่อ-นามสกุล</th>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">บริษัท</th>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">ตำแหน่ง</th>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">แผนก</th>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">สถานะ</th>
                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                <tr class="hover:bg-gray-200 dark:hover:bg-gray-600 border-b border-gray-200 dark:border-gray-700">
                    <td class="py-2 px-4">{{ $employee->id }}</td>
                    <td class="py-2 px-4">
                        @if($employee->profile_image)
                            <img src="{{ Storage::url($employee->profile_image) }}" alt="Profile" class="h-10 w-10 rounded-full object-cover">
                        @else
                            <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-500">{{ substr($employee->first_name, 0, 1) }}</span>
                            </div>
                        @endif
                    </td>
                    <td class="py-2 px-4">{{ $employee->employee_code ?: '-' }}</td>
                    <td class="py-2 px-4">{{ $employee->first_name }} {{ $employee->last_name }}</td>
                    <td class="py-2 px-4">{{ $employee->company->name ?? '-' }}</td>
                    <td class="py-2 px-4">{{ $employee->position->name ?? '-' }}</td>
                    <td class="py-2 px-4">{{ $employee->department->name ?? '-' }}</td>
                    <td class="py-2 px-4">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full 
                                {{ $employee->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $employee->status === 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                        </span>
                    </td>
                    <td class="py-2 px-4 text-center">
                        <div class="flex justify-center space-x-2">
                            <a href="{{ route('employees.show', $employee) }}" class="text-blue-500 hover:text-blue-700">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            <a href="{{ route('employees.edit', $employee) }}" class="text-yellow-500 hover:text-yellow-700">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบพนักงานนี้?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="py-4 text-center text-gray-500 dark:text-gray-400">ไม่พบข้อมูลพนักงาน</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $employees->appends(request()->query())->links() }}
    </div>
</div>
