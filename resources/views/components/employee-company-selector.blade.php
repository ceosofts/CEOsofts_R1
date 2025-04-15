@props(['companies'])

<div class="company-selector bg-white dark:bg-gray-800 p-4 rounded-lg shadow mb-6">
    <h3 class="text-lg font-medium mb-3">เลือกบริษัทเพื่อดูข้อมูลพนักงาน</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($companies as $company)
            <div class="company-card" data-company-id="{{ $company->id }}">
                <div class="p-4 border rounded-lg {{ session('current_company_id') == $company->id ? 'bg-blue-50 dark:bg-blue-900 border-blue-300 dark:border-blue-700' : 'bg-white dark:bg-gray-700 border-gray-200 dark:border-gray-600' }}">
                    <a href="{{ route('switch.company', ['company' => $company->id, 'ref' => url()->full()]) }}" class="block company-selector-link">
                        <h4 class="text-md font-semibold {{ session('current_company_id') == $company->id ? 'text-blue-800 dark:text-blue-200' : 'text-gray-800 dark:text-gray-200' }}">{{ $company->name }}</h4>
                        <div class="text-sm {{ session('current_company_id') == $company->id ? 'text-blue-600 dark:text-blue-300' : 'text-gray-600 dark:text-gray-400' }}">
                            {{ $company->tax_id ?? 'ไม่ระบุเลขประจำตัวผู้เสียภาษี' }}
                        </div>
                        
                        <div class="mt-2 status-badge">
                            @if(session('current_company_id') == $company->id)
                                <span class="px-2 py-1 bg-blue-500 text-white text-xs rounded-full">กำลังเลือก</span>
                            @else
                                <span class="px-2 py-1 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs rounded-full">คลิกเพื่อเลือก</span>
                            @endif
                        </div>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>

@once
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // อาจเพิ่มโค้ด JavaScript ที่นี่ถ้าต้องการจัดการการเลือกบริษัทด้วย AJAX (ไม่จำเป็นในการแก้ไขนี้)
    });
    </script>
    @endpush
@endonce
