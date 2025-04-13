<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800">
                {{ $department->name }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('departments.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('ย้อนกลับ') }}
                </a>
                <a href="{{ route('departments.edit', $department->id) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-yellow-600 border border-transparent rounded-md shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ __('แก้ไข') }}
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

            @if(session('error') || isset($error))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') ?? $error }}</span>
            </div>
            @endif

            <!-- ข้อมูลทั่วไป -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-5 border-b pb-3">ข้อมูลทั่วไป</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">ชื่อแผนก</h4>
                                <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $department->name }}</p>
                            </div>

                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">รหัสแผนก</h4>
                                <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $department->code ?: '-' }}</p>
                            </div>

                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">บริษัท</h4>
                                <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $department->company ? $department->company->name : '-' }}</p>
                            </div>
                        </div>

                        <div>
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">สถานะ</h4>
                                <p class="mt-1">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $department->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $department->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                    </span>
                                </p>
                            </div>

                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">สร้างเมื่อ</h4>
                                <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $department->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    @if($department->description)
                    <div class="mt-4">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">คำอธิบาย</h4>
                        <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $department->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- แผนกย่อย -->
            @if($department->children && $department->children->count() > 0)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-5 border-b pb-3">แผนกย่อย</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200">
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">รหัสแผนก</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">ชื่อแผนก</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">สถานะ</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($department->children as $child)
                                <tr class="hover:bg-gray-200 dark:hover:bg-gray-600 border-b border-gray-200 dark:border-gray-700">
                                    <td class="py-2 px-4">{{ $child->code ?: '-' }}</td>
                                    <td class="py-2 px-4">{{ $child->name }}</td>
                                    <td class="py-2 px-4">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full {{ $child->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $child->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-4 text-center">
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('departments.show', $child->id) }}" class="text-blue-500 hover:text-blue-700">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- แสดงปุ่มลบด้านล่าง -->
            <div class="mt-8 text-right">
                <form action="{{ route('departments.destroy', $department->id) }}" method="POST" class="inline" onsubmit="return confirm('คุณแน่ใจที่จะลบแผนกนี้ใช่หรือไม่?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        {{ __('ลบแผนกนี้') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>