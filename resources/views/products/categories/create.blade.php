<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800">
                {{ __('เพิ่มหมวดหมู่สินค้า') }}
            </h2>
            <a href="{{ route('product-categories.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                กลับไปยังรายการ
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('product-categories.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Company ID -->
                            <div>
                                <x-input-label for="company_id" :value="__('บริษัท')" />
                                <x-select id="company_id" name="company_id" class="block mt-1 w-full" required>
                                    <option value="">เลือกบริษัท</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->company_name }}
                                        </option>
                                    @endforeach
                                </x-select>
                                <x-input-error :messages="$errors->get('company_id')" class="mt-2" />
                            </div>

                            <!-- Category Code -->
                            <div>
                                <x-input-label for="category_code" :value="__('รหัสหมวดหมู่')" />
                                <x-text-input id="category_code" class="block mt-1 w-full" type="text" name="category_code" :value="old('category_code')" required autofocus maxlength="20" />
                                <x-input-error :messages="$errors->get('category_code')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Category Name -->
                        <div class="mt-4">
                            <x-input-label for="category_name" :value="__('ชื่อหมวดหมู่')" />
                            <x-text-input id="category_name" class="block mt-1 w-full" type="text" name="category_name" :value="old('category_name')" required maxlength="100" />
                            <x-input-error :messages="$errors->get('category_name')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mt-4">
                            <x-input-label for="description" :value="__('รายละเอียด')" />
                            <x-textarea id="description" class="block mt-1 w-full" name="description" maxlength="255">{{ old('description') }}</x-textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Is Active -->
                        <div class="mt-4">
                            <label class="inline-flex items-center">
                                <input id="is_active" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-600">{{ __('สถานะ (เปิดใช้งาน)') }}</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('product-categories.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-4">
                                ยกเลิก
                            </a>
                            <x-primary-button>
                                {{ __('บันทึก') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
