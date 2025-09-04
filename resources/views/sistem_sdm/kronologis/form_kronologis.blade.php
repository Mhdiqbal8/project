@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="text-success mb-3"><i class="fas fa-plus-circle"></i> Form Tambah Kronologis</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('kronologis.store', $bap->id) }}" method="POST">
        @csrf

        {{-- Tipe Kronologis --}}
        <div class="mb-3">
            <label for="tipe_kronologis" class="form-label fw-bold">
                <i class="fas fa-list"></i> Tipe Kronologis
            </label>
            <select name="tipe_kronologis" id="tipe_kronologis" class="form-control" required>
                <option value="">-- Pilih Tipe Kronologis --</option>
                <option value="Medis" {{ old('tipe_kronologis') == 'Medis' ? 'selected' : '' }}>Medis</option>
                <option value="Non-Medis" {{ old('tipe_kronologis') == 'Non-Medis' ? 'selected' : '' }}>Non-Medis</option>
            </select>
        </div>

        {{-- Judul --}}
        <div class="mb-3">
            <label for="judul" class="form-label fw-bold">📌 Judul Kronologis</label>
            <input type="text" class="form-control" id="judul" name="judul" value="{{ old('judul') }}" required>
        </div>

        {{-- Tanggal --}}
        <div class="mb-3">
            <label for="tanggal" class="form-label fw-bold">🗓️ Tanggal Kejadian</label>
            <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required>
        </div>

        {{-- Data Pasien (bungkus pakai div) --}}
        <div id="section-pasien">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nama_pasien" class="form-label fw-bold">👤 Nama Pasien</label>
                    <input type="text" class="form-control" id="nama_pasien" name="nama_pasien" value="{{ old('nama_pasien', $bap->nama_pasien ?? '') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="no_rm" class="form-label fw-bold">🆔 No. RM</label>
                    <input type="text" class="form-control" id="no_rm" name="no_rm" value="{{ old('no_rm', $bap->no_rm ?? '') }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="diagnosa" class="form-label fw-bold">📝 Diagnosa</label>
                    <input type="text" class="form-control" id="diagnosa" name="diagnosa" value="{{ old('diagnosa', $bap->diagnosa ?? '') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="ruangan" class="form-label fw-bold">🏥 Ruangan</label>
                    <input type="text" class="form-control" id="ruangan" name="ruangan" value="{{ old('ruangan', $bap->ruangan ?? '') }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="usia" class="form-label fw-bold">🎂 Usia</label>
                    <input type="number" class="form-control" id="usia" name="usia" value="{{ old('usia', $bap->usia ?? '') }}">
                </div>
            </div>
        </div>

        {{-- Deskripsi --}}
        <div class="mb-4">
            <label class="form-label fw-bold">📋 Deskripsi Kronologis</label>
            <input id="deskripsi" type="hidden" name="deskripsi" value="{{ old('deskripsi') }}">
            <trix-editor input="deskripsi" class="trix-content bg-white border rounded-3"></trix-editor>
        </div>

        {{-- Info TTD --}}
        <div class="alert alert-secondary">
            <strong>🖋️ Tanda Tangan (TTD):</strong><br>
            Akan otomatis muncul saat diverifikasi oleh:<br>
            ✅ Petugas (pengisi form)<br>
            ✅ Kepala Unit / Supervisor<br>
            ✅ Manager
        </div>

        {{-- Tombol --}}
        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save"></i> Simpan Kronologis</button>
    </form>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.js"></script>

<script>
    document.addEventListener("trix-change", function(event) {
        const trixEditor = document.querySelector("trix-editor");
        const html = trixEditor.editor.getHTML();
        document.getElementById("deskripsi").value = html;
    });

    document.addEventListener("DOMContentLoaded", function () {
        const tipeSelect = document.getElementById('tipe_kronologis');
        const pasienSection = document.getElementById('section-pasien');

        function togglePasien() {
            if (tipeSelect.value === 'Medis') {
                pasienSection.style.display = '';
                // Set required attributes
                document.getElementById('nama_pasien').required = true;
                document.getElementById('no_rm').required = true;
                document.getElementById('diagnosa').required = true;
                document.getElementById('ruangan').required = true;
                document.getElementById('usia').required = true;
            } else {
                pasienSection.style.display = 'none';
                // Remove required attributes
                document.getElementById('nama_pasien').required = false;
                document.getElementById('no_rm').required = false;
                document.getElementById('diagnosa').required = false;
                document.getElementById('ruangan').required = false;
                document.getElementById('usia').required = false;
            }
        }

        tipeSelect.addEventListener('change', togglePasien);
        togglePasien();
    });
</script>
@endpush
