<x-app-layout>
    <x-slot name="header">
        จัดการข้อมูลบริษัท
    </x-slot>

    <div class="space-y-6">
        <!-- ส่วนหัว -->
        <div class="flex justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">รายการบริษัท</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">ดูและจัดการข้อมูลบริษัททั้งหมด</p>
            </div>
            <div>
                <a href="{{ route('companies.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    เพิ่มบริษัทใหม่
                </a>
            </div>
        </div>

        <!-- ข้อความแจ้งเตือน -->
        @if (session('success'))
            <div class="rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- ตารางข้อมูล -->
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            ชื่อบริษัท
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            ข้อมูลติดต่อ
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            สถานะ
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            จัดการ
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($companies as $company)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if ($company->logo)
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full" src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}">
                                        </div>
                                    @else
                                        <div class="flex-shrink-0 h-10 w-10 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                            <span class="text-gray-500 dark:text-gray-400 font-medium">{{ substr($company->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $company->name }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $company->tax_id ?? 'ไม่ระบุเลขประจำตัวผู้เสียภาษี' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-gray-100">{{ $company->email ?? 'ไม่ระบุอีเมล' }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $company->phone ?? 'ไม่ระบุโทรศัพท์' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($company->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100">
                                        ใช้งาน
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-100">
                                        ไม่ใช้งาน
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('companies.show', $company) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">ดูข้อมูล</a>
                                <a href="{{ route('companies.edit', $company) }}" class="ml-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">แก้ไข</a>
                                <form class="inline-block ml-2" action="{{ route('companies.destroy', $company) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือว่าต้องการลบบริษัทนี้?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">ลบ</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500 dark:text-gray-400">
                                ไม่พบข้อมูลบริษัท
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $companies->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
