<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800">
                {{ __('แก้ไขแผนก') }}: {{ $department->name }}
            </h2>
            <div>
                <a href="{{ route('departments.show', $department->id) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('ย้อนกลับ') }}
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

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('departments.update', $department->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="mb-4">
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ชื่อแผนก <span class="text-red-600">*</span></label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $department->name) }}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @error('name')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">รหัสแผนก</label>
                                    <input type="text" name="code" id="code" value="{{ old('code', $department->code) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @error('code')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="company_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">บริษัท <span class="text-red-600">*</span></label>
                                    <select name="company_id" id="company_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="">-- เลือกบริษัท --</option>

                                        @php
                                        // ตรวจสอบว่าหากไม่มีบริษัทที่ส่งมา แต่มีบริษัทปัจจุบัน
                                        $hasCurrentCompany = isset($department->company) && $department->company;
                                        @endphp

                                        @if(isset($companies) && count($companies) > 0)
                                        @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ (old('company_id', $department->company_id) == $company->id) ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                        @endforeach
                                        @elseif($hasCurrentCompany)
                                        {{-- แสดงเฉพาะบริษัทปัจจุบันหากไม่มีรายการบริษัทอื่นๆ --}}
                                        <option value="{{ $department->company_id }}" selected>
                                            {{ $department->company->name }} (บริษัทปัจจุบัน)
                                        </option>
                                        <option value="" disabled>-- ไม่พบบริษัทอื่นในระบบ --</option>
                                        @else
                                        <option value="" disabled selected>-- ไม่พบบริษัท กรุณาสร้างบริษัทก่อน --</option>
                                        @endif
                                    </select>
                                    @error('company_id')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Hidden fields to preserve values but not show them -->
                                <input type="hidden" name="parent_id" value="{{ old('parent_id', $department->parent_id) }}">
                                <!-- ลบ input สำหรับ head_position_id -->
                            </div>

                            <div>
                                <div class="mb-4">
                                    <label for="is_active" class="block text-sm font-medium text-gray-700 dark:text-gray-300">สถานะ</label>
                                    <select name="is_active" id="is_active"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                        <option value="1" {{ old('is_active', $department->is_active) == 1 ? 'selected' : '' }}>ใช้งาน</option>
                                        <option value="0" {{ old('is_active', $department->is_active) == 0 ? 'selected' : '' }}>ไม่ใช้งาน</option>
                                    </select>
                                    @error('is_active')
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-4 col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">คำอธิบาย</label>
                            <textarea name="description" id="description" rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('description', $department->description) }}</textarea>
                            @error('description')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ __('บันทึก') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>