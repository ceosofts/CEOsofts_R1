<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800">
                {{ __('ข้อมูลพนักงาน') }}: {{ $employee->first_name }} {{ $employee->last_name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('employees.edit', $employee) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-yellow-600 border border-transparent rounded-md shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ __('แก้ไขข้อมูล') }}
                </a>
                <a href="{{ route('employees.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('กลับไปยังรายการพนักงาน') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="col-span-2">
                            <h2 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">ข้อมูลส่วนบุคคล</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    @if($employee->profile_image)
                                        <img src="{{ Storage::url($employee->profile_image) }}" alt="Profile Image" class="w-32 h-32 rounded-full object-cover mb-4">
                                    @else
                                        <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center mb-4">
                                            <span class="text-gray-500 text-2xl">{{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600">รหัสพนักงาน</p>
                                        <p class="font-medium">{{ $employee->employee_code ?? 'ไม่ระบุ' }}</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600">คำนำหน้า</p>
                                        <p class="font-medium">{{ $employee->title ?? 'ไม่ระบุ' }}</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600">ชื่อ - นามสกุล</p>
                                        <p class="font-medium">{{ $employee->first_name }} {{ $employee->last_name }}</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600">ชื่อเล่น</p>
                                        <p class="font-medium">{{ $employee->nickname ?? 'ไม่ระบุ' }}</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600">เพศ</p>
                                        <p class="font-medium">{{ $employee->gender ?? 'ไม่ระบุ' }}</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600">วันเกิด</p>
                                        <p class="font-medium">{{ $employee->birthdate ? date('d/m/Y', strtotime($employee->birthdate)) : 'ไม่ระบุ' }}</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600">เลขบัตรประชาชน</p>
                                        <p class="font-medium">{{ $employee->id_card_number ?? 'ไม่ระบุ' }}</p>
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600">สัญชาติ</p>
                                        <p class="font-medium">{{ $employee->nationality ?? 'ไม่ระบุ' }}</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600">ศาสนา</p>
                                        <p class="font-medium">{{ $employee->religion ?? 'ไม่ระบุ' }}</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600">สถานะสมรส</p>
                                        <p class="font-medium">{{ $employee->marital_status ?? 'ไม่ระบุ' }}</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600">กรุ๊ปเลือด</p>
                                        <p class="font-medium">{{ $employee->blood_type ?? 'ไม่ระบุ' }}</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600">ส่วนสูง</p>
                                        <p class="font-medium">{{ $employee->height ? $employee->height . ' ซม.' : 'ไม่ระบุ' }}</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600">น้ำหนัก</p>
                                        <p class="font-medium">{{ $employee->weight ? $employee->weight . ' กก.' : 'ไม่ระบุ' }}</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600">โรคประจำตัว</p>
                                        <p class="font-medium">{{ $employee->medical_conditions ?? 'ไม่ระบุ' }}</p>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600">ที่อยู่</p>
                                        <p class="font-medium">{{ $employee->address ?? 'ไม่ระบุ' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ข้อมูลการติดต่อ -->
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">ข้อมูลการติดต่อ</h2>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">อีเมล</p>
                                <p class="font-medium">{{ $employee->email ?? 'ไม่ระบุ' }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">อีเมลบริษัท</p>
                                <p class="font-medium">{{ $employee->company_email ?? 'ไม่ระบุ' }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">เบอร์โทรศัพท์</p>
                                <p class="font-medium">{{ $employee->phone ?? 'ไม่ระบุ' }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">ผู้ติดต่อฉุกเฉิน</p>
                                <p class="font-medium">{{ $employee->emergency_contact_name ?? 'ไม่ระบุ' }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">เบอร์ติดต่อฉุกเฉิน</p>
                                <p class="font-medium">{{ $employee->emergency_contact_phone ?? 'ไม่ระบุ' }}</p>
                            </div>

                            <h2 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4 mt-6">ข้อมูลการทำงาน</h2>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">บริษัท</p>
                                <p class="font-medium">{{ $employee->company->name ?? 'ไม่ระบุ' }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">แผนก</p>
                                <p class="font-medium">{{ $employee->department->name ?? 'ไม่ระบุ' }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">ตำแหน่ง</p>
                                <p class="font-medium">{{ $employee->position->name ?? 'ไม่ระบุ' }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">สาขา</p>
                                <p class="font-medium">{{ $employee->branchOffice->name ?? 'ไม่ระบุ' }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">ผู้จัดการ</p>
                                <p class="font-medium">{{ $employee->manager ? $employee->manager->first_name . ' ' . $employee->manager->last_name : 'ไม่ระบุ' }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">ประเภทพนักงาน</p>
                                <p class="font-medium">{{ $employee->employee_type ?? 'ไม่ระบุ' }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">วันที่เริ่มงาน</p>
                                <p class="font-medium">{{ $employee->hire_date ? date('d/m/Y', strtotime($employee->hire_date)) : 'ไม่ระบุ' }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">วันที่สิ้นสุดทดลองงาน</p>
                                <p class="font-medium">{{ $employee->probation_end_date ? date('d/m/Y', strtotime($employee->probation_end_date)) : 'ไม่ระบุ' }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm text-gray-600">สถานะพนักงาน</p>
                                <p class="font-medium">{{ $employee->status === 'active' ? 'ทำงาน' : 'ไม่ทำงาน' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- เพิ่มเติม: ข้อมูลการศึกษา, ธนาคาร, เอกสาร ฯลฯ -->
                    <div class="mt-8">
                        <h2 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">ข้อมูลการศึกษาและประสบการณ์</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">วุฒิการศึกษา</p>
                                    <p class="font-medium">{{ $employee->education_level ?? 'ไม่ระบุ' }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">สถานศึกษา</p>
                                    <p class="font-medium">{{ $employee->education_institute ?? 'ไม่ระบุ' }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">สาขาวิชา</p>
                                    <p class="font-medium">{{ $employee->education_major ?? 'ไม่ระบุ' }}</p>
                                </div>
                            </div>
                            
                            <div>
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">ประสบการณ์ทำงาน (ปี)</p>
                                    <p class="font-medium">{{ $employee->years_experience ?? 'ไม่ระบุ' }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">ทักษะ</p>
                                    <p class="font-medium">{{ $employee->skills ?? 'ไม่ระบุ' }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">ประกาศนียบัตร</p>
                                    <p class="font-medium">{{ $employee->certificates ?? 'ไม่ระบุ' }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">ประวัติการทำงานที่ผ่านมา</p>
                                    <p class="font-medium">{{ $employee->previous_employment ?? 'ไม่ระบุ' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ข้อมูลธนาคารและภาษี -->
                    <div class="mt-8">
                        <h2 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">ข้อมูลธนาคารและภาษี</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">ธนาคาร</p>
                                    <p class="font-medium">{{ $employee->bank_name ?? 'ไม่ระบุ' }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">เลขบัญชี</p>
                                    <p class="font-medium">{{ $employee->bank_account ?? 'ไม่ระบุ' }}</p>
                                </div>
                            </div>
                            
                            <div>
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">เลขประจำตัวผู้เสียภาษี</p>
                                    <p class="font-medium">{{ $employee->tax_id ?? 'ไม่ระบุ' }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">สถานะการยื่นภาษี</p>
                                    <p class="font-medium">{{ $employee->tax_filing_status ?? 'ไม่ระบุ' }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">เลขประกันสังคม</p>
                                    <p class="font-medium">{{ $employee->social_security_number ?? 'ไม่ระบุ' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ข้อมูลเอกสาร -->
                    <div class="mt-8">
                        <h2 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">ข้อมูลเอกสาร</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">เลขที่หนังสือเดินทาง</p>
                                    <p class="font-medium">{{ $employee->passport_number ?? 'ไม่ระบุ' }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">วันหมดอายุหนังสือเดินทาง</p>
                                    <p class="font-medium">{{ $employee->passport_expiry ? date('d/m/Y', strtotime($employee->passport_expiry)) : 'ไม่ระบุ' }}</p>
                                </div>
                            </div>
                            
                            <div>
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">เลขที่ใบอนุญาตทำงาน</p>
                                    <p class="font-medium">{{ $employee->work_permit_number ?? 'ไม่ระบุ' }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">วันหมดอายุใบอนุญาตทำงาน</p>
                                    <p class="font-medium">{{ $employee->work_permit_expiry ? date('d/m/Y', strtotime($employee->work_permit_expiry)) : 'ไม่ระบุ' }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">ประเภทวีซ่า</p>
                                    <p class="font-medium">{{ $employee->visa_type ?? 'ไม่ระบุ' }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">วันหมดอายุวีซ่า</p>
                                    <p class="font-medium">{{ $employee->visa_expiry ? date('d/m/Y', strtotime($employee->visa_expiry)) : 'ไม่ระบุ' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ข้อมูลเพิ่มเติม (Metadata) -->
                    @if(isset($employee->metadata) && is_array($employee->metadata) && count($employee->metadata) > 0)
                    <div class="mt-8">
                        <h2 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">ข้อมูลเพิ่มเติม</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($employee->metadata as $key => $value)
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $key)) }}</p>
                                    <p class="font-medium">{{ $value }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
