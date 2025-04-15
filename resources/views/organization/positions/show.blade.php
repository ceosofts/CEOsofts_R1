<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800">
                {{ __('รายละเอียดตำแหน่ง') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('positions.edit', $position->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    {{ __('แก้ไข') }}
                </a>

                <button onclick="confirmDelete()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    {{ __('ลบ') }}
                </button>
                <form id="delete-form" action="{{ route('positions.destroy', $position->id) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
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

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- ข้อมูลทั่วไป -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">ข้อมูลทั่วไป</h3>

                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">ชื่อตำแหน่ง</p>
                                <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ $position->name }}</p>
                            </div>

                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">รหัสตำแหน่ง</p>
                                <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ $position->code ?? '-' }}</p>
                            </div>

                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">บริษัท</p>
                                <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ $position->company ? $position->company->name : '-' }}</p>
                            </div>

                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">แผนก</p>
                                <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ $position->department ? $position->department->name : '-' }}</p>
                            </div>

                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">สถานะ</p>
                                <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $position->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $position->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                </span>
                                @if($position->status)
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $position->status }}
                                </span>
                                @endif
                            </div>

                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">คำอธิบาย</p>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $position->description ?: '-' }}</p>
                            </div>
                        </div>

                        <!-- ข้อมูลเพิ่มเติม -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                            <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">ข้อมูลเพิ่มเติม</h3>

                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">ระดับตำแหน่ง</p>
                                <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ $position->level ?? '-' }}</p>
                            </div>

                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">เงินเดือนขั้นต่ำ (บาท)</p>
                                <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ number_format($position->min_salary ?? 0, 2) }}</p>
                            </div>

                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">เงินเดือนขั้นสูง (บาท)</p>
                                <p class="mt-1 text-lg text-gray-900 dark:text-gray-100">{{ number_format($position->max_salary ?? 0, 2) }}</p>
                            </div>

                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Metadata</p>
                                @if($position->metadata)
                                <div x-data="{ open: false }" class="mt-2">
                                    <button @click="open = !open" type="button" class="flex items-center text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        <span x-text="open ? 'ซ่อนข้อมูล' : 'แสดงข้อมูล'">แสดงข้อมูล</span>
                                        <svg class="ml-1 h-5 w-5 transition-transform" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div x-show="open" style="display: none" class="mt-2 p-3 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                        <pre class="text-xs text-gray-900 dark:text-gray-100 overflow-auto max-h-40">{{ json_encode($position->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    </div>
                                </div>
                                @else
                                <p class="mt-1 text-gray-900 dark:text-gray-100">-</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- พนักงานในตำแหน่งนี้ -->
                    <div class="mt-6 bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
                        <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">พนักงานในตำแหน่งนี้</h3>

                        @if(count($position->employees) > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">รหัสพนักงาน</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ชื่อ-นามสกุล</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">อีเมล</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($position->employees as $employee)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">{{ $employee->employee_code ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $employee->first_name }} {{ $employee->last_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $employee->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $employee->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $employee->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-gray-600 dark:text-gray-400">ไม่พบข้อมูลพนักงานในตำแหน่งนี้</p>
                        @endif
                    </div>

                    <!-- ข้อมูล timestamps -->
                    <div class="mt-6">
                        <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                            <p>สร้างเมื่อ: {{ $position->created_at }}</p>
                            <p>แก้ไขล่าสุด: {{ $position->updated_at }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-between">
                        <a href="{{ route('positions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            {{ __('กลับไปยังรายการตำแหน่ง') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete() {
            if (confirm("คุณต้องการลบตำแหน่งนี้ใช่หรือไม่? การดำเนินการนี้ไม่สามารถเรียกคืนได้")) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
</x-app-layout>