@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800">
                {{ __('สาขาบริษัท') }}
            </h2>
            <a href="{{ route('branch-offices.create') }}" 
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('เพิ่มสาขาใหม่') }}
            </a>
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

            <!-- เพิ่มการ์ดสำหรับการกรองข้อมูล -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">ค้นหาและกรองข้อมูลสาขา</h3>

                    <form action="{{ route('branch-offices.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:items-end md:space-x-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- เพิ่มตัวเลือกบริษัท -->
                            <div class="flex-1">
                                <label for="company_id" class="block text-sm font-medium text-gray-700">บริษัท</label>
                                <select name="company_id" id="company_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">-- ทุกบริษัท --</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- ช่องค้นหา -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ค้นหา</label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="รหัส หรือ ชื่อสาขา">
                            </div>

                            <!-- สถานะ -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">สถานะ</label>
                                <select name="status" id="status"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">-- ทั้งหมด --</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>ใช้งาน</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน</option>
                                </select>
                            </div>

                            <!-- ประเภทสาขา -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ประเภท</label>
                                <select name="type" id="type"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">-- ทั้งหมด --</option>
                                    <option value="headquarters" {{ request('type') == 'headquarters' ? 'selected' : '' }}>สำนักงานใหญ่</option>
                                    <option value="branch" {{ request('type') == 'branch' ? 'selected' : '' }}>สาขาย่อย</option>
                                </select>
                            </div>

                            <!-- ปุ่มค้นหาและรีเซ็ต -->
                            <div class="flex items-end">
                                <div class="flex space-x-2">
                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        ค้นหา
                                    </button>
                                    <a href="{{ route('branch-offices.index', ['company_id' => '']) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                                        รีเซ็ต
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- เพิ่มแจ้งเตือนถ้าไม่มีข้อมูล -->
            @if(isset($debugInfo) && isset($debugInfo['warning']))
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">คำเตือน!</strong>
                <span class="block sm:inline">{{ $debugInfo['warning'] }}</span>
                @if(isset($showAllLink) && $showAllLink)
                <div class="mt-2">
                    <a href="{{ route('branch-offices.index', ['show_all' => true]) }}" 
                      class="inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-100 border border-transparent rounded-md hover:bg-indigo-200">
                        แสดงข้อมูลทั้งหมด
                    </a>
                </div>
                @endif
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">
                                รายการสาขา 
                                @if(isset($branchOffices))
                                    ({{ $branchOffices->total() }} รายการ)
                                @endif
                            </h3>
                            
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
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'code', 'direction' => 'asc']) }}"
                                    class="{{ request('sort') == 'code' && request('direction') == 'asc' ? 'text-blue-600 font-medium' : 'text-gray-600' }} text-sm mx-1">
                                    รหัส ↑
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'code', 'direction' => 'desc']) }}"
                                    class="{{ request('sort') == 'code' && request('direction') == 'desc' ? 'text-blue-600 font-medium' : 'text-gray-600' }} text-sm mx-1">
                                    รหัส ↓
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => 'asc']) }}"
                                    class="{{ request('sort') == 'name' && request('direction') == 'asc' ? 'text-blue-600 font-medium' : 'text-gray-600' }} text-sm mx-1">
                                    ชื่อ ↑
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'direction' => 'desc']) }}"
                                    class="{{ request('sort') == 'name' && request('direction') == 'desc' ? 'text-blue-600 font-medium' : 'text-gray-600' }} text-sm mx-1">
                                    ชื่อ ↓
                                </a>
                            </div>
                        </div>
                        
                        <table class="min-w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200">
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">รหัสสาขา</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">ชื่อสาขา</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">บริษัท</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">ที่อยู่</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-center">ประเภท</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-center">สถานะ</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($branchOffices->isEmpty())
                                    <tr>
                                        <td colspan="7" class="py-4 text-center text-gray-500 dark:text-gray-400">
                                            ไม่พบข้อมูลสาขา
                                            @if(request('search') || request('status') || request('type') || request('company_id'))
                                                ที่ตรงกับเงื่อนไขการค้นหา
                                            @endif
                                        </td>
                                    </tr>
                                @else
                                    @php
                                        // เพิ่มการ debug ข้อมูล
                                        $firstBranch = $branchOffices->first();
                                        \Illuminate\Support\Facades\Log::debug('First branch in view:', [
                                            'id' => $firstBranch->id,
                                            'name' => $firstBranch->name,
                                            'raw_name' => isset($firstBranch->name) ? 'ค่าไม่เป็น null' : 'ค่าเป็น null',
                                            'code' => $firstBranch->code,
                                        ]);
                                    @endphp
                                    
                                    @foreach ($branchOffices as $branch)
                                        <tr class="hover:bg-gray-200 dark:hover:bg-gray-600 border-b border-gray-200 dark:border-gray-700">
                                            <td class="py-2 px-4 font-medium">{{ $branch->formatted_code ?? $branch->code }}</td>
                                            <td class="py-2 px-4">
                                                <a href="{{ route('branch-offices.show', $branch->id) }}" 
                                                title="{{ $branch->name ?? 'ไม่มีชื่อ' }}">
                                                    {{ $branch->name ?? 'ไม่มีชื่อ' }}
                                                </a>
                                                @if($branch->is_headquarters)
                                                    <span class="ml-1 px-1.5 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-800">HQ</span>
                                                @endif
                                            </td>
                                            <td class="py-2 px-4">{{ $branch->company->name ?? 'ไม่มีข้อมูล' }}</td>
                                            <td class="py-2 px-4 max-w-xs truncate">{{ \Illuminate\Support\Str::limit($branch->address, 50) }}</td>
                                            <td class="py-2 px-4 text-center">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full {{ $branch->is_headquarters ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ $branch->is_headquarters ? 'สำนักงานใหญ่' : 'สาขาย่อย' }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 text-center">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full 
                                                    {{ $branch->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $branch->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 text-center">
                                                <div class="flex justify-center space-x-2">
                                                    <a href="{{ route('branch-offices.show', $branch->id) }}" class="text-blue-500 hover:text-blue-700">
                                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('branch-offices.edit', $branch->id) }}" class="text-yellow-500 hover:text-yellow-700">
                                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('branch-offices.destroy', $branch->id) }}" method="POST" class="inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบสาขานี้?');">
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
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $branchOffices->appends(request()->query())->links() }}
                    </div>

                    <!-- แสดงข้อมูล Debug ถ้าอยู่ในโหมด Debug -->
                    @if(config('app.debug') && isset($debugInfo))
                    <div x-data="{ open: false }" class="mt-8">
                        <button
                            @click="open = !open"
                            class="flex items-center justify-between w-full px-4 py-2 bg-gray-200 dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-t-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            <span class="font-semibold text-red-500">Debug Information</span>
                            <svg x-show="!open" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            <svg x-show="open" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        
                        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="p-4 bg-gray-100 rounded-b-lg text-xs">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                <div><strong>User ID:</strong> {{ $debugInfo['user_id'] ?? '-' }}</div>
                                <div><strong>User Email:</strong> {{ $debugInfo['user_email'] ?? '-' }}</div>
                                <div><strong>Current Company ID:</strong> {{ $debugInfo['current_company_id'] ?? '-' }}</div>
                                <div><strong>Selected Company ID:</strong> {{ $debugInfo['selected_company_id'] ?? '-' }}</div>
                                <div><strong>Total Branches:</strong> {{ $debugInfo['total_branches'] ?? '0' }}</div>
                                @if(isset($debugInfo['query_sql']))
                                    <div class="col-span-2">
                                        <strong>Query:</strong> 
                                        <div class="mt-1 p-2 bg-gray-200 dark:bg-gray-700 rounded overflow-x-auto">
                                            <code class="text-xs text-gray-800 dark:text-gray-300">{{ $debugInfo['query_sql'] }}</code>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
