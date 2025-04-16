<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-extrabold text-4xl text-blue-800">
                    {{ $customer->name }}
                </h2>
                <p class="text-md text-gray-600 dark:text-gray-400">
                    {{ $customer->code ?? 'ไม่มีรหัส' }} | {{ $customer->type == 'company' ? 'บริษัท/องค์กร' : 'บุคคลธรรมดา' }} 
                    @if($customer->reference_id) | อ้างอิงภายนอก: {{ $customer->reference_id }} @endif
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('customers.edit', $customer) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-yellow-500 border border-transparent rounded-md shadow-sm hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 0L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    แก้ไขข้อมูล
                </a>
                <a href="{{ route('customers.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 dark:border-gray-600">
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

            @if(session('error') || isset($error))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') ?? $error }}</span>
                </div>
            @endif

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
                        
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">รหัสลูกค้า</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ $customer->code ?? 'ไม่มีรหัส' }}</dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ชื่อลูกค้า</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ $customer->name }}</dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ประเภทลูกค้า</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ $customer->type == 'company' ? 'บริษัท/องค์กร' : 'บุคคลธรรมดา' }}</dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">สถานะ</dt>
                                    <dd class="mt-1 sm:mt-0 sm:col-span-2">
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $customer->status == 'active' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                            {{ $customer->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">รหัสอ้างอิง</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ $customer->reference_id ?? '-' }}</dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">เป็นซัพพลายเออร์</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $customer->is_supplier ? 'bg-purple-50 text-purple-700' : 'bg-gray-100 text-gray-700' }}">
                                            {{ $customer->is_supplier ? 'ใช่' : 'ไม่ใช่' }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">หมายเหตุ</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ $customer->note ?? '-' }}</dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">วันที่สร้าง</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ $customer->created_at->format('d/m/Y H:i:s') }}</dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">วันที่แก้ไขล่าสุด</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ $customer->updated_at->format('d/m/Y H:i:s') }}</dd>
                                </div>
                            </dl>
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
                        
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">อีเมล</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">
                                        <a href="mailto:{{ $customer->email }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">{{ $customer->email ?? '-' }}</a>
                                    </dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">โทรศัพท์</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">
                                        <a href="tel:{{ $customer->phone }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">{{ $customer->phone ?? '-' }}</a>
                                    </dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">เว็บไซต์</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">
                                        @if($customer->website)
                                            <a href="{{ $customer->website }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300" target="_blank">{{ $customer->website }}</a>
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ที่อยู่</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ $customer->address ?? '-' }}</dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">เลขประจำตัวผู้เสียภาษี</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ $customer->tax_id ?? '-' }}</dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">โซเชียลมีเดีย</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">
                                        @if($customer->social_media && is_array(json_decode($customer->social_media, true)))
                                            <ul class="space-y-1">
                                            @foreach(json_decode($customer->social_media, true) as $platform => $username)
                                                <li>
                                                    <span class="font-medium">{{ ucfirst($platform) }}:</span> {{ $username }}
                                                </li>
                                            @endforeach
                                            </ul>
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">วันที่ติดต่อล่าสุด</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ $customer->last_contacted_date ? \Carbon\Carbon::parse($customer->last_contacted_date)->format('d/m/Y') : '-' }}</dd>
                                </div>
                            </dl>
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
                        
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ชื่อผู้ติดต่อ</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ $customer->contact_person ?? '-' }}</dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ตำแหน่ง</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ $customer->contact_person_position ?? '-' }}</dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">อีเมล</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">
                                        @if($customer->contact_person_email)
                                            <a href="mailto:{{ $customer->contact_person_email }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">{{ $customer->contact_person_email }}</a>
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">เบอร์โทรศัพท์</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">
                                        @if($customer->contact_person_phone)
                                            <a href="tel:{{ $customer->contact_person_phone }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">{{ $customer->contact_person_phone }}</a>
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">LINE ID</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ $customer->contact_person_line_id ?? '-' }}</dd>
                                </div>
                            </dl>
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
                        
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">กลุ่มลูกค้า</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">
                                        @if($customer->customer_group)
                                            <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-blue-50 text-blue-700">
                                                กลุ่ม {{ $customer->customer_group }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">การจัดอันดับ</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">
                                        @if($customer->customer_rating)
                                            <div class="flex">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="h-5 w-5 {{ $i <= $customer->customer_rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                                         xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">อุตสาหกรรม</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">
                                        @if($customer->metadata && isset(json_decode($customer->metadata, true)['industry']))
                                            {{ json_decode($customer->metadata, true)['industry'] }}
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">พื้นที่การขาย</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">
                                        @if($customer->metadata && isset(json_decode($customer->metadata, true)['sales_region']))
                                            {{ json_decode($customer->metadata, true)['sales_region'] }}
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ประเภทการชำระเงิน</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold border-2 shadow-sm text-white"
                                            style="background-color: {{ 
                                                $customer->payment_term_type == 'credit' ? '#4338ca' : 
                                                ($customer->payment_term_type == 'cash' ? '#059669' : 
                                                ($customer->payment_term_type == 'cheque' ? '#b45309' : '#a21caf')) 
                                            }}; border-color: {{ 
                                                $customer->payment_term_type == 'credit' ? '#3730a3' : 
                                                ($customer->payment_term_type == 'cash' ? '#047857' : 
                                                ($customer->payment_term_type == 'cheque' ? '#92400e' : '#86198f')) 
                                            }};">
                                            {{ $customer->payment_term_type == 'credit' ? 'เครดิต' :
                                                ($customer->payment_term_type == 'cash' ? 'เงินสด' : 
                                                ($customer->payment_term_type == 'cheque' ? 'เช็ค' : 'โอนเงิน')) }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">วงเงินเครดิต</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ $customer->credit_limit ? number_format($customer->credit_limit, 2) . ' บาท' : '-' }}</dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ระยะเวลาเครดิต</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">
                                        @if($customer->metadata && isset(json_decode($customer->metadata, true)['credit_term']))
                                            {{ json_decode($customer->metadata, true)['credit_term'] . ' วัน' }}
                                        @else
                                            -
                                        @endif
                                    </dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">อัตราส่วนลดพิเศษ</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ $customer->discount_rate ? $customer->discount_rate . '%' : '-' }}</dd>
                                </div>
                            </dl>
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
                        
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ธนาคาร</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ $customer->bank_name ?? '-' }}</dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">สาขา</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ $customer->bank_branch ?? '-' }}</dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">ชื่อบัญชี</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ $customer->bank_account_name ?? '-' }}</dd>
                                </div>
                                <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">เลขที่บัญชี</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">{{ $customer->bank_account_number ?? '-' }}</dd>
                                </div>
                            </dl>
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
                            ข้อมูลเพิ่มเติม
                        </h3>
                        
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                                @if($customer->metadata && is_array(json_decode($customer->metadata, true)))
                                    @foreach(json_decode($customer->metadata, true) as $key => $value)
                                        @if(!in_array($key, ['industry', 'sales_region', 'credit_term'])) {{-- ข้อมูลที่แสดงในส่วนอื่นแล้ว --}}
                                            <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ ucfirst(str_replace('_', ' ', $key)) }}</dt>
                                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-200 sm:mt-0 sm:col-span-2">
                                                    @if(is_array($value))
                                                        <pre class="whitespace-pre-wrap">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </dd>
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    <div class="py-3">
                                        <p class="text-sm text-gray-500">ไม่มีข้อมูลเพิ่มเติม</p>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- คำสั่งซื้อล่าสุด -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold flex items-center">
                            <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            คำสั่งซื้อล่าสุด
                        </h3>
                        <a href="{{ route('customers.purchase-history', $customer) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm flex items-center">
                            <span>ดูประวัติทั้งหมด</span>
                            <svg class="h-4 w-4 ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                    
                    @if(isset($recentOrders) && $recentOrders->isEmpty())
                        <div class="text-center py-8 text-sm text-gray-500 dark:text-gray-400">
                            ยังไม่มีประวัติการสั่งซื้อ
                        </div>
                    @elseif(isset($recentOrders))
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 table-fixed">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200">
                                        <th class="py-3 px-4 border-b border-gray-300 dark:border-gray-600 text-left font-semibold text-sm w-1/6">เลขที่</th>
                                        <th class="py-3 px-4 border-b border-gray-300 dark:border-gray-600 text-left font-semibold text-sm w-1/6">วันที่</th>
                                        <th class="py-3 px-4 border-b border-gray-300 dark:border-gray-600 text-left font-semibold text-sm w-1/4">อ้างอิง</th>
                                        <th class="py-3 px-4 border-b border-gray-300 dark:border-gray-600 text-left font-semibold text-sm w-1/6">สถานะ</th>
                                        <th class="py-3 px-4 border-b border-gray-300 dark:border-gray-600 text-right font-semibold text-sm w-1/6">มูลค่ารวม</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 border-b border-gray-200 dark:border-gray-700">
                                            <td class="py-3 px-4 text-sm truncate">
                                                <a href="#" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">{{ $order->order_number }}</a>
                                            </td>
                                            <td class="py-3 px-4 text-sm">{{ $order->order_date->format('d/m/Y') }}</td>
                                            <td class="py-3 px-4 text-sm truncate">
                                                @if($order->quotation)
                                                    <a href="#" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">{{ $order->quotation->quotation_number }}</a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-sm">
                                                <span class="inline-flex px-2.5 py-1 rounded-full text-sm font-semibold border-2 shadow-sm text-white"
                                                    style="background-color: {{ 
                                                        $order->status == 'completed' ? '#15803d' : 
                                                        ($order->status == 'pending' ? '#b45309' : 
                                                        ($order->status == 'cancelled' ? '#be123c' : 
                                                        ($order->status == 'confirm' ? '#1d4ed8' : 
                                                        ($order->status == 'delivery' ? '#4338ca' : '#475569')))) 
                                                    }}; border-color: {{ 
                                                        $order->status == 'completed' ? '#166534' : 
                                                        ($order->status == 'pending' ? '#92400e' : 
                                                        ($order->status == 'cancelled' ? '#9f1239' : 
                                                        ($order->status == 'confirm' ? '#1e40af' : 
                                                        ($order->status == 'delivery' ? '#3730a3' : '#334155')))) 
                                                    }};">
                                                    {{ $order->status }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-sm text-right">{{ number_format($order->total_amount, 2) }} บาท</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 text-sm text-gray-500 dark:text-gray-400">
                            ไม่สามารถโหลดข้อมูลคำสั่งซื้อได้
                        </div>
                    @endif
                </div>
            </div>

            <!-- ใบเสนอราคาล่าสุด -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold flex items-center">
                            <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            ใบเสนอราคาล่าสุด
                        </h3>
                        <a href="#" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm flex items-center">
                            <span>ดูประวัติทั้งหมด</span>
                            <svg class="h-4 w-4 ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                    
                    @if(isset($recentQuotations) && $recentQuotations->isEmpty())
                        <div class="text-center py-8 text-sm text-gray-500 dark:text-gray-400">
                            ยังไม่มีประวัติใบเสนอราคา
                        </div>
                    @elseif(isset($recentQuotations))
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 table-fixed">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200">
                                        <th class="py-3 px-4 border-b border-gray-300 dark:border-gray-600 text-left font-semibold text-sm w-1/6">เลขที่</th>
                                        <th class="py-3 px-4 border-b border-gray-300 dark:border-gray-600 text-left font-semibold text-sm w-1/6">วันที่</th>
                                        <th class="py-3 px-4 border-b border-gray-300 dark:border-gray-600 text-left font-semibold text-sm w-1/6">วันที่หมดอายุ</th>
                                        <th class="py-3 px-4 border-b border-gray-300 dark:border-gray-600 text-left font-semibold text-sm w-1/6">สถานะ</th>
                                        <th class="py-3 px-4 border-b border-gray-300 dark:border-gray-600 text-right font-semibold text-sm w-1/4">มูลค่ารวม</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentQuotations as $quotation)
                                        <tr class="hover:bg-gray-100 dark:hover:bg-gray-600 border-b border-gray-200 dark:border-gray-700">
                                            <td class="py-3 px-4 text-sm truncate">
                                                <a href="#" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">{{ $quotation->quotation_number }}</a>
                                            </td>
                                            <td class="py-3 px-4 text-sm">{{ $quotation->quotation_date->format('d/m/Y') }}</td>
                                            <td class="py-3 px-4 text-sm">{{ $quotation->expiration_date->format('d/m/Y') }}</td>
                                            <td class="py-3 px-4 text-sm">
                                                <span class="inline-flex px-2.5 py-1 rounded-full text-sm font-semibold border-2 shadow-sm text-white"
                                                    style="background-color: {{ 
                                                        $quotation->status == 'approved' ? '#059669' : 
                                                        ($quotation->status == 'pending' ? '#4338ca' : 
                                                        ($quotation->status == 'rejected' ? '#a21caf' : 
                                                        ($quotation->status == 'expired' ? '#71717a' : 
                                                        ($quotation->status == 'confirm' ? '#1d4ed8' : 
                                                        ($quotation->status == 'draft' ? '#b45309' : '#525252'))))) 
                                                    }}; border-color: {{ 
                                                        $quotation->status == 'approved' ? '#047857' : 
                                                        ($quotation->status == 'pending' ? '#3730a3' : 
                                                        ($quotation->status == 'rejected' ? '#86198f' : 
                                                        ($quotation->status == 'expired' ? '#52525b' : 
                                                        ($quotation->status == 'confirm' ? '#1e40af' : 
                                                        ($quotation->status == 'draft' ? '#92400e' : '#404040'))))) 
                                                    }};">
                                                    {{ $quotation->status }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4 text-sm text-right">{{ number_format($quotation->total_amount, 2) }} บาท</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8 text-sm text-gray-500 dark:text-gray-400">
                            ไม่สามารถโหลดข้อมูลใบเสนอราคาได้
                        </div>
                    @endif
                </div>
            </div>

            <!-- ปุ่มดำเนินการ -->
            <div class="mt-6 flex justify-end space-x-4">
                <form action="{{ route('customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบลูกค้านี้?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        ลบลูกค้า
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
