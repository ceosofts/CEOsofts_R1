<div x-data="{ open: false }" class="relative inline-block text-left">
    <button @click="open = !open" type="button" class="inline-flex justify-center items-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-primary-500">
        <span class="mr-2">
            @if ($currentCompany)
                {{ $currentCompany->name }}
            @else
                เลือกบริษัท
            @endif
        </span>
        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>

    <div x-show="open" @click.outside="open = false" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
        <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="company-menu">
            @forelse ($companies as $company)
                <button wire:click="selectCompany({{ $company->id }})" @click="open = false" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 {{ $currentCompany && $currentCompany->id === $company->id ? 'bg-gray-100 text-gray-900' : '' }}" role="menuitem">
                    {{ $company->name }}
                </button>
            @empty
                <span class="block px-4 py-2 text-sm text-gray-500">
                    ไม่พบบริษัท
                </span>
            @endforelse
            
            @can('create', App\Models\Company::class)
                <div class="border-t border-gray-100 mt-1 pt-1">
                    <a href="{{ route('company.create') }}" class="block px-4 py-2 text-sm text-indigo-600 hover:bg-gray-100 hover:text-indigo-700">
                        <svg class="inline-block w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        เพิ่มบริษัทใหม่
                    </a>
                </div>
            @endcan
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.addEventListener('company-changed', () => {
        // Reload the page when company changes
        window.location.reload();
    });
</script>
@endpush
