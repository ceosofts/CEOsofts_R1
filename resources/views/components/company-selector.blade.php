<div class="company-selector bg-white p-4 rounded-lg shadow mb-6">
    <h3 class="text-lg font-medium mb-3">เลือกบริษัท</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($companies as $company)
            <div class="card p-4 border rounded-lg {{ session('current_company_id') == $company->id ? 'bg-blue-50 border-blue-300' : 'bg-white border-gray-200' }}">
                <a href="{{ route('dashboard.switch-company', $company) }}" class="block">
                    <h4 class="text-md font-semibold">{{ $company->name }}</h4>
                    <div class="text-sm text-gray-600">{{ $company->tax_id }}</div>
                    @if(session('current_company_id') == $company->id)
                        <div class="mt-2">
                            <span class="px-2 py-1 bg-blue-500 text-white text-xs rounded-full">กำลังใช้งาน</span>
                        </div>
                    @else
                        <div class="mt-2">
                            <span class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded-full">คลิกเพื่อเลือก</span>
                        </div>
                    @endif
                </a>
            </div>
        @endforeach
    </div>
</div>
