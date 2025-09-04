@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="text-success mb-4">
        <i class="fas fa-user-shield"></i> Edit Privilege: {{ $user->nama }}
    </h3>

    {{-- ALERT FLASH MESSAGE --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('failed'))
        <div class="alert alert-danger">{{ session('failed') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('privileges.update', $user->id) }}" method="POST">
                @csrf
        @method('PUT')  
                <div class="mb-3">
                    <label class="form-label"><strong>Nama:</strong></label>
                    <p class="form-control-plaintext">{{ $user->nama }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Email:</strong></label>
                    <p class="form-control-plaintext">{{ $user->email }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Akses:</strong></label>
                    <div class="row">
                        @foreach ($allAkses as $akses)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input 
                                        type="checkbox" 
                                        name="akses[]" 
                                        value="{{ $akses->id }}"
                                        class="form-check-input"
                                        id="akses{{ $akses->id }}"
                                        {{ $user->akses->contains('id', $akses->id) ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="akses{{ $akses->id }}">
                                        {{ $akses->nama_akses }} <br>
                                        <small class="text-muted">{{ $akses->deskripsi }}</small>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn btn-success mt-3">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>

    <a href="{{ route('privileges.index') }}" class="btn btn-secondary mt-3">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>
@endsection
