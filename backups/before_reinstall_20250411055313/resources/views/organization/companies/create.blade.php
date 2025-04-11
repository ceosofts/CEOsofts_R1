<x-app-layout>
    <x-slot name="header">
        เพิ่มบริษัทใหม่
    </x-slot>

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="space-y-6">
                    <!-- ชื่อบริษัท -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            ชื่อบริษัท <span class="text-red-600">*</span>
                        </label>
                        <div class="mt-1">
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm">
                        </div>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- เลขประจำตัวผู้เสียภาษี -->
                    <div>
                        <label for="tax_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            เลขประจำตัวผู้เสียภาษี
                        </label>
                        <div class="mt-1">
                            <input type="text" id="tax_id" name="tax_id" value="{{ old('tax_id') }}"
                                class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm">
                        </div>
                        @error('tax_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- ที่อยู่ -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            ที่อยู่
                        </label>
                        <div class="mt-1">
                            <textarea id="address" name="address" rows="3"
                                class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm">{{ old('address') }}</textarea>
                        </div>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- โทรศัพท์ -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                โทรศัพท์
                            </label>
                            <div class="mt-1">
                                <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm">
                            </div>
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- อีเมล -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                อีเมล
                            </label>
                            <div class="mt-1">
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                    class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm">
                            </div>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- เว็บไซต์ -->
                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            เว็บไซต์
                        </label>
                        <div class="mt-1">
                            <input type="url" id="website" name="website" value="{{ old('website') }}"
                                class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 shadow-sm">
                        </div>
                        @error('website')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- โลโก้ -->
                    <div>
                        <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            โลโก้บริษัท
                        </label>
                        <div class="mt-1">
                            <input type="file" id="logo" name="logo" accept="image/*"
                                class="block w-full text-sm text-gray-700 dark:text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        </div>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">รูปภาพ PNG, JPG, GIF ขนาดไม่เกิน 2MB</p>
                        @error('logo')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- สถานะ -->
                    <div>
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="is_active" name="is_active" type="checkbox" value="1" checked
                                    class="h-4 w-4 rounded border-gray-300 dark:border-gray-700 text-primary-600 focus:ring-primary-500 dark:bg-gray-900">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_active" class="font-medium text-gray-700 dark:text-gray-300">เปิดใช้งาน</label>
                                <p class="text-gray-500 dark:text-gray-400">บริษัทนี้จะสามารถใช้งานในระบบได้ทันที</p>
                            </div>
                        </div>
                    </div>

                    <!-- สร้างแผนกเริ่มต้น -->
                    <div>
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="create_default_departments" name="create_default_departments" type="checkbox" value="1" checked
                                    class="h-4 w-4 rounded border-gray-300 dark:border-gray-700 text-primary-600 focus:ring-primary-500 dark:bg-gray-900">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="create_default_departments" class="font-medium text-gray-700 dark:text-gray-300">สร้างแผนกเริ่มต้น</label>
                                <p class="text-gray-500 dark:text-gray-400">สร้างแผนกพื้นฐานให้กับบริษัทโดยอัตโนมัติ</p>
                            </div>
                        </div>
                    </div>

                    <!-- ปุ่มกดส่งฟอร์ม -->
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('companies.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-700 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            ยกเลิก
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            บันทึก
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
