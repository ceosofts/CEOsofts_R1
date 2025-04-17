<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-extrabold text-4xl text-blue-800">
                    แก้ไขข้อมูลลูกค้า: {{ $customer->name }}
                </h2>
                <p class="text-md text-gray-600 dark:text-gray-400">
                    {{ $customer->code ?? 'ไม่มีรหัส' }} | {{ $customer->type == 'company' ? 'บริษัท/องค์กร' : 'บุคคลธรรมดา' }}
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('customers.show', $customer) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 dark:border-gray-600">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    กลับ
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

            @if(session('error') || $errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') ?? 'กรุณาตรวจสอบข้อมูลที่กรอก' }}</span>
                    @if($errors->any())
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            <form action="{{ route('customers.update', $customer) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- ข้อมูลพื้นฐาน -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-semibold mb-4 flex items-center">
                                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                ข้อมูลพื้นฐาน
                            </h3>
                            
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-4">
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">รหัสลูกค้า</label>
                                        <input type="text" name="code" id="code" value="{{ old('code', $customer->code) }}" readonly class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 bg-gray-100 dark:border-gray-600 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-md cursor-not-allowed">
                                        <p class="mt-1 text-xs text-gray-500">รหัสลูกค้าไม่สามารถแก้ไขได้</p>
                                    </div>
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ชื่อลูกค้า <span class="text-red-600">*</span></label>
                                        <input type="text" name="name" id="name" value="{{ old('name', $customer->name) }}" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                    <div>
                                        <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ประเภทลูกค้า</label>
                                        <select name="type" id="type" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                            <option value="company" {{ old('type', $customer->type) == 'company' ? 'selected' : '' }}>บริษัท/องค์กร</option>
                                            <option value="person" {{ old('type', $customer->type) == 'person' ? 'selected' : '' }}>บุคคลธรรมดา</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">สถานะ</label>
                                        <select name="status" id="status" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                            <option value="active" {{ old('status', $customer->status) == 'active' ? 'selected' : '' }}>ใช้งาน</option>
                                            <option value="inactive" {{ old('status', $customer->status) == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="reference_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">รหัสอ้างอิง</label>
                                        <input type="text" name="reference_id" id="reference_id" value="{{ old('reference_id', $customer->reference_id) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_supplier" id="is_supplier" value="1" {{ old('is_supplier', $customer->is_supplier) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded">
                                        <label for="is_supplier" class="ml-2 block text-sm font-medium text-gray-700 dark:text-gray-300">เป็นซัพพลายเออร์</label>
                                    </div>
                                    <div>
                                        <label for="note" class="block text-sm font-medium text-gray-700 dark:text-gray-300">หมายเหตุ</label>
                                        <textarea name="note" id="note" rows="3" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">{{ old('note', $customer->note) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ข้อมูลการติดต่อ -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-semibold mb-4 flex items-center">
                                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                ข้อมูลการติดต่อ
                            </h3>
                            
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-4">
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">อีเมล</label>
                                        <input type="email" name="email" id="email" value="{{ old('email', $customer->email) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">โทรศัพท์</label>
                                        <input type="text" name="phone" id="phone" value="{{ old('phone', $customer->phone) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                    <div>
                                        <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300">เว็บไซต์</label>
                                        <input type="url" name="website" id="website" value="{{ old('website', $customer->website) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                    <div>
                                        <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ที่อยู่</label>
                                        <textarea name="address" id="address" rows="3" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">{{ old('address', $customer->address) }}</textarea>
                                    </div>
                                    <div>
                                        <label for="tax_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">เลขประจำตัวผู้เสียภาษี</label>
                                        <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id', $customer->tax_id) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">โซเชียลมีเดีย</label>
                                        
                                        @php
                                            $social_media = old('social_media', $customer->social_media) ?? [];
                                            // แปลงเป็น array ถ้าเป็น string
                                            if (is_string($social_media)) {
                                                $social_media = json_decode($social_media, true) ?? [];
                                            }
                                        @endphp
                                        
                                        <div class="space-y-2">
                                            <div class="grid grid-cols-5 gap-2">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">Facebook</label>
                                                <input type="text" name="social_media[facebook]" value="{{ $social_media['facebook'] ?? '' }}" class="col-span-4 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                            </div>
                                            <div class="grid grid-cols-5 gap-2">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">Line</label>
                                                <input type="text" name="social_media[line]" value="{{ $social_media['line'] ?? '' }}" class="col-span-4 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                            </div>
                                            <div class="grid grid-cols-5 gap-2">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 col-span-1">Instagram</label>
                                                <input type="text" name="social_media[instagram]" value="{{ $social_media['instagram'] ?? '' }}" class="col-span-4 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="last_contacted_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">วันที่ติดต่อล่าสุด</label>
                                        <input type="date" name="last_contacted_date" id="last_contacted_date" value="{{ old('last_contacted_date', $customer->last_contacted_date ? \Carbon\Carbon::parse($customer->last_contacted_date)->format('Y-m-d') : '') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ข้อมูลผู้ติดต่อ -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-semibold mb-4 flex items-center">
                                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                ข้อมูลผู้ติดต่อ
                            </h3>
                            
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-4">
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label for="contact_person" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ชื่อผู้ติดต่อ</label>
                                        <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person', $customer->contact_person) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                    <div>
                                        <label for="contact_person_position" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ตำแหน่ง</label>
                                        <input type="text" name="contact_person_position" id="contact_person_position" value="{{ old('contact_person_position', $customer->contact_person_position) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                    <div>
                                        <label for="contact_person_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">อีเมล</label>
                                        <input type="email" name="contact_person_email" id="contact_person_email" value="{{ old('contact_person_email', $customer->contact_person_email) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                    <div>
                                        <label for="contact_person_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">เบอร์โทรศัพท์</label>
                                        <input type="text" name="contact_person_phone" id="contact_person_phone" value="{{ old('contact_person_phone', $customer->contact_person_phone) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                    <div>
                                        <label for="contact_person_line_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">LINE ID</label>
                                        <input type="text" name="contact_person_line_id" id="contact_person_line_id" value="{{ old('contact_person_line_id', $customer->contact_person_line_id) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ข้อมูลธุรกิจและการเงิน -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-semibold mb-4 flex items-center">
                                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                ข้อมูลธุรกิจและการเงิน
                            </h3>
                            
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-4">
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label for="customer_group" class="block text-sm font-medium text-gray-700 dark:text-gray-300">กลุ่มลูกค้า</label>
                                        <input type="text" name="customer_group" id="customer_group" value="{{ old('customer_group', $customer->customer_group) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                    <div>
                                        <label for="customer_rating" class="block text-sm font-medium text-gray-700 dark:text-gray-300">การจัดอันดับ (1-5)</label>
                                        <select name="customer_rating" id="customer_rating" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                            <option value="">-- เลือกการจัดอันดับ --</option>
                                            @for($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}" {{ old('customer_rating', $customer->customer_rating) == $i ? 'selected' : '' }}>{{ $i }} ดาว</option>
                                            @endfor
                                        </select>
                                    </div>
                                    
                                    @php
                                        $metadata = old('metadata', $customer->metadata) ?? [];
                                        // แปลงเป็น array ถ้าเป็น string
                                        if (is_string($metadata)) {
                                            $metadata = json_decode($metadata, true) ?? [];
                                        }
                                    @endphp
                                    
                                    <div>
                                        <label for="industry" class="block text-sm font-medium text-gray-700 dark:text-gray-300">อุตสาหกรรม</label>
                                        <input type="text" name="metadata[industry]" id="industry" value="{{ $metadata['industry'] ?? '' }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                    <div>
                                        <label for="sales_region" class="block text-sm font-medium text-gray-700 dark:text-gray-300">พื้นที่การขาย</label>
                                        <input type="text" name="metadata[sales_region]" id="sales_region" value="{{ $metadata['sales_region'] ?? '' }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                    <div>
                                        <label for="payment_term_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ประเภทการชำระเงิน</label>
                                        <select name="payment_term_type" id="payment_term_type" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                            <option value="credit" {{ old('payment_term_type', $customer->payment_term_type) == 'credit' ? 'selected' : '' }}>เครดิต</option>
                                            <option value="cash" {{ old('payment_term_type', $customer->payment_term_type) == 'cash' ? 'selected' : '' }}>เงินสด</option>
                                            <option value="cheque" {{ old('payment_term_type', $customer->payment_term_type) == 'cheque' ? 'selected' : '' }}>เช็ค</option>
                                            <option value="transfer" {{ old('payment_term_type', $customer->payment_term_type) == 'transfer' ? 'selected' : '' }}>โอนเงิน</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="credit_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">วงเงินเครดิต (บาท)</label>
                                        <input type="number" step="0.01" min="0" name="credit_limit" id="credit_limit" value="{{ old('credit_limit', $customer->credit_limit) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                    <div>
                                        <label for="credit_term" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ระยะเวลาเครดิต (วัน)</label>
                                        <input type="number" min="0" name="metadata[credit_term]" id="credit_term" value="{{ $metadata['credit_term'] ?? '' }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                    <div>
                                        <label for="discount_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">อัตราส่วนลดพิเศษ (%)</label>
                                        <input type="number" step="0.01" min="0" max="100" name="discount_rate" id="discount_rate" value="{{ old('discount_rate', $customer->discount_rate) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ข้อมูลธนาคาร -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-semibold mb-4 flex items-center">
                                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                ข้อมูลธนาคาร
                            </h3>
                            
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-4">
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label for="bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ธนาคาร</label>
                                        <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $customer->bank_name) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                    <div>
                                        <label for="bank_branch" class="block text-sm font-medium text-gray-700 dark:text-gray-300">สาขา</label>
                                        <input type="text" name="bank_branch" id="bank_branch" value="{{ old('bank_branch', $customer->bank_branch) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                    <div>
                                        <label for="bank_account_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ชื่อบัญชี</label>
                                        <input type="text" name="bank_account_name" id="bank_account_name" value="{{ old('bank_account_name', $customer->bank_account_name) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                    <div>
                                        <label for="bank_account_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">เลขที่บัญชี</label>
                                        <input type="text" name="bank_account_number" id="bank_account_number" value="{{ old('bank_account_number', $customer->bank_account_number) }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ข้อมูลเพิ่มเติม (Metadata) -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-semibold mb-4 flex items-center">
                                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                ข้อมูลเพิ่มเติม (คุณสมบัติพิเศษ)
                            </h3>
                            
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-4">
                                <div class="grid gap-4" id="additional-fields">
                                    @if($metadata)
                                        @foreach($metadata as $key => $value)
                                            @if(!in_array($key, ['industry', 'sales_region', 'credit_term']))
                                                <div class="grid grid-cols-12 gap-2 additional-field-row">
                                                    <div class="col-span-5">
                                                        <input type="text" name="metadata_keys[]" value="{{ $key }}" placeholder="ชื่อฟิลด์" class="focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                                    </div>
                                                    <div class="col-span-5">
                                                        <input type="text" name="metadata_values[]" value="{{ $value }}" placeholder="ค่า" class="focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                                    </div>
                                                    <div class="col-span-2 flex items-center">
                                                        <button type="button" class="remove-field text-red-600 hover:text-red-800">
                                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif

                                    <!-- สำหรับฟิลด์ใหม่ -->
                                    <div class="grid-cols-12 gap-2 additional-field-row hidden" id="additional-field-template">
                                        <div class="col-span-5">
                                            <input type="text" name="metadata_keys[]" placeholder="ชื่อฟิลด์" class="focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                        </div>
                                        <div class="col-span-5">
                                            <input type="text" name="metadata_values[]" placeholder="ค่า" class="focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                        </div>
                                        <div class="col-span-2 flex items-center">
                                            <button type="button" class="remove-field text-red-600 hover:text-red-800">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <button type="button" id="add-field" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        เพิ่มฟิลด์ใหม่
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ปุ่มบันทึก -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('customers.show', $customer) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 dark:border-gray-600">
                        ยกเลิก
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        บันทึกข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // เพิ่มฟิลด์เพิ่มเติม
            document.getElementById('add-field').addEventListener('click', function() {
                const template = document.getElementById('additional-field-template');
                const newField = template.cloneNode(true);
                newField.classList.remove('hidden');
                newField.classList.add('grid'); // เพิ่มคลาส grid เมื่อแสดงแถวใหม่
                newField.removeAttribute('id');
                
                const additionalFields = document.getElementById('additional-fields');
                additionalFields.insertBefore(newField, template);
                
                // เพิ่ม event listener สำหรับปุ่มลบ
                newField.querySelector('.remove-field').addEventListener('click', function() {
                    newField.remove();
                });
            });
            
            // เพิ่ม event listener สำหรับปุ่มลบที่มีอยู่แล้ว
            document.querySelectorAll('.remove-field').forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('.additional-field-row');
                    if (row && !row.classList.contains('hidden')) {
                        row.remove();
                    }
                });
            });
        });
    </script>
</x-app-layout>
