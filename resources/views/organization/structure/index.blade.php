<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                โครงสร้างองค์กร
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">เลือกบริษัทเพื่อดูโครงสร้างองค์กร</h3>
                    
                    @if($companies->isEmpty())
                        <p class="text-gray-500">ไม่พบข้อมูลบริษัท</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($companies as $company)
                                <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition p-4">
                                    <h4 class="text-lg font-medium mb-2">{{ $company->name }}</h4>
                                    <p class="text-sm text-gray-500 mb-4">{{ $company->description ?? 'ไม่มีคำอธิบาย' }}</p>
                                    
                                    <div class="flex flex-col space-y-1 text-sm mb-4">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-blue-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <span>{{ count($company->branchOffices ?? []) }} สาขา</span>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-green-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            <span>{{ count($company->departments ?? []) }} แผนก</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 flex space-x-2">
                                        <a href="{{ route('organization.structure.show', $company->id) }}" class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                            ดูโครงสร้าง
                                        </a>
                                        <a href="{{ route('organization.structure.tree', $company->id) }}" class="text-green-600 hover:text-green-800 hover:underline font-medium">
                                            แผนผังองค์กร
                                        </a>
                                        <a href="{{ route('organization.structure.edit', $company->id) }}" class="text-indigo-600 hover:text-indigo-800 hover:underline font-medium">
                                            จัดการโครงสร้าง
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Debug Information - แสดงเฉพาะใน Development Mode -->
        @if(config('app.debug'))
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6 relative">
                <div class="bg-gray-100 overflow-hidden shadow-sm sm:rounded-lg p-6" x-data="{ showDebug: false }">
                    <button 
                        @click="showDebug = !showDebug" 
                        class="absolute top-4 right-4 px-3 py-1 text-xs font-medium rounded bg-gray-200 hover:bg-gray-300 text-gray-700">
                        <span x-text="showDebug ? 'ซ่อนข้อมูล Debug' : 'แสดงข้อมูล Debug'"></span>
                    </button>
                    
                    <div x-show="showDebug" x-cloak>
                        <h3 class="font-semibold text-lg mb-2 text-gray-700">Debug Information:</h3>
                        <pre class="bg-gray-800 text-green-400 p-4 rounded-md overflow-auto text-xs"><?php 
                            try {
                                $debug = [
                                    'companies_count' => $companies->count(),
                                    'company_ids' => $companies->pluck('id')->toArray(),
                                    'routes' => [
                                        'structure_index' => route('organization.structure.index'),
                                        'example_show' => $companies->first() ? route('organization.structure.show', $companies->first()->id) : null
                                    ]
                                ];
                                echo json_encode($debug, JSON_PRETTY_PRINT);
                            } catch (Exception $e) {
                                echo "Error: " . $e->getMessage();
                            }
                        ?></pre>
                    </div>
                </div>
            </div>
        @endif
    </div>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>
