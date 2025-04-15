<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800">
                {{ __('แก้ไขข้อมูลบริษัท') }}
            </h2>
            <a href="{{ route('companies.show', $company) }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
                {{ __('กลับไปรายละเอียดบริษัท') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                <h3 class="font-bold mb-2">โปรดแก้ไขข้อผิดพลาดต่อไปนี้:</h3>
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('companies.update', $company) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- ส่วนข้อมูลทั่วไป -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4 text-blue-600 dark:text-blue-400 border-b pb-2">ข้อมูลทั่วไป</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- รหัสบริษัท -->
                            <div>
                                <x-input-label for="code" :value="__('รหัสบริษัท')" class="required" />
                                <x-text-input id="code" name="code" type="text" class="mt-1 block w-full"
                                    :value="old('code', $company->code)" required />
                                <x-input-error :messages="$errors->get('code')" class="mt-2" />
                            </div>

                            <!-- ชื่อบริษัท -->
                            <div>
                                <x-input-label for="name" :value="__('ชื่อบริษัท')" class="required" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                    :value="old('name', $company->name)" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- อีเมล -->
                            <div>
                                <x-input-label for="email" :value="__('อีเมล')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                    :value="old('email', $company->email)" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- โทรศัพท์ -->
                            <div>
                                <x-input-label for="phone" :value="__('เบอร์โทรศัพท์')" />
                                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
                                    :value="old('phone', $company->phone)" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>

                            <!-- เว็บไซต์ -->
                            <div>
                                <x-input-label for="website" :value="__('เว็บไซต์')" />
                                <x-text-input id="website" name="website" type="url" class="mt-1 block w-full"
                                    :value="old('website', $company->website)" />
                                <x-input-error :messages="$errors->get('website')" class="mt-2" />
                            </div>

                            <!-- เลขประจำตัวผู้เสียภาษี -->
                            <div>
                                <x-input-label for="tax_id" :value="__('เลขประจำตัวผู้เสียภาษี')" />
                                <x-text-input id="tax_id" name="tax_id" type="text" class="mt-1 block w-full"
                                    :value="old('tax_id', $company->tax_id)" />
                                <x-input-error :messages="$errors->get('tax_id')" class="mt-2" />
                            </div>

                            <!-- สถานะ -->
                            <div class="col-span-full">
                                <label for="is_active" class="inline-flex items-center">
                                    <input id="is_active" name="is_active" type="checkbox"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                        {{ (old('is_active', $company->is_active) ? 'checked' : '') }}>
                                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('เปิดใช้งาน') }}</span>
                                </label>
                            </div>

                            <!-- โลโก้ -->
                            <div class="col-span-full">
                                <x-input-label for="logo" :value="__('โลโก้บริษัท')" />
                                <input type="file" id="logo" name="logo" class="mt-1 block w-full border p-2 rounded-md border-gray-300 dark:border-gray-700" />
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">เฉพาะไฟล์ภาพ (JPEG, PNG, GIF, SVG), ขนาดสูงสุด 2MB</p>

                                @if($company->logo)
                                <div class="mt-2 flex items-center">
                                    <div class="w-16 h-16 overflow-hidden border rounded-md">
                                        <img src="{{ Storage::url($company->logo) }}" alt="{{ $company->name }}" class="h-full w-full object-contain">
                                    </div>
                                    <p class="ml-2 text-sm text-gray-500 dark:text-gray-400">โลโก้ปัจจุบัน</p>
                                </div>
                                @endif

                                <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                            </div>

                            <!-- ที่อยู่ -->
                            <div class="col-span-full">
                                <x-input-label for="address" :value="__('ที่อยู่')" />
                                <textarea id="address" name="address" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', $company->address) }}</textarea>
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ส่วนข้อมูลการจดทะเบียน -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4 text-blue-600 dark:text-blue-400 border-b pb-2">ข้อมูลการจดทะเบียน</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- เลขทะเบียนนิติบุคคล -->
                            <div>
                                <x-input-label for="registration_number" :value="__('เลขทะเบียนนิติบุคคล')" />
                                <x-text-input id="registration_number" name="registration_number" type="text" class="mt-1 block w-full"
                                    :value="old('registration_number', $company->registration_number)" />
                                <x-input-error :messages="$errors->get('registration_number')" class="mt-2" />
                            </div>

                            <!-- วันที่จดทะเบียน -->
                            <div>
                                <x-input-label for="registration_date" :value="__('วันที่จดทะเบียน')" />
                                <x-text-input id="registration_date" name="registration_date" type="date" class="mt-1 block w-full"
                                    :value="old('registration_date', $company->registration_date ? $company->registration_date->format('Y-m-d') : '')" />
                                <x-input-error :messages="$errors->get('registration_date')" class="mt-2" />
                            </div>

                            <!-- ทุนจดทะเบียน -->
                            <div>
                                <x-input-label for="registered_capital" :value="__('ทุนจดทะเบียน (บาท)')" />
                                <x-text-input id="registered_capital" name="registered_capital" type="number" step="0.01" class="mt-1 block w-full"
                                    :value="old('registered_capital', $company->registered_capital)" />
                                <x-input-error :messages="$errors->get('registered_capital')" class="mt-2" />
                            </div>

                            <!-- ประเภทธุรกิจ -->
                            <div>
                                <x-input-label for="business_type" :value="__('ประเภทธุรกิจ')" />
                                <x-text-input id="business_type" name="business_type" type="text" class="mt-1 block w-full"
                                    :value="old('business_type', $company->business_type)" />
                                <x-input-error :messages="$errors->get('business_type')" class="mt-2" />
                            </div>

                            <!-- ประเภทบริษัท -->
                            <div>
                                <x-input-label for="company_type" :value="__('ประเภทบริษัท')" />
                                <select id="company_type" name="company_type" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- เลือกประเภท --</option>
                                    <option value="บริษัทจำกัด" {{ old('company_type', $company->company_type) == 'บริษัทจำกัด' ? 'selected' : '' }}>บริษัทจำกัด</option>
                                    <option value="บริษัทมหาชนจำกัด" {{ old('company_type', $company->company_type) == 'บริษัทมหาชนจำกัด' ? 'selected' : '' }}>บริษัทมหาชนจำกัด</option>
                                    <option value="ห้างหุ้นส่วนจำกัด" {{ old('company_type', $company->company_type) == 'ห้างหุ้นส่วนจำกัด' ? 'selected' : '' }}>ห้างหุ้นส่วนจำกัด</option>
                                    <option value="ห้างหุ้นส่วนสามัญนิติบุคคล" {{ old('company_type', $company->company_type) == 'ห้างหุ้นส่วนสามัญนิติบุคคล' ? 'selected' : '' }}>ห้างหุ้นส่วนสามัญนิติบุคคล</option>
                                    <option value="กิจการเจ้าของคนเดียว" {{ old('company_type', $company->company_type) == 'กิจการเจ้าของคนเดียว' ? 'selected' : '' }}>กิจการเจ้าของคนเดียว</option>
                                </select>
                                <x-input-error :messages="$errors->get('company_type')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ส่วนข้อมูลสาขา -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4 text-blue-600 dark:text-blue-400 border-b pb-2">ข้อมูลสาขา</h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- รหัสสาขา -->
                            <div>
                                <x-input-label for="branch_code" :value="__('รหัสสาขา')" />
                                <x-text-input id="branch_code" name="branch_code" type="text" class="mt-1 block w-full"
                                    :value="old('branch_code', $company->branch_code)" />
                                <x-input-error :messages="$errors->get('branch_code')" class="mt-2" />
                            </div>

                            <!-- ชื่อสาขา -->
                            <div>
                                <x-input-label for="branch_name" :value="__('ชื่อสาขา')" />
                                <x-text-input id="branch_name" name="branch_name" type="text" class="mt-1 block w-full"
                                    :value="old('branch_name', $company->branch_name)" />
                                <x-input-error :messages="$errors->get('branch_name')" class="mt-2" />
                            </div>

                            <!-- ประเภทสาขา -->
                            <div>
                                <x-input-label for="branch_type" :value="__('ประเภทสาขา')" />
                                <select id="branch_type" name="branch_type" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- เลือกประเภท --</option>
                                    <option value="สำนักงานใหญ่" {{ old('branch_type', $company->branch_type) == 'สำนักงานใหญ่' ? 'selected' : '' }}>สำนักงานใหญ่</option>
                                    <option value="สาขา" {{ old('branch_type', $company->branch_type) == 'สาขา' ? 'selected' : '' }}>สาขา</option>
                                </select>
                                <x-input-error :messages="$errors->get('branch_type')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ส่วนข้อมูลผู้ติดต่อ -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4 text-blue-600 dark:text-blue-400 border-b pb-2">ข้อมูลผู้ติดต่อ</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- ชื่อผู้ติดต่อ -->
                            <div>
                                <x-input-label for="contact_person" :value="__('ชื่อผู้ติดต่อ')" />
                                <x-text-input id="contact_person" name="contact_person" type="text" class="mt-1 block w-full"
                                    :value="old('contact_person', $company->contact_person)" />
                                <x-input-error :messages="$errors->get('contact_person')" class="mt-2" />
                            </div>

                            <!-- ตำแหน่งผู้ติดต่อ -->
                            <div>
                                <x-input-label for="contact_position" :value="__('ตำแหน่งผู้ติดต่อ')" />
                                <x-text-input id="contact_position" name="contact_position" type="text" class="mt-1 block w-full"
                                    :value="old('contact_position', $company->contact_position)" />
                                <x-input-error :messages="$errors->get('contact_position')" class="mt-2" />
                            </div>

                            <!-- อีเมลผู้ติดต่อ -->
                            <div>
                                <x-input-label for="contact_email" :value="__('อีเมลผู้ติดต่อ')" />
                                <x-text-input id="contact_email" name="contact_email" type="email" class="mt-1 block w-full"
                                    :value="old('contact_email', $company->contact_email)" />
                                <x-input-error :messages="$errors->get('contact_email')" class="mt-2" />
                            </div>

                            <!-- เบอร์โทรผู้ติดต่อ -->
                            <div>
                                <x-input-label for="contact_phone" :value="__('เบอร์โทรผู้ติดต่อ')" />
                                <x-text-input id="contact_phone" name="contact_phone" type="text" class="mt-1 block w-full"
                                    :value="old('contact_phone', $company->contact_phone)" />
                                <x-input-error :messages="$errors->get('contact_phone')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ส่วนข้อมูลการตั้งค่าเพิ่มเติม -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4 text-blue-600 dark:text-blue-400 border-b pb-2">ข้อมูลเพิ่มเติมและการตั้งค่า</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- ข้อมูลการตั้งค่า (JSON) -->
                            <div>
                                <h4 class="font-medium text-base mb-3">ข้อมูลการตั้งค่า</h4>

                                @php
                                $settings = json_decode($company->settings, true) ?: [];
                                $invoice_prefix = $settings['invoice_prefix'] ?? '';
                                $receipt_prefix = $settings['receipt_prefix'] ?? '';
                                @endphp

                                <div class="space-y-4">
                                    <div>
                                        <x-input-label for="invoice_prefix" :value="__('คำนำหน้าใบแจ้งหนี้')" />
                                        <x-text-input id="invoice_prefix" name="invoice_prefix" type="text" class="mt-1 block w-full"
                                            value="{{ old('invoice_prefix', $invoice_prefix) }}" />
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">เช่น: INV-CEOSOFT</p>
                                    </div>

                                    <div>
                                        <x-input-label for="receipt_prefix" :value="__('คำนำหน้าใบเสร็จ')" />
                                        <x-text-input id="receipt_prefix" name="receipt_prefix" type="text" class="mt-1 block w-full"
                                            value="{{ old('receipt_prefix', $receipt_prefix) }}" />
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">เช่น: REC-CEOSOFT</p>
                                    </div>

                                    <div class="pt-2">
                                        <details>
                                            <summary class="cursor-pointer text-sm text-blue-600 dark:text-blue-400 pb-2">ปรับแต่งเพิ่มเติม (JSON)</summary>
                                            <textarea id="settings" name="settings" rows="3"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 font-mono text-xs">{{ old('settings', $company->settings) }}</textarea>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">ข้อมูล JSON จะถูกอัปเดตอัตโนมัติเมื่อกรอกข้อมูลด้านบน</p>
                                        </details>
                                    </div>
                                </div>
                            </div>

                            <!-- ข้อมูลเพิ่มเติม (JSON) -->
                            <div>
                                <h4 class="font-medium text-base mb-3">ข้อมูลเพิ่มเติม</h4>

                                @php
                                $metadata = json_decode($company->metadata, true) ?: [];
                                $founded_year = $metadata['founded_year'] ?? '';
                                $industry = $metadata['industry'] ?? '';
                                @endphp

                                <div class="space-y-4">
                                    <div>
                                        <x-input-label for="founded_year" :value="__('ปีที่ก่อตั้ง')" />
                                        <x-text-input id="founded_year" name="founded_year" type="number" min="1900" max="2100" class="mt-1 block w-full"
                                            value="{{ old('founded_year', $founded_year) }}" />
                                    </div>

                                    <div>
                                        <x-input-label for="industry" :value="__('ประเภทอุตสาหกรรม')" />
                                        <select id="industry" name="industry" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">-- เลือกประเภท --</option>
                                            <option value="Software Development" {{ old('industry', $industry) == 'Software Development' ? 'selected' : '' }}>Software Development</option>
                                            <option value="Technology" {{ old('industry', $industry) == 'Technology' ? 'selected' : '' }}>Technology</option>
                                            <option value="Finance" {{ old('industry', $industry) == 'Finance' ? 'selected' : '' }}>Finance</option>
                                            <option value="Healthcare" {{ old('industry', $industry) == 'Healthcare' ? 'selected' : '' }}>Healthcare</option>
                                            <option value="Retail" {{ old('industry', $industry) == 'Retail' ? 'selected' : '' }}>Retail</option>
                                            <option value="Manufacturing" {{ old('industry', $industry) == 'Manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                            <option value="Education" {{ old('industry', $industry) == 'Education' ? 'selected' : '' }}>Education</option>
                                            <option value="Other" {{ old('industry', $industry) == 'Other' ? 'selected' : '' }}>Other</option>
                                        </select>
                                    </div>

                                    <div class="pt-2">
                                        <details>
                                            <summary class="cursor-pointer text-sm text-blue-600 dark:text-blue-400 pb-2">ปรับแต่งเพิ่มเติม (JSON)</summary>
                                            <textarea id="metadata" name="metadata" rows="3"
                                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 font-mono text-xs">{{ old('metadata', $company->metadata) }}</textarea>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">ข้อมูล JSON จะถูกอัปเดตอัตโนมัติเมื่อกรอกข้อมูลด้านบน</p>
                                        </details>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ปุ่มดำเนินการ -->
                <div class="flex items-center justify-end">
                    <a href="{{ route('companies.show', $company) }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition mr-3">
                        {{ __('ยกเลิก') }}
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">
                        {{ __('บันทึกการเปลี่ยนแปลง') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

<style>
    .required:after {
        content: " *";
        color: red;
    }
</style>

<!-- เพิ่ม JavaScript เพื่อแปลงข้อมูลเป็น JSON ก่อนส่งฟอร์ม -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');

        // เพิ่ม event listener สำหรับการส่งฟอร์ม
        form.addEventListener('submit', function(event) {
            // สร้างข้อมูล settings JSON
            const settings = {
                invoice_prefix: document.getElementById('invoice_prefix').value,
                receipt_prefix: document.getElementById('receipt_prefix').value
            };

            // สร้างข้อมูล metadata JSON
            const metadata = {
                founded_year: document.getElementById('founded_year').value ? parseInt(document.getElementById('founded_year').value) : null,
                industry: document.getElementById('industry').value
            };

            // อัปเดตฟิลด์ JSON
            document.getElementById('settings').value = JSON.stringify(settings);
            document.getElementById('metadata').value = JSON.stringify(metadata);
        });

        // อัปเดต JSON เมื่อมีการเปลี่ยนแปลงค่าในฟิลด์
        const settingFields = ['invoice_prefix', 'receipt_prefix'];
        const metadataFields = ['founded_year', 'industry'];

        settingFields.forEach(field => {
            document.getElementById(field).addEventListener('input', updateSettingsJSON);
        });

        metadataFields.forEach(field => {
            document.getElementById(field).addEventListener('input', updateMetadataJSON);
            document.getElementById(field).addEventListener('change', updateMetadataJSON);
        });

        function updateSettingsJSON() {
            const settings = {
                invoice_prefix: document.getElementById('invoice_prefix').value,
                receipt_prefix: document.getElementById('receipt_prefix').value
            };
            document.getElementById('settings').value = JSON.stringify(settings);
        }

        function updateMetadataJSON() {
            const metadata = {
                founded_year: document.getElementById('founded_year').value ? parseInt(document.getElementById('founded_year').value) : null,
                industry: document.getElementById('industry').value
            };
            document.getElementById('metadata').value = JSON.stringify(metadata);
        }
    });
</script>