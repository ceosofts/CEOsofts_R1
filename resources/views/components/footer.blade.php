<footer class="bg-white shadow-inner mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-4 text-center text-sm text-secondary-500 sm:flex sm:justify-between sm:items-center">
            <div>
                <span>&copy; {{ date('Y') }} {{ config('app.name') }}. สงวนลิขสิทธิ์.</span>
            </div>
            <div class="mt-2 sm:mt-0">
                <span>เวอร์ชั่น 1.0</span>
                @if(config('app.debug'))
                    <span class="mx-2">•</span>
                    <span>{{ config('app.env') }} mode</span>
                    <span class="mx-2">•</span>
                    <span>Laravel v{{ Illuminate\Foundation\Application::VERSION }}</span>
                    <span class="mx-2">•</span>
                    <span>PHP v{{ PHP_VERSION }}</span>
                @endif
            </div>
        </div>
    </div>
</footer>
