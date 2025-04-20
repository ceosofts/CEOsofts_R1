<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h1 class="text-2xl font-semibold">แผงควบคุมผู้บริหาร {{ isset($company) ? '- '.$company->name : '' }}</h1>
                        
                        <!-- ส่วนเลือกบริษัท -->
                        @if(isset($userCompanies) && $userCompanies->count() > 0)
                            <div class="relative">
                                <form action="{{ route('executive.switch-company') }}" method="POST" class="flex items-center">
                                    @csrf
                                    <label for="company_selector" class="mr-2 text-sm">เลือกบริษัท:</label>
                                    <select name="company_id" id="company_selector" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="this.form.submit()">
                                        @foreach($userCompanies as $userCompany)
                                            <option value="{{ $userCompany->id }}" {{ session('company_id') == $userCompany->id ? 'selected' : '' }}>
                                                {{ $userCompany->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        @endif
                    </div>
                    
                    @if(session('success'))
                        <div class="bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-200 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-200 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    @if(isset($error))
                        <div class="bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-200 px-4 py-3 rounded mb-4">
                            <h3 class="font-bold">ข้อผิดพลาด</h3>
                            <p>{{ $error }}</p>
                            @if(config('app.debug') && isset($debug))
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">Debug info: {{ $debug }}</p>
                            @endif
                            
                            @if(isset($userCompanies) && $userCompanies->count() > 0)
                                <div class="mt-4">
                                    <p>กรุณาเลือกบริษัทจากตัวเลือกด้านบน</p>
                                </div>
                            @else
                                <div class="mt-4 space-y-3">
                                    <p>เนื่องจากคุณยังไม่ได้รับสิทธิ์เข้าถึงข้อมูลบริษัทใดๆ คุณสามารถ:</p>
                                    <ol class="list-decimal list-inside ml-4 space-y-2">
                                        <li>ติดต่อผู้ดูแลระบบเพื่อขอสิทธิ์เข้าถึงบริษัท</li>
                                        <li>สร้างบริษัทใหม่ หากคุณมีสิทธิ์ในการสร้างบริษัท</li>
                                        <li>ส่งคำขอสิทธิ์เข้าถึงบริษัทจากระบบ</li>
                                    </ol>
                                    
                                    <div class="mt-6 flex space-x-4">
                                        <a href="{{ route('companies.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring ring-blue-300 disabled:opacity:25 transition ease-in-out duration-150">
                                            ดูรายการบริษัททั้งหมด
                                        </a>
                                        
                                        <a href="{{ route('companies.create') }}" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring ring-green-300 disabled:opacity:25 transition ease-in-out duration-150">
                                            สร้างบริษัทใหม่
                                        </a>
                                        
                                        <button id="requestAccessBtn" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:border-yellow-700 focus:ring ring-yellow-300 disabled:opacity:25 transition ease-in-out duration-150">
                                            ส่งคำขอสิทธิ์เข้าถึงบริษัท
                                        </button>
                                    </div>
                                    
                                    <!-- Modal สำหรับส่งคำขอเข้าถึงบริษัท -->
                                    <div id="accessRequestModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-75 items-center justify-center z-50">
                                        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
                                            <h3 class="text-lg font-medium mb-4">ส่งคำขอสิทธิ์เข้าถึงบริษัท</h3>
                                            <form id="accessRequestForm" action="{{ route('company.request-access') }}" method="POST">
                                                @csrf
                                                <div class="mb-4">
                                                    <label class="block text-sm font-medium mb-1">ชื่อบริษัทที่ต้องการเข้าถึง:</label>
                                                    <input type="text" name="company_name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required placeholder="ระบุชื่อบริษัท">
                                                </div>
                                                <div class="mb-4">
                                                    <label class="block text-sm font-medium mb-1">เหตุผลที่ต้องการเข้าถึง:</label>
                                                    <textarea name="reason" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required placeholder="ระบุเหตุผลที่ต้องการเข้าถึงบริษัทนี้"></textarea>
                                                </div>
                                                <div class="flex justify-end space-x-3">
                                                    <button type="button" id="closeModalBtn" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">ยกเลิก</button>
                                                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">ส่งคำขอ</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <script>
                                        // JavaScript สำหรับควบคุม modal
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const requestAccessBtn = document.getElementById('requestAccessBtn');
                                            const accessRequestModal = document.getElementById('accessRequestModal');
                                            const closeModalBtn = document.getElementById('closeModalBtn');
                                            
                                            requestAccessBtn.addEventListener('click', function() {
                                                // แก้ไขการสลับคลาส - ลบ hidden และเพิ่ม flex เมื่อแสดง modal
                                                accessRequestModal.classList.remove('hidden');
                                                accessRequestModal.classList.add('flex');
                                            });
                                            
                                            closeModalBtn.addEventListener('click', function() {
                                                // แก้ไขการสลับคลาส - เพิ่ม hidden และลบ flex เมื่อซ่อน modal
                                                accessRequestModal.classList.add('hidden');
                                                accessRequestModal.classList.remove('flex');
                                            });
                                            
                                            // ปิด modal เมื่อคลิกภายนอก
                                            accessRequestModal.addEventListener('click', function(e) {
                                                if (e.target === accessRequestModal) {
                                                    accessRequestModal.classList.add('hidden');
                                                    accessRequestModal.classList.remove('flex');
                                                }
                                            });
                                        });
                                    </script>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- สรุปข้อมูลองค์กร -->
                        <!-- ...existing code... -->
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>