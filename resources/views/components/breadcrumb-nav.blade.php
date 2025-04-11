<div>
    <!-- It is never too late to be what you might have been. - George Eliot -->
</div>

<nav class="flex mb-5" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-2">
        @foreach($breadcrumbs as $label => $url)
            <li class="inline-flex items-center">
                @if($loop->first && $showHomeIcon)
                    <a href="{{ $url }}" class="inline-flex items-center text-sm font-medium text-secondary-700 hover:text-primary-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        {{ $label }}
                    </a>
                @else
                    @if($loop->last)
                        <span class="text-sm font-medium text-secondary-500">{{ $label }}</span>
                    @else
                        <a href="{{ $url }}" class="inline-flex items-center text-sm font-medium text-secondary-700 hover:text-primary-600">
                            {{ $label }}
                        </a>
                    @endif
                @endif
            </li>
            
            @if(!$loop->last)
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-secondary-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </li>
            @endif
        @endforeach
    </ol>
</nav>