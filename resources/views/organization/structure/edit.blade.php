<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                จัดการโครงสร้างองค์กร: {{ $company->name }}
            </h2>
            <div>
                <a href="{{ route('organization.structure.show', $company->id) }}" class="text-sm bg-gray-500 hover:bg-gray-700 text-white py-1 px-3 rounded">
                    กลับ
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('organization.structure.update', $company->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">กำหนดโครงสร้างแผนก</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            แผนก
                                        </th>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            แผนกต้นสังกัด
                                        </th>
                                        <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                            ตำแหน่งในแผนก
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($company->departments as $department)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <div class="flex items-center">
                                                <div class="ml-4">
                                                    <div class="text-sm leading-5 font-medium text-gray-900">
                                                        {{ $department->name }}
                                                    </div>
                                                    <div class="text-sm leading-5 text-gray-500">
                                                        {{ $department->description }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <select name="parent_departments[{{ $department->id }}]" class="rounded border-gray-300 shadow-sm">
                                                <option value="">-- ไม่มีแผนกต้นสังกัด --</option>
                                                @foreach($company->departments as $parentDept)
                                                    @if($parentDept->id !== $department->id)
                                                        <option value="{{ $parentDept->id }}" {{ $department->parent_id == $parentDept->id ? 'selected' : '' }}>
                                                            {{ $parentDept->name }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-6 py-4 border-b border-gray-200">
                                            <select name="department_positions[{{ $department->id }}][]" multiple class="form-multiselect rounded border-gray-300 shadow-sm w-full">
                                                @foreach($positions->where('company_id', $company->id) as $position)
                                                    <option value="{{ $position->id }}" {{ $department->positions->contains($position->id) ? 'selected' : '' }}>
                                                        {{ $position->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                        บันทึกการเปลี่ยนแปลง
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
