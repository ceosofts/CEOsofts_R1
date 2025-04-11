<nav class="flex" aria-label="Breadcrumb">
  <ol role="list" class="flex items-center space-x-1.5 md:space-x-3">
    <li>
      <div class="flex items-center">
        <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">
          <svg class="h-4 w-4 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 011.414 0l7 7A1 1 0 0117 11h-1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3a1 1 0 00-1-1H9a1 1 0 00-1 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-6H3a1 1 0 01-.707-1.707l7-7z" clip-rule="evenodd" />
          </svg>
        </a>
      </div>
    </li>

    @foreach($items as $index => $item)
      <li>
        <div class="flex items-center">
          @if(!$loop->first)
            <svg class="h-5 w-5 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
            </svg>
          @endif
          @if($item['url'] && !$loop->last)
            <a href="{{ $item['url'] }}" class="ml-1 md:ml-2 text-sm font-medium text-gray-500 hover:text-gray-700">{{ $item['label'] }}</a>
          @else
            <span class="ml-1 md:ml-2 text-sm font-medium text-gray-900">{{ $item['label'] }}</span>
          @endif
        </div>
      </li>
    @endforeach
  </ol>
</nav>
