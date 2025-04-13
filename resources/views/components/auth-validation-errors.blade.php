@props(['errors'])

@if ($errors->any())
<div {{ $attributes->merge(['class' => 'bg-red-50 p-4 rounded-md border border-red-200 mb-4']) }}>
    <div class="font-medium text-red-600">
        {{ __('เกิดข้อผิดพลาด! กรุณาตรวจสอบข้อมูลที่กรอก.') }}
    </div>

    <ul class="mt-3 list-disc list-inside text-sm text-red-600">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif