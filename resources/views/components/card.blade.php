<div {{ $attributes->merge(['class' => 'card']) }}>
    @isset($header)
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            {{ $header }}
        </div>
    @endisset

    <div class="p-6">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $footer }}
        </div>
    @endisset
</div>
