<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800">
                {{ __('แก้ไขตำแหน่ง') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800">
                    <form action="{{ route('positions.update', $position->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- ชื่อตำแหน่ง -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ชื่อตำแหน่ง <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name', $position->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- รหัสตำแหน่ง -->
                            <div>
                                <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">รหัสตำแหน่ง</label>
                                <input type="text" name="code" id="code" value="{{ old('code', $position->code) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <p class="text-sm text-gray-500 mt-1">รหัสตำแหน่งในแต่ละบริษัทต้องไม่ซ้ำกัน</p>
                                @error('code')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- บริษัท -->
                            <div>
                                <label for="company_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">บริษัท</label>
                                <select name="company_id" id="company_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">-- เลือกบริษัท --</option>
                                    @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ (old('company_id', $position->company_id) == $company->id) ? 'selected' : '' }}>{{ $company->name }}</option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- แผนก -->
                            <div>
                                <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">แผนก</label>
                                <select name="department_id" id="department_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">-- เลือกแผนก --</option>
                                    @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ (old('department_id', $position->department_id) == $department->id) ? 'selected' : '' }}>{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- ระดับตำแหน่ง -->
                            <div>
                                <label for="level" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ระดับตำแหน่ง</label>
                                <input type="number" name="level" id="level" value="{{ old('level', $position->level) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('level')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- เงินเดือนขั้นต่ำ -->
                            <div>
                                <label for="min_salary" class="block text-sm font-medium text-gray-700 dark:text-gray-300">เงินเดือนขั้นต่ำ (บาท)</label>
                                <input type="number" name="min_salary" id="min_salary" value="{{ old('min_salary', $position->min_salary) }}" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('min_salary')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- เงินเดือนขั้นสูง -->
                            <div>
                                <label for="max_salary" class="block text-sm font-medium text-gray-700 dark:text-gray-300">เงินเดือนขั้นสูง (บาท)</label>
                                <input type="number" name="max_salary" id="max_salary" value="{{ old('max_salary', $position->max_salary) }}" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('max_salary')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- การใช้งาน -->
                            <div class="flex items-center mt-6">
                                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $position->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                <label for="is_active" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">เปิดใช้งานตำแหน่ง</label>
                                @error('is_active')
                                <p class="text-red-500 text-xs ml-6">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- คำอธิบาย -->
                        <div class="mt-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">คำอธิบาย</label>
                            <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('description', $position->description) }}</textarea>
                            @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ข้อมูลเพิ่มเติม (JSON Metadata) - แบบ Collapsible -->
                        <div class="mt-6">
                            <button type="button" id="metadata-toggle" class="flex items-center justify-between w-full px-4 py-2 text-sm font-medium text-left text-blue-600 bg-blue-100 rounded-lg hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <span>ข้อมูลเพิ่มเติม (JSON)</span>
                                <svg id="metadata-arrow" class="w-5 h-5 transform transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 011.414 1.414l-4 4a1 1 01-1.414 0l-4-4a1 1 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </button>
                            <div id="metadata-content" class="mt-2 hidden">
                                <div class="border border-gray-200 rounded-md p-4 bg-gray-50 dark:bg-gray-700 dark:border-gray-600">
                                    <div class="mb-3">
                                        <div class="border-b border-gray-200">
                                            <nav class="-mb-px flex space-x-4">
                                                <button type="button" id="edit-tab" class="px-3 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600">
                                                    แก้ไข
                                                </button>
                                                <button type="button" id="view-tab" class="px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 border-b-2 border-transparent">
                                                    ดูข้อมูล
                                                </button>
                                            </nav>
                                        </div>
                                    </div>

                                    <!-- Edit mode -->
                                    <div id="edit-mode">
                                        <textarea name="metadata" id="metadata" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm font-mono dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('metadata', is_array($position->metadata) ? json_encode($position->metadata, JSON_PRETTY_PRINT) : $position->metadata) }}</textarea>
                                    </div>

                                    <!-- View mode (formatted JSON) -->
                                    <div id="view-mode" class="hidden">
                                        <pre id="json-viewer" class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-50 p-3 h-[120px] overflow-auto text-sm font-mono dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"></pre>
                                    </div>

                                    <p class="text-sm text-gray-500 mt-1">ใส่ข้อมูลในรูปแบบ JSON เช่น {"en_name": "Manager", "reports_to": "CEO", "grade": "M1"}</p>
                                    @error('metadata')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-between">
                            <a href="{{ route('positions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                ยกเลิก
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                บันทึกการเปลี่ยนแปลง
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Validate JSON on submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const metadataField = document.getElementById('metadata');
            try {
                if (metadataField.value.trim() !== '') {
                    JSON.parse(metadataField.value);
                }
            } catch (error) {
                e.preventDefault();
                alert('ข้อมูล JSON ไม่ถูกต้อง กรุณาตรวจสอบ');
            }
        });

        // JSON viewer/editor toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const editTab = document.getElementById('edit-tab');
            const viewTab = document.getElementById('view-tab');
            const editMode = document.getElementById('edit-mode');
            const viewMode = document.getElementById('view-mode');
            const metadataField = document.getElementById('metadata');
            const jsonViewer = document.getElementById('json-viewer');

            // Toggle collapsible metadata section
            const metadataToggle = document.getElementById('metadata-toggle');
            const metadataContent = document.getElementById('metadata-content');
            const metadataArrow = document.getElementById('metadata-arrow');
            
            metadataToggle.addEventListener('click', function() {
                metadataContent.classList.toggle('hidden');
                metadataArrow.classList.toggle('rotate-180');
            });

            // Function to update the JSON viewer
            function updateJsonViewer() {
                try {
                    const json = metadataField.value.trim();
                    if (json) {
                        const parsed = JSON.parse(json);
                        jsonViewer.textContent = JSON.stringify(parsed, null, 2);
                    } else {
                        jsonViewer.textContent = '';
                    }
                } catch (e) {
                    jsonViewer.textContent = 'ข้อมูล JSON ไม่ถูกต้อง';
                }
            }

            // Initial update
            updateJsonViewer();

            // Update JSON viewer when metadata changes
            metadataField.addEventListener('input', updateJsonViewer);

            // Toggle between edit and view modes
            editTab.addEventListener('click', function() {
                editMode.classList.remove('hidden');
                viewMode.classList.add('hidden');
                editTab.classList.add('text-blue-600', 'border-blue-600');
                editTab.classList.remove('text-gray-500');
                viewTab.classList.remove('text-blue-600', 'border-blue-600');
                viewTab.classList.add('text-gray-500', 'border-transparent');
            });

            viewTab.addEventListener('click', function() {
                updateJsonViewer(); // Update before showing
                editMode.classList.add('hidden');
                viewMode.classList.remove('hidden');
                viewTab.classList.add('text-blue-600', 'border-blue-600');
                viewTab.classList.remove('text-gray-500', 'border-transparent');
                editTab.classList.remove('text-blue-600', 'border-blue-600');
                editTab.classList.add('text-gray-500');
            });
        });
    </script>
</x-app-layout>