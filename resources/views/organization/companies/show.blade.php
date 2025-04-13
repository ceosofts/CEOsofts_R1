<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl  text-blue-800">
                {{ __('รายละเอียดบริษัท') }}
            </h2>
            <div>
                <a href="{{ route('companies.edit', $company) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition mr-2">
                    {{ __('แก้ไขข้อมูล') }}
                </a>
                <a href="{{ route('companies.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
                    {{ __('กลับไปรายการบริษัท') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(app()->environment('local') && isset($debug))
            <div id="debugInfo" class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded-lg" style="display: none;">
                <div class="flex justify-between">
                    <h3 class="font-bold">Debug Information:</h3>
                    <button onclick="document.getElementById('debugInfo').style.display='none';" class="text-yellow-800 hover:text-yellow-900">
                        <span>×</span>
                    </button>
                </div>
                <p>Company ID: {{ $company->id ?? 'ไม่มีข้อมูล' }}</p>
                <p>Company Name: {{ $company->name ?? 'ไม่มีข้อมูล' }}</p>
                <p>UUID: {{ $company->uuid ?? 'ไม่มีข้อมูล' }}</p>
            </div>

            <div class="mb-4">
                <button onclick="document.getElementById('debugInfo').style.display='block';" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    แสดงข้อมูล Debug
                </button>
            </div>
            @endif

            <!-- ส่วนแสดงโลโก้และข้อมูลทั่วไป -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- ส่วนแสดงโลโก้บริษัท -->
                        <div class="md:col-span-1">
                            <div class="border dark:border-gray-700 rounded-lg p-4 text-center">
                                @if(isset($company->logo) && $company->logo)
                                <img src="{{ Storage::url($company->logo) }}" alt="{{ $company->name }}" class="mx-auto h-40 w-auto">
                                @else
                                <div class="h-40 w-40 bg-gray-200 dark:bg-gray-600 flex items-center justify-center rounded-full mx-auto">
                                    <span class="text-gray-600 dark:text-gray-300 text-5xl font-bold">{{ substr($company->name, 0, 1) }}</span>
                                </div>
                                @endif
                                <div class="mt-2">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full 
                                          {{ $company->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $company->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <h3 class="font-medium text-lg">{{ $company->name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $company->code }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- ส่วนแสดงข้อมูลทั่วไป -->
                        <div class="md:col-span-2">
                            <div class="border dark:border-gray-700 rounded-lg p-4">
                                <h3 class="text-lg font-semibold mb-4">ข้อมูลทั่วไป</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">รหัสบริษัท</p>
                                        <p class="font-medium">{{ $company->code ?? 'ไม่ระบุ' }}</p>
                                    </div>

                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">ชื่อบริษัท</p>
                                        <p class="font-medium">{{ $company->name }}</p>
                                    </div>

                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">อีเมล</p>
                                        <p class="font-medium">{{ $company->email ?? '-' }}</p>
                                    </div>

                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">เบอร์โทรศัพท์</p>
                                        <p class="font-medium">{{ $company->phone ?? '-' }}</p>
                                    </div>

                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">เว็บไซต์</p>
                                        <p class="font-medium">{{ $company->website ?? '-' }}</p>
                                    </div>

                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">เลขประจำตัวผู้เสียภาษี</p>
                                        <p class="font-medium">{{ $company->tax_id ?? '-' }}</p>
                                    </div>

                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">UUID</p>
                                        <p class="font-medium text-xs">{{ $company->uuid ?? '-' }}</p>
                                    </div>

                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">ULID</p>
                                        <p class="font-medium text-xs">{{ $company->ulid ?? '-' }}</p>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">ที่อยู่</p>
                                    <p class="font-medium">{{ $company->address ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ส่วนแสดงข้อมูลการจดทะเบียน -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">ข้อมูลการจดทะเบียน</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">เลขทะเบียนนิติบุคคล</p>
                            <p class="font-medium">{{ $company->registration_number ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">วันที่จดทะเบียน</p>
                            <p class="font-medium">{{ $company->registration_date ? $company->registration_date->format('d/m/Y') : '-' }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">ทุนจดทะเบียน</p>
                            <p class="font-medium">{{ $company->registered_capital ? number_format($company->registered_capital, 2) . ' บาท' : '-' }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">ประเภทธุรกิจ</p>
                            <p class="font-medium">{{ $company->business_type ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">ประเภทบริษัท</p>
                            <p class="font-medium">{{ $company->company_type ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ส่วนแสดงข้อมูลสาขา -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">ข้อมูลสาขา</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">รหัสสาขา</p>
                            <p class="font-medium">{{ $company->branch_code ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">ชื่อสาขา</p>
                            <p class="font-medium">{{ $company->branch_name ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">ประเภทสาขา</p>
                            <p class="font-medium">{{ $company->branch_type ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ส่วนแสดงข้อมูลผู้ติดต่อ -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">ข้อมูลผู้ติดต่อ</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">ชื่อผู้ติดต่อ</p>
                            <p class="font-medium">{{ $company->contact_person ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">ตำแหน่งผู้ติดต่อ</p>
                            <p class="font-medium">{{ $company->contact_position ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">อีเมลผู้ติดต่อ</p>
                            <p class="font-medium">{{ $company->contact_email ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">เบอร์โทรผู้ติดต่อ</p>
                            <p class="font-medium">{{ $company->contact_phone ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ส่วนแสดงข้อมูลเพิ่มเติมและการตั้งค่า -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">ข้อมูลเพิ่มเติมและการตั้งค่า</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- ข้อมูลการตั้งค่า -->
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mb-2">ข้อมูลการตั้งค่า (Settings)</p>
                            @if($company->settings)
                            @php
                            $settings = json_decode($company->settings, true);
                            @endphp
                            @if(is_array($settings) && count($settings) > 0)
                            <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                        <tr>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ชื่อการตั้งค่า</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ค่า</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                                        @foreach($settings as $key => $value)
                                        <tr>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $key }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                @if(is_array($value) || is_object($value))
                                                {{ json_encode($value, JSON_UNESCAPED_UNICODE) }}
                                                @else
                                                {{ $value }}
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-gray-500 dark:text-gray-400">ไม่สามารถแปลงข้อมูลการตั้งค่าได้</p>
                            @endif
                            @else
                            <p class="font-medium">-</p>
                            @endif
                        </div>

                        <!-- Metadata -->
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mb-2">Metadata</p>
                            @if($company->metadata)
                            @php
                            $metadata = json_decode($company->metadata, true);
                            @endphp
                            @if(is_array($metadata) && count($metadata) > 0)
                            <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                        <tr>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ชื่อข้อมูล</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ค่า</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                                        @foreach($metadata as $key => $value)
                                        <tr>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $key }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                @if(is_array($value) || is_object($value))
                                                {{ json_encode($value, JSON_UNESCAPED_UNICODE) }}
                                                @else
                                                {{ $value }}
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-gray-500 dark:text-gray-400">ไม่สามารถแปลงข้อมูล Metadata ได้</p>
                            @endif
                            @else
                            <p class="font-medium">-</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- ส่วนแสดงข้อมูลระบบ -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">ข้อมูลระบบ</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">วันที่สร้าง</p>
                            <p class="font-medium">{{ isset($company->created_at) ? $company->created_at->format('d/m/Y H:i:s') : '-' }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">วันที่อัปเดต</p>
                            <p class="font-medium">{{ isset($company->updated_at) ? $company->updated_at->format('d/m/Y H:i:s') : '-' }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">วันที่ลบ (ถ้ามี)</p>
                            <p class="font-medium">{{ isset($company->deleted_at) ? $company->deleted_at->format('d/m/Y H:i:s') : '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ส่วนแสดงข้อมูลแผนก/ตำแหน่งที่เกี่ยวข้อง -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">ข้อมูลที่เกี่ยวข้อง</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h4 class="font-medium mb-2 text-indigo-600 dark:text-indigo-400">แผนก</h4>
                            @if(isset($company->departments) && $company->departments->count() > 0)
                            <ul class="list-disc list-inside">
                                @foreach($company->departments as $department)
                                <li>{{ $department->name }}</li>
                                @endforeach
                            </ul>
                            @else
                            <p class="text-gray-500">ไม่มีข้อมูลแผนก</p>
                            @endif
                        </div>

                        <div>
                            <h4 class="font-medium mb-2 text-indigo-600 dark:text-indigo-400">ตำแหน่ง</h4>
                            @if(isset($company->positions) && $company->positions->count() > 0)
                            <ul class="list-disc list-inside">
                                @foreach($company->positions as $position)
                                <li>{{ $position->name }}</li>
                                @endforeach
                            </ul>
                            @else
                            <p class="text-gray-500">ไม่มีข้อมูลตำแหน่ง</p>
                            @endif
                        </div>

                        <div>
                            <h4 class="font-medium mb-2 text-indigo-600 dark:text-indigo-400">พนักงาน</h4>
                            <p class="text-gray-700 dark:text-gray-300">จำนวนพนักงานทั้งหมด: {{ isset($company->employees) ? $company->employees->count() : 0 }} คน</p>
                        </div>
                    </div>

                    <!-- ปุ่มดำเนินการ -->
                    <div class="mt-6 flex justify-end">
                        <form action="{{ route('companies.destroy', $company) }}" method="POST" class="inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบบริษัทนี้?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition">
                                {{ __('ลบบริษัทนี้') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>