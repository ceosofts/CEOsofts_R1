@props(['company'])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-700 rounded-lg shadow overflow-hidden']) }}>
    <div class="p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0 mr-4">
                @if(isset($company->logo) && $company->logo)
                <img src="{{ Storage::url($company->logo) }}" alt="{{ $company->name }}" class="h-12 w-12 rounded-full">
                @else
                <div class="h-12 w-12 bg-gray-200 dark:bg-gray-600 flex items-center justify-center rounded-full">
                    <span class="text-gray-600 dark:text-gray-300 text-lg font-bold">{{ substr($company->name, 0, 1) }}</span>
                </div>
                @endif
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $company->name }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $company->code ?? 'ไม่ระบุรหัส' }}</p>
            </div>
        </div>

        <div class="mt-3 grid grid-cols-1 gap-2">
            @if($company->email)
            <div class="flex items-center text-sm">
                <svg class="h-4 w-4 text-gray-500 dark:text-gray-400 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>{{ $company->email }}</span>
            </div>
            @endif

            @if($company->phone)
            <div class="flex items-center text-sm">
                <svg class="h-4 w-4 text-gray-500 dark:text-gray-400 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                <span>{{ $company->phone }}</span>
            </div>
            @endif
        </div>

        <div class="mt-4 flex justify-between items-center">
            <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full 
                    {{ isset($company->status) && $company->status == 'active' || $company->is_active ? 
                        'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ isset($company->status) ? ($company->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน') : 
                    ($company->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน') }}
            </span>

            <div class="flex space-x-2">
                <a href="{{ route('companies.show', $company) }}" class="text-blue-500 hover:text-blue-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </a>
                <a href="{{ route('companies.edit', $company) }}" class="text-yellow-500 hover:text-yellow-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>