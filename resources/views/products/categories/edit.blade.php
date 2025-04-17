<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800">
                {{ __('แก้ไขหมวดหมู่สินค้า') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('product-categories.show', $productCategory) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    ดูรายละเอียด
                </a>
                
                <a href="{{ route('product-categories.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    กลับไปยังรายการ
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

            @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <!-- แสดงข้อมูลเดิม -->
                    <!-- <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                        <h3 class="text-md font-medium text-blue-800 mb-2">ข้อมูลเดิม</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">บริษัท:</p>
                                <p class="mt-1 text-sm font-semibold">
                                    @if($productCategory->company)
                                        {{ $productCategory->company->company_name }} (รหัส: {{ $productCategory->company_id }})
                                    @else
                                        ไม่ระบุบริษัท
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">รหัสหมวดหมู่:</p>
                                <p class="mt-1 text-sm font-semibold">{{ $productCategory->code }}</p>
                            </div>
                        </div>
                    </div> -->

                    <form method="POST" action="{{ route('product-categories.update', $productCategory) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Company ID -->
                            <div>
                                <x-input-label for="company_id" :value="__('บริษัท')" class="font-semibold text-base" />
                                

                                
                                <!-- กลับไปใช้ dropdown แบบเดิม -->
                                <select id="company_id" name="company_id" 
                                    class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    required>
                                    <option value="">-- เลือกบริษัท --</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id', $productCategory->company_id) == $company->id ? 'selected' : '' }}>
                                            {{ $company->company_name ?? $company->name ?? "บริษัท {$company->id}" }}
                                        </option>
                                    @endforeach
                                </select>
                                
                                <!-- แสดงบริษัทปัจจุบัน -->
                                <!-- @if($productCategory->company)
                                    <p class="mt-1 text-sm text-gray-600">
                                        บริษัทปัจจุบัน: <span class="font-medium text-blue-700">{{ $productCategory->company->company_name ?? $productCategory->company->name ?? "บริษัท {$productCategory->company_id}" }}</span>
                                    </p>
                                @endif -->
                                
                                                                <!-- แสดง debug info แบบที่สามารถปิดได้ -->
                                <details class="mb-2">
                                    <summary class="text-xs text-blue-600 cursor-pointer">แสดงข้อมูล debug</summary>
                                    <div class="mt-1 p-2 border border-dashed border-blue-300 bg-blue-50 text-xs">
                                        <strong>Debug - ข้อมูลบริษัทที่มี ({{ $companies->count() }} บริษัท):</strong><br>
                                        @foreach($companies as $index => $company)
                                            {{ $index + 1 }}. ID: {{ $company->id }} - 
                                            ชื่อบริษัท: {{ $company->company_name ?? $company->name ?? $company->title ?? 'ไม่มีชื่อ' }}<br>
                                        @endforeach
                                    </div>
                                </details>

                                <x-input-error :messages="$errors->get('company_id')" class="mt-2" />
                            </div>

                            <!-- Category Code -->
                            <div>
                                <x-input-label for="category_code" :value="__('รหัสหมวดหมู่')" />
                                <x-text-input id="category_code" class="block mt-1 w-full" type="text" name="category_code" :value="old('category_code', $productCategory->code)" required autofocus maxlength="20" />
                                <x-input-error :messages="$errors->get('category_code')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Category Name -->
                        <div class="mt-4">
                            <x-input-label for="category_name" :value="__('ชื่อหมวดหมู่')" />
                            <x-text-input id="category_name" class="block mt-1 w-full" type="text" name="category_name" :value="old('category_name', $productCategory->name)" required maxlength="100" />
                            <x-input-error :messages="$errors->get('category_name')" class="mt-2" />
                        </div>

                        <!-- Parent Category -->
                        <div class="mt-4">
                            <x-input-label for="parent_id" :value="__('หมวดหมู่หลัก (ถ้ามี)')" />
                            
                            <!-- เปลี่ยนจาก x-select เป็น select ธรรมดา -->
                            <select id="parent_id" name="parent_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm bg-white">
                                <option value="">ไม่มีหมวดหมู่หลัก</option>
                                @foreach ($possibleParents as $parentCategory)
                                    <option value="{{ $parentCategory->id }}" {{ old('parent_id', $productCategory->parent_id) == $parentCategory->id ? 'selected' : '' }}>
                                        {{ $parentCategory->name }}
                                    </option>
                                @endforeach
                            </select>
                            
                            <!-- เพิ่มการแสดงชื่อหมวดหมู่หลักปัจจุบัน -->
                            @if($productCategory->parent)
                                <p class="mt-1 text-sm text-gray-500">หมวดหมู่หลักปัจจุบัน: <span class="font-medium text-blue-700">{{ $productCategory->parent->name }}</span></p>
                            @endif
                            
                            <x-input-error :messages="$errors->get('parent_id')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mt-4">
                            <x-input-label for="description" :value="__('รายละเอียด')" />
                            <x-textarea id="description" class="block mt-1 w-full" name="description" maxlength="255">{{ old('description', $productCategory->description) }}</x-textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Is Active -->
                        <div class="mt-4">
                            <label class="inline-flex items-center">
                                <input id="is_active" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" name="is_active" value="1" {{ old('is_active', $productCategory->is_active) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-600">{{ __('สถานะ (เปิดใช้งาน)') }}</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('product-categories.show', $productCategory) }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-4">
                                ยกเลิก
                            </a>
                            <x-primary-button>
                                {{ __('บันทึกการแก้ไข') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const companySelect = document.getElementById('company_id');
            const parentSelect = document.getElementById('parent_id');
            
            companySelect.addEventListener('change', function() {
                const companyId = this.value;
                
                // ล้างค่าเก่า
                parentSelect.innerHTML = '<option value="">ไม่มีหมวดหมู่หลัก</option>';
                
                if (companyId) {
                    // Fetch categories from the same company
                    fetch(`/api/companies/${companyId}/categories?exclude={{ $productCategory->id }}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(category => {
                                const option = document.createElement('option');
                                option.value = category.id;
                                option.textContent = category.name;
                                parentSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error('Error fetching categories:', error));
                }
            });
        });
    </script>
</x-app-layout>
