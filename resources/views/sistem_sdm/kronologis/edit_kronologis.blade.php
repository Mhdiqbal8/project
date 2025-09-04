@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="text-primary mb-3">
        <i class="fas fa-edit me-1"></i> Edit Form Kronologis
    </h3>

    {{-- alert error --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="kronologisForm" action="{{ route('kronologis.update', $form->id) }}" method="POST">
        @csrf
        @method('PATCH')

        {{-- Tipe Kronologis --}}
        <div class="mb-3">
            <label for="tipe_kronologis" class="form-label fw-bold">
                <i class="fas fa-list"></i> Tipe Kronologis
            </label>
            <select name="tipe_kronologis" id="tipe_kronologis" class="form-control" required>
                <option value="">-- Pilih Tipe Kronologis --</option>
                <option value="Medis" {{ old('tipe_kronologis', $form->tipe_kronologis) == 'Medis' ? 'selected' : '' }}>Medis</option>
                <option value="Non-Medis" {{ old('tipe_kronologis', $form->tipe_kronologis) == 'Non-Medis' ? 'selected' : '' }}>Non-Medis</option>
            </select>
        </div>

        {{-- Judul Kronologis --}}
        <div class="mb-3">
            <label for="judul" class="form-label fw-bold">📌 Judul Kronologis</label>
            <input type="text" class="form-control" id="judul" name="judul"
                   value="{{ old('judul', $form->judul) }}" required>
        </div>

        {{-- Tanggal --}}
        <div class="mb-3">
            <label for="tanggal" class="form-label fw-bold">🗓️ Tanggal Kejadian</label>
            <input type="date" class="form-control" id="tanggal" name="tanggal"
                   value="{{ old('tanggal', $form->tanggal) }}" required>
        </div>

        {{-- Data Pasien (bungkus pakai div) --}}
        <div id="section-pasien">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">👤 Nama Pasien</label>
                    <input type="text" class="form-control" name="nama_pasien"
                           id="nama_pasien"
                           value="{{ old('nama_pasien', $form->nama_pasien) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">🆔 No. RM</label>
                    <input type="text" class="form-control" name="no_rm"
                           id="no_rm"
                           value="{{ old('no_rm', $form->no_rm) }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">📝 Diagnosa</label>
                    <input type="text" class="form-control" name="diagnosa"
                           id="diagnosa"
                           value="{{ old('diagnosa', $form->diagnosa) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">🏥 Ruangan</label>
                    <input type="text" class="form-control" name="ruangan"
                           id="ruangan"
                           value="{{ old('ruangan', $form->ruangan) }}">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-bold">🎂 Usia (tahun)</label>
                    <input type="number" class="form-control" name="usia"
                           id="usia"
                           value="{{ old('usia', $form->usia) }}">
                </div>
            </div>
        </div>

        {{-- Deskripsi --}}
        <div class="mb-4">
            <label class="form-label fw-bold">📋 Deskripsi Kronologis</label>
            <input id="deskripsi" type="hidden" name="deskripsi"
                   value="{{ old('deskripsi', $form->deskripsi) }}">
            <trix-editor input="deskripsi"
                         class="trix-content bg-white border rounded-3"></trix-editor>
        </div>

        {{-- Info TTD --}}
        <div class="alert alert-secondary">
            <strong>🖋️ Tanda Tangan (TTD):</strong><br>
            Akan otomatis muncul saat diverifikasi oleh petugas, SPV/Manager, dan unit terkait.
        </div>

        <button type="submit" class="btn btn-primary w-100">
            <i class="fas fa-save me-1"></i> Update Form Kronologis
        </button>
    </form>
</div>
@endsection

@push('scripts')
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.js"></script>

<script>
    document.addEventListener('trix-change', function (e) {
        document.querySelector('#deskripsi').value = e.target.editor.getHTML();
    });

    document.addEventListener("DOMContentLoaded", function () {
        const tipeSelect = document.getElementById('tipe_kronologis');
        const pasienSection = document.getElementById('section-pasien');

        function togglePasien() {
            if (tipeSelect.value === 'Medis') {
                pasienSection.style.display = '';
                document.getElementById('nama_pasien').required = true;
                document.getElementById('no_rm').required = true;
                document.getElementById('diagnosa').required = true;
                document.getElementById('ruangan').required = true;
                document.getElementById('usia').required = true;
            } else {
                pasienSection.style.display = 'none';
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
