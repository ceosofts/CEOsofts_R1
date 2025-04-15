<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ทดสอบการเปลี่ยนบริษัท') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-bold text-lg mb-4">บริษัทปัจจุบัน: {{ optional(\App\Models\Company::find(session('current_company_id')))->name ?? 'ไม่ได้เลือกบริษัท' }}</h3>
                    
                    <div class="mt-4">
                        <h4 class="font-medium">เลือกบริษัท:</h4>
                        <div class="mt-2 space-y-2">
                            @foreach(\App\Models\Company::all() as $company)
                                <div>
                                    <a 
                                        href="{{ route('switch.company', ['company' => $company->id, 'ref' => url()->current()]) }}"
                                        class="px-4 py-2 {{ session('current_company_id') == $company->id ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800' }} rounded-md inline-block"
                                    >
                                        {{ $company->name }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <a href="{{ route('employees.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
                            กลับไปยังหน้าพนักงาน
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
