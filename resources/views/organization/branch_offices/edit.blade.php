<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('แก้ไขข้อมูลสาขา') }}: {{ $branchOffice->name }}
            </h2>
            <div class="flex space-x-2">

                <a href="{{ route('branch-offices.index') }}" 
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gray-500 border border-gray-500 rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    กลับไปรายการสาขา
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p class="font-bold">เกิดข้อผิดพลาด</p>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('branch-offices.update', $branchOffice) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- ข้อมูลทั่วไป -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2">ข้อมูลทั่วไป</h3>

                                <!-- บริษัท -->
                                <div>
                                    <label for="company_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">บริษัท <span class="text-red-500">*</span></label>
                                    <select id="company_id" name="company_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}" {{ (old('company_id', $branchOffice->company_id) == $company->id) ? 'selected' : '' }}>
                                                {{ $company->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- รหัสสาขา -->
                                <div>
                                    <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">รหัสสาขา <span class="text-red-500">*</span></label>
                                    <input type="text" name="code" id="code" value="{{ old('code', $branchOffice->code) }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>

                                <!-- ชื่อสาขา -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ชื่อสาขา <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $branchOffice->name) }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>

                                <!-- ที่อยู่ -->
                                <div>
                                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ที่อยู่ <span class="text-red-500">*</span></label>
                                    <textarea name="address" id="address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>{{ old('address', $branchOffice->address) }}</textarea>
                                </div>

                                <!-- เบอร์โทรศัพท์ -->
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">เบอร์โทรศัพท์</label>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone', $branchOffice->phone) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>

                                <!-- อีเมล -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">อีเมล</label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $branchOffice->email) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>

                                <!-- ผู้จัดการสาขา -->
                                <div>
                                    <label for="manager_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ผู้จัดการสาขา</label>
                                    <select id="manager_id" name="manager_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">-- เลือกผู้จัดการสาขา --</option>
                                        @foreach($managers as $manager)
                                            <option value="{{ $manager->id }}" data-company-id="{{ $manager->company_id }}" 
                                                {{ old('manager_id', $branchOffice->manager_id) == $manager->id ? 'selected' : '' }}>
                                                {{ $manager->first_name }} {{ $manager->last_name }} - {{ $manager->employee_code }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-sm text-gray-500">ผู้จัดการสาขาควรเป็นพนักงานในบริษัทเดียวกันกับสาขา</p>
                                </div>

                                <!-- สถานะและประเภท -->
                                <div class="flex flex-col sm:flex-row sm:space-x-6 space-y-4 sm:space-y-0">
                                    <div class="flex items-center">
                                        <input type="checkbox" id="is_headquarters" name="is_headquarters" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600" {{ old('is_headquarters', $branchOffice->is_headquarters) ? 'checked' : '' }}>
                                        <label for="is_headquarters" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">เป็นสำนักงานใหญ่</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="is_active" name="is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600" {{ old('is_active', $branchOffice->is_active) ? 'checked' : '' }}>
                                        <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">เปิดใช้งาน</label>
                                    </div>
                                </div>
                            </div>

                            <!-- ข้อมูลเพิ่มเติม -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2">ข้อมูลเพิ่มเติม</h3>

                                <!-- ภูมิภาค -->
                                <div>
                                    <label for="region" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ภูมิภาค</label>
                                    <select id="region" name="region" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">-- เลือกภูมิภาค --</option>
                                        @foreach($regions as $region)
                                            <option value="{{ $region }}" {{ old('region', $metadata['region'] ?? '') == $region ? 'selected' : '' }}>
                                                {{ $region }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- รหัสสาขากรมสรรพากร -->
                                <div>
                                    <label for="tax_branch_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">รหัสสาขากรมสรรพากร</label>
                                    <input type="text" name="tax_branch_id" id="tax_branch_id" value="{{ old('tax_branch_id', $metadata['tax_branch_id'] ?? '') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="00000">
                                    <p class="mt-1 text-sm text-gray-500">สำนักงานใหญ่ใช้ 00000, สาขาที่ 1 ใช้ 00001, สาขาที่ 2 ใช้ 00002 เป็นต้น</p>
                                </div>

                                <!-- วันที่เปิดสาขา -->
                                <div>
                                    <label for="opening_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">วันที่เปิดสาขา</label>
                                    <input type="date" name="opening_date" id="opening_date" value="{{ old('opening_date', $metadata['opening_date'] ?? '') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>

                                <!-- ข้อมูล metadata อื่นๆ -->
                                <div class="border border-gray-200 dark:border-gray-600 rounded-md p-4 bg-gray-50 dark:bg-gray-700">
                                    <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-3">ข้อมูล metadata ทั้งหมด</h4>

                                    <!-- แสดงข้อมูล metadata ที่มีอยู่แล้ว -->
                                    @if(isset($metadata) && is_array($metadata))
                                        @foreach($metadata as $key => $value)
                                            @if(!in_array($key, ['region', 'tax_branch_id', 'opening_date']) && !is_array($value))
                                                <div class="grid grid-cols-2 gap-4 mb-4">
                                                    <div>
                                                        <input type="text" name="metadata[{{ $key }}][key]" value="{{ $key }}" readonly
                                                            class="mt-1 block w-full rounded-md bg-gray-100 border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-700 dark:text-gray-300">
                                                    </div>
                                                    <div>
                                                        <input type="text" name="metadata[{{ $key }}]" value="{{ $value }}" 
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-700 dark:text-gray-300">
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif

                                    <!-- เพิ่มข้อมูล metadata ใหม่ -->
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <input type="text" name="metadata[new_key][key]" placeholder="Key"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-700 dark:text-gray-300">
                                        </div>
                                        <div>
                                            <input type="text" name="metadata[new_key]" placeholder="Value"
                                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-600 dark:border-gray-700 dark:text-gray-300">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                                {{ __('บันทึกการเปลี่ยนแปลง') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/branch-office-form.js') }}"></script>
    @endpush
</x-app-layout>