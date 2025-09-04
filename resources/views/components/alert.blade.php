@if (session('success'))
    <div class="alert alert-success mt-1">
        {{ session('success') }}
    </div>
@endif

@if (session('failed'))
    <div class="alert alert-danger mt-1">
        {{ session('failed') }}
    </div>
@endif
