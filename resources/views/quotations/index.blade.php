<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800">
                {{ __('ใบเสนอราคา') }}
            </h2>
            <div>
                <a href="{{ route('quotations.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('สร้างใบเสนอราคาใหม่') }}
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

            <!-- ฟอร์มค้นหาและตัวกรอง -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form action="{{ route('quotations.index') }}" method="GET">
                        <!-- Grid layout สำหรับจัดเรียงอินพุตในฟอร์มค้นหา -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- ช่อง "ค้นหา" -->
                            <div class="col-span-1">
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
                            <div class="col-span-1">
                                <label for="customer" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ลูกค้า</label>
                                <select name="customer" id="customer"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">-- ทั้งหมด --</option>
                                    @foreach(\App\Models\Customer::where('company_id', session('company_id'))->orderBy('name')->get() as $customer)
                                    <option value="{{ $customer->id }}" {{ request('customer') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- ช่อง "สถานะ" -->
                            <div class="col-span-1">
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">สถานะ</label>
                                <select name="status" id="status"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">-- ทั้งหมด --</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>กำลังเสนอลูกค้า</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>อนุมัติแล้ว</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>ปฏิเสธแล้ว</option>
                                </select>
                            </div>

                            <!-- ช่อง "พนักงานขาย" เพิ่มในส่วนฟิลเตอร์ -->
                            <div class="col-span-1">
                                <label for="sales_person_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">พนักงานขาย</label>
                                <select name="sales_person_id" id="sales_person_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">-- ทั้งหมด --</option>
                                    @foreach(\App\Models\Employee::where('company_id', session('company_id'))->orderBy('first_name')->get() as $employee)
                                    <option value="{{ $employee->id }}" {{ request('sales_person_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->employee_code }} - {{ $employee->first_name }} {{ $employee->last_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- ช่วงวันที่ - กินพื้นที่ 2 คอลัมน์ -->
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ช่วงวันที่</label>
                                <div class="flex space-x-2">
                                    <div class="w-1/2 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                                            class="block w-full pl-10 pr-3 py-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                    <div class="flex items-center">
                                        <span class="text-gray-500 dark:text-gray-400">ถึง</span>
                                    </div>
                                    <div class="w-1/2 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                                            class="block w-full pl-10 pr-3 py-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>
                                </div>
                            </div>

                            <!-- การเรียงลำดับ -->
                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">เรียงลำดับ</label>
                                <div class="flex space-x-2">
                                    <select id="sort" name="sort" class="w-2/3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="quotation_number" {{ request('sort') == 'quotation_number' ? 'selected' : '' }}>เลขที่ใบเสนอราคา</option>
                                        <option value="issue_date" {{ request('sort') == 'issue_date' ? 'selected' : '' }}>วันที่ออก</option>
                                        <option value="expiry_date" {{ request('sort') == 'expiry_date' ? 'selected' : '' }}>วันที่หมดอายุ</option>
                                        <option value="total_amount" {{ request('sort') == 'total_amount' ? 'selected' : '' }}>จำนวนเงิน</option>
                                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>วันที่สร้าง</option>
                                    </select>
                                    <select id="direction" name="direction" class="w-1/3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>↑</option>
                                        <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>↓</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- ปุ่มค้นหา วางชิดขวา -->
                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('quotations.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                    </svg>
                                    รีเซ็ต
                                </div>
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                ค้นหา
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">รายการใบเสนอราคา</h3>
                            <div class="flex items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400 mr-2">เรียงตาม:</span>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'quotation_number', 'direction' => 'asc']) }}"
                                    class="{{ request('sort') == 'quotation_number' && request('direction') == 'asc' ? 'text-blue-600 font-medium' : 'text-gray-600' }} text-sm mx-1">
                                    เลขที่ ↑
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'quotation_number', 'direction' => 'desc']) }}"
                                    class="{{ (!request()->has('sort') && !request()->has('direction')) || (request('sort') == 'quotation_number' && request('direction') == 'desc') ? 'text-blue-600 font-medium' : 'text-gray-600' }} text-sm mx-1">
                                    เลขที่ ↓
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'issue_date', 'direction' => 'desc']) }}"
                                    class="{{ request('sort') == 'issue_date' && request('direction') == 'desc' ? 'text-blue-600 font-medium' : 'text-gray-600' }} text-sm mx-1">
                                    ล่าสุด
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'issue_date', 'direction' => 'asc']) }}"
                                    class="{{ request('sort') == 'issue_date' && request('direction') == 'asc' ? 'text-blue-600 font-medium' : 'text-gray-600' }} text-sm mx-1">
                                    เก่าสุด
                                </a>
                            </div>
                        </div>

                        <table class="min-w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200">
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">เลขที่</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">วันที่</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">ลูกค้า</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">พนักงานขาย</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-right">ยอดรวม</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">สถานะ</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quotations as $quotation)
                                <tr class="hover:bg-gray-200 dark:hover:bg-gray-600 border-b border-gray-200 dark:border-gray-700">
                                    <td class="py-2 px-4">{{ $quotation->quotation_number }}</td>
                                    <td class="py-2 px-4">{{ $quotation->issue_date ? $quotation->issue_date->format('d/m/Y') : '-' }}</td>
                                    <td class="py-2 px-4">{{ $quotation->customer->name ?? 'ไม่ระบุ' }}</td>
                                    <td class="py-2 px-4">
                                        @if($quotation->sales_person_id && $salesPerson = \App\Models\Employee::find($quotation->sales_person_id))
                                            {{ $salesPerson->employee_code }} - {{ $salesPerson->first_name }} {{ $salesPerson->last_name }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="py-2 px-4 text-right">{{ number_format($quotation->total_amount, 2) }}</td>
                                    <td class="py-2 px-4">
                                        @if($quotation->status == 'draft')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full bg-gray-100 text-gray-800">
                                                กำลังเสนอลูกค้า
                                            </span>
                                        @elseif($quotation->status == 'approved')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full bg-green-100 text-green-800">
                                                อนุมัติแล้ว
                                            </span>
                                            @if($quotation->approved_at)
                                                <span class="text-xs text-gray-500 ml-1">{{ $quotation->approved_at->format('d/m/Y') }}</span>
                                            @endif
                                        @elseif($quotation->status == 'rejected')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full bg-red-100 text-red-800">
                                                ปฏิเสธแล้ว
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full bg-blue-100 text-blue-800">
                                                {{ $quotation->status }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-4 text-center">
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('quotations.show', $quotation) }}" class="text-blue-500 hover:text-blue-700">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            
                                            @if($quotation->status == 'draft')
                                            <a href="{{ route('quotations.edit', $quotation) }}" class="text-yellow-500 hover:text-yellow-700">
                                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            
                                            <form action="{{ route('quotations.destroy', $quotation) }}" method="POST" class="inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบใบเสนอราคานี้?');">
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
                                    <td colspan="6" class="py-4 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center py-8">
                                            <svg class="h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p>ไม่พบข้อมูลใบเสนอราคา</p>
                                            <a href="{{ route('quotations.create') }}" class="mt-3 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <i class="fas fa-plus mr-1"></i> สร้างใบเสนอราคาใหม่
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        @if($quotations instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            {{ $quotations->appends(request()->query())->links() }}
                        @endif
                    </div>
                </div>
            </div>

            <!-- เพิ่มในส่วนท้ายของ view เพื่อแสดงข้อมูล debug -->
            @if(false && isset($debugInfo) && config('app.env') === 'local')
            <div class="mt-6 bg-gray-100 dark:bg-gray-700 rounded-lg p-4">
                <h3 class="font-semibold text-lg mb-2 text-red-600 dark:text-red-400">Debug Information</h3>
                <div class="text-xs overflow-auto">
                    <pre>{{ print_r($debugInfo, true) }}</pre>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>