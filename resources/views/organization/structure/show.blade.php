<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                โครงสร้างองค์กร: {{ $company->name }}
            </h2>
            <div>
                <a href="{{ route('organization.structure.index') }}" class="text-sm bg-gray-500 hover:bg-gray-700 text-white py-1 px-3 rounded">
                    กลับ
                </a>
                <a href="{{ route('organization.structure.edit', $company->id) }}" class="text-sm bg-blue-500 hover:bg-blue-700 text-white py-1 px-3 rounded ml-2">
                    จัดการโครงสร้าง
                </a>
                <a href="{{ route('organization.structure.tree', $company->id) }}" class="text-sm bg-green-500 hover:bg-green-700 text-white py-1 px-3 rounded ml-2">
                    แผนผังองค์กร
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            
            <!-- ข้อมูลบริษัท -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-blue-100 p-4 rounded-lg">
                            <svg class="h-12 w-12 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="ml-6 flex-1">
                            <h3 class="text-xl font-bold text-gray-800">{{ $company->name }}</h3>
                            <p class="text-gray-500 mt-1">{{ $company->description }}</p>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                                <div>
                                    <span class="text-sm text-gray-500">สาขา</span>
                                    <p class="text-lg font-semibold">{{ $company->branchOffices->count() }}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">แผนก</span>
                                    <p class="text-lg font-semibold">{{ $company->departments->count() }}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">ตำแหน่ง</span>
                                    <p class="text-lg font-semibold">{{ $company->positions->count() }}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">พนักงาน</span>
                                    <p class="text-lg font-semibold">{{ $company->employees->count() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- โครงสร้างแผนก -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">โครงสร้างแผนก</h3>
                    
                    @if($company->departments->whereNull('parent_id')->isEmpty())
                        <p class="text-gray-500">ยังไม่มีแผนกที่กำหนด</p>
                    @else
                        <ul class="space-y-2">
                            @foreach($company->departments->whereNull('parent_id') as $department)
                                @include('organization.structure.partials.department-tree-item', [
                                    'department' => $department,
                                    'level' => 0,
                                ])
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
