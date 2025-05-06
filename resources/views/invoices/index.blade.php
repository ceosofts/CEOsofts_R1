<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800">
                {{ __('รายการใบแจ้งหนี้') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('invoices.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('สร้างใบแจ้งหนี้ใหม่') }}
                </a>
                
                @if(config('app.debug'))
                <a href="{{ route('invoices.index', ['seed_sample' => 1]) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    {{ __('สร้างข้อมูลตัวอย่าง') }}
                </a>
                @endif
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

            @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif

            @if(session('error_message'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error_message') }}</span>
            </div>
            @endif

            @if(session('info'))
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('info') }}</span>
            </div>
            @endif

            <!-- Debug section -->
            @if(config('app.debug'))
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="font-bold">Debug Info:</span>
                <ul class="ml-4 mt-1 text-sm">
                    <li>Current Company ID: {{ session('company_id') ?? 'Not Set' }}</li>
                    <li>Current Company ID (Alternate): {{ session('current_company_id') ?? 'Not Set' }}</li>
                    <li>Invoices Count: {{ $invoices->count() }} (Total: {{ $invoices->total() ?? 0 }})</li>
                    <li>Is Paginated: {{ $invoices instanceof \Illuminate\Pagination\LengthAwarePaginator ? 'Yes' : 'No' }}</li>
                    <li>Auth User: {{ auth()->check() ? auth()->user()->name : 'Not Logged In' }}</li>
                </ul>
            </div>
            @endif

            <!-- ฟอร์มค้นหาและตัวกรอง -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form action="{{ route('invoices.index') }}" method="GET">
                        <!-- Grid layout แบบแถวเดียว -->
                        <div class="flex flex-wrap items-end gap-3">
                            <!-- ช่อง "ค้นหา" -->
                            <div class="w-full md:w-64 lg:w-70">
                                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ค้นหา</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                                        class="block w-full pl-10 pr-3 py-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="เลขที่, อ้างอิง, ชื่อลูกค้า">
                                </div>
                            </div>

                            <!-- ช่อง "ลูกค้า" -->
                            <div class="w-full md:w-40 lg:w-52">
                                <label for="customer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ลูกค้า</label>
                                <select name="customer_id" id="customer_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">-- ทั้งหมด --</option>
                                    @php
                                        $companyId = session('company_id') ?? session('current_company_id') ?? 1;
                                        $customers = \App\Models\Customer::where('company_id', $companyId)->orderBy('name')->get();
                                    @endphp
                                    @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- ช่อง "สถานะ" -->
                            <div class="w-full md:w-36 lg:w-40">
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">สถานะ</label>
                                <select name="status" id="status"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">-- ทั้งหมด --</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>ร่าง</option>
                                    <option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>ออกแล้ว</option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>ชำระแล้ว</option>
                                    <option value="void" {{ request('status') == 'void' ? 'selected' : '' }}>ยกเลิก</option>
                                </select>
                            </div>

                            <!-- วันที่ (ย่อให้สั้นลง) -->
                            <div class="w-full md:w-44 lg:w-60">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ช่วงวันที่</label>
                                <div class="flex space-x-1">
                                    <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}"
                                        class="block w-1/2 py-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}"
                                        class="block w-1/2 py-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                            </div>

                            <!-- ปุ่มค้นหาและรีเซ็ต -->
                            <div class="flex space-x-2">
                                <button type="submit" class="inline-flex items-center px-4 h-10 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    ค้นหา
                                </button>
                                <a href="{{ route('invoices.index') }}" class="inline-flex items-center px-4 h-10 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                                    <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                    </svg>
                                    รีเซ็ต
                                </a>
                            </div>
                        </div>

                        <!-- Hidden sort fields to maintain ordering -->
                        <input type="hidden" name="sort" value="{{ request('sort', 'invoice_number') }}">
                        <input type="hidden" name="direction" value="{{ request('direction', 'desc') }}">
                        
                    </form>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">รายการใบแจ้งหนี้</h3>
                            <div class="flex items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400 mr-2">เรียงตาม:</span>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'invoice_number', 'direction' => 'asc']) }}"
                                    class="{{ request('sort') == 'invoice_number' && request('direction') == 'asc' ? 'text-blue-600 font-medium' : 'text-gray-600' }} text-sm mx-1">
                                    เลขที่ ↑
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'invoice_number', 'direction' => 'desc']) }}"
                                    class="{{ (!request()->has('sort') && !request()->has('direction')) || (request('sort') == 'invoice_number' && request('direction') == 'desc') ? 'text-blue-600 font-medium' : 'text-gray-600' }} text-sm mx-1">
                                    เลขที่ ↓
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'invoice_date', 'direction' => 'desc']) }}"
                                    class="{{ request('sort') == 'invoice_date' && request('direction') == 'desc' ? 'text-blue-600 font-medium' : 'text-gray-600' }} text-sm mx-1">
                                    ล่าสุด
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'invoice_date', 'direction' => 'asc']) }}"
                                    class="{{ request('sort') == 'invoice_date' && request('direction') == 'asc' ? 'text-blue-600 font-medium' : 'text-gray-600' }} text-sm mx-1">
                                    เก่าสุด
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'due_date', 'direction' => 'asc']) }}"
                                    class="{{ request('sort') == 'due_date' && request('direction') == 'asc' ? 'text-blue-600 font-medium' : 'text-gray-600' }} text-sm mx-1">
                                    ครบกำหนด
                                </a>
                            </div>
                        </div>

                        <table class="min-w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200">
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">เลขที่</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">วันที่</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">ลูกค้า</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-right">มูลค่ารวม</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">สถานะ</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">ครบกำหนด</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $invoice)
                                <tr class="hover:bg-gray-200 dark:hover:bg-gray-600 border-b border-gray-200 dark:border-gray-700">
                                    <td class="py-2 px-4">
                                        <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-600 hover:text-blue-900 hover:underline font-medium">
                                            {{ $invoice->invoice_number }}
                                        </a>
                                    </td>
                                    <td class="py-2 px-4">{{ $invoice->invoice_date ? $invoice->invoice_date->format('d/m/Y') : 'N/A' }}</td>
                                    <td class="py-2 px-4">{{ $invoice->customer->name ?? 'ไม่ระบุ' }}</td>
                                    <td class="py-2 px-4 text-right">{{ number_format($invoice->total_amount, 2) }}</td>
                                    <td class="py-2 px-4">
                                        @if($invoice->status == 'draft')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full bg-gray-100 text-gray-800">
                                                ร่าง
                                            </span>
                                        @elseif($invoice->status == 'issued')
                                            @if($invoice->isOverdue())
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full bg-red-100 text-red-800">
                                                    เกินกำหนด
                                                </span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full bg-blue-100 text-blue-800">
                                                    ออกแล้ว
                                                </span>
                                            @endif
                                        @elseif($invoice->status == 'paid')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full bg-green-100 text-green-800">
                                                ชำระแล้ว
                                            </span>
                                        @elseif($invoice->status == 'void')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full bg-red-100 text-red-800">
                                                ยกเลิก
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full bg-gray-100 text-gray-800">
                                                {{ $invoice->status }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-4">
                                        {{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="py-2 px-4 text-center">
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-500 hover:text-blue-700">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            
                                            @if($invoice->status == 'draft')
                                                <a href="{{ route('invoices.edit', $invoice) }}" class="text-yellow-500 hover:text-yellow-700">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                
                                                <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบใบแจ้งหนี้นี้?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="py-4 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center py-8">
                                            <svg class="h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p>ไม่พบข้อมูลใบแจ้งหนี้</p>
                                            
                                            @if(session('info'))
                                                <p class="mt-2 text-sm text-yellow-600">{{ session('info') }}</p>
                                            @endif
                                            
                                            <div class="flex space-x-2 mt-4">
                                                <a href="{{ route('invoices.create') }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <i class="fas fa-plus mr-1"></i> สร้างใบแจ้งหนี้ใหม่
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        @if($invoices instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            {{ $invoices->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
