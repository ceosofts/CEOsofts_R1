<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('จัดการบริษัท') }}
            </h2>
            <a href="{{ route('companies.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">
                {{ __('เพิ่มบริษัทใหม่') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
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

                    <!-- Debug Information - เพิ่มส่วนนี้เพื่อดีบั๊ก -->
                    @if(app()->environment('local'))
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4">
                        <p class="font-bold">Debug Information:</p>
                        <p>จำนวนบริษัททั้งหมด: {{ $companies->count() }}</p>
                        <p>หน้าปัจจุบัน: {{ $companies->currentPage() }}</p>
                    </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200">
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">โลโก้</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">รหัส</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">ชื่อบริษัท</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">เบอร์โทรศัพท์</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">อีเมล</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">สถานะ</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($companies as $company)
                                <tr class="hover:bg-gray-100 dark:hover:bg-gray-650 border-b border-gray-200 dark:border-gray-700">
                                    <td class="py-2 px-4">
                                        @if(isset($company->logo) && $company->logo)
                                        <img src="{{ Storage::url($company->logo) }}" alt="{{ $company->name }}" class="h-8 w-auto">
                                        @else
                                        <div class="h-8 w-8 bg-gray-200 dark:bg-gray-600 flex items-center justify-center rounded-full">
                                            <span class="text-gray-600 dark:text-gray-300">{{ substr($company->name, 0, 1) }}</span>
                                        </div>
                                        @endif
                                    </td>
                                    <td class="py-2 px-4">{{ $company->code ?? 'ไม่ระบุ' }}</td>
                                    <td class="py-2 px-4">{{ $company->name }}</td>
                                    <td class="py-2 px-4">{{ $company->phone ?? '-' }}</td>
                                    <td class="py-2 px-4">{{ $company->email ?? '-' }}</td>
                                    <td class="py-2 px-4">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full 
                                                {{ isset($company->status) && $company->status == 'active' || $company->is_active ? 
                                                   'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ isset($company->status) ? ($company->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน') : 
                                                   ($company->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน') }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-4 text-center">
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('companies.show', $company) }}" class="text-blue-500 hover:text-blue-700">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('companies.edit', $company) }}" class="text-yellow-500 hover:text-yellow-700">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('companies.destroy', $company) }}" method="POST" class="inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบบริษัทนี้?');">
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
                                    <td colspan="7" class="py-4 text-center text-gray-500 dark:text-gray-400">ไม่พบข้อมูลบริษัท</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $companies->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>