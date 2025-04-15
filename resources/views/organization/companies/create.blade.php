<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('เพิ่มบริษัทใหม่') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('companies.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- รหัสบริษัท -->
                        <div class="mb-4">
                            <x-input-label for="code" :value="__('รหัสบริษัท')" />
                            <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" :value="old('code')" required autofocus />
                            <x-input-error :messages="$errors->get('code')" class="mt-2" />
                        </div>

                        <!-- ชื่อบริษัท -->
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('ชื่อบริษัท')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- อีเมล -->
                        <div class="mb-4">
                            <x-input-label for="email" :value="__('อีเมล')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- เบอร์โทรศัพท์ -->
                        <div class="mb-4">
                            <x-input-label for="phone" :value="__('เบอร์โทรศัพท์')" />
                            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>

                        <!-- เลขประจำตัวผู้เสียภาษี -->
                        <div class="mb-4">
                            <x-input-label for="tax_id" :value="__('เลขประจำตัวผู้เสียภาษี')" />
                            <x-text-input id="tax_id" class="block mt-1 w-full" type="text" name="tax_id" :value="old('tax_id')" />
                            <x-input-error :messages="$errors->get('tax_id')" class="mt-2" />
                        </div>

                        <!-- ที่อยู่ -->
                        <div class="mb-4">
                            <x-input-label for="address" :value="__('ที่อยู่')" />
                            <textarea id="address" name="address" rows="3" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">{{ old('address') }}</textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <!-- โลโก้บริษัท -->
                        <div class="mb-4">
                            <x-input-label for="logo" :value="__('โลโก้บริษัท')" />
                            <input id="logo" type="file" name="logo" class="block mt-1 w-full text-sm text-gray-900 dark:text-gray-300 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 focus:outline-none" accept="image/*">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">PNG, JPG หรือ GIF (สูงสุด 2MB)</p>
                            <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                        </div>

                        <!-- สถานะ -->
                        <div class="mb-4">
                            <label for="is_active" class="inline-flex items-center">
                                <input id="is_active" type="checkbox" name="is_active" class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600" checked>
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('เปิดใช้งาน') }}</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('companies.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 mr-3">
                                {{ __('ยกเลิก') }}
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