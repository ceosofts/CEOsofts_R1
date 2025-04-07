@props([
    'title' => null,
    'subtitle' => null,
    'footer' => null,
    'padding' => 'p-6',
    'headerPadding' => 'px-6 py-4',
    'footerPadding' => 'px-6 py-4',
    'bodyPadding' => 'p-6',
    'rounded' => 'rounded-lg',
    'shadow' => 'shadow-md',
])

<div {{ $attributes->merge(['class' => "bg-white dark:bg-gray-800 {$rounded} {$shadow} border border-gray-200 dark:border-gray-700 overflow-hidden"]) }}>
    @if ($title || $subtitle)
        <div class="border-b border-gray-200 dark:border-gray-700 {{ $headerPadding }}">
            @if ($title)
                <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-gray-100">
                    {{ $title }}
                </h3>
            @endif
            
            @if ($subtitle)
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $subtitle }}
                </p>
            @endif
        </div>
    @endif
    
    <div class="{{ $bodyPadding }}">
        {{ $slot }}
    </div>
    
    @if ($footer)
        <div class="border-t border-gray-200 dark:border-gray-700 {{ $footerPadding }}">
            {{ $footer }}
        </div>
    @endif
</div>
