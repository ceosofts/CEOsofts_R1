@if ($errors->any() || session('error_message'))
    <div class="alert alert-danger mt-3">
        @if(session('error_message'))
            <p class="mb-0"><strong>{{ session('error_message') }}</strong></p>
        @endif
        
        @if ($errors->any())
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
    </div>
@endif
