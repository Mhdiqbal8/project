@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h3 class="fw-bold mb-4 text-success">
        <i class="fas fa-calendar-check me-2"></i> Laporan Harian Kerja
    </h3>

    {{-- ALERT ERROR --}}
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success shadow-sm">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('laporan-kerja.store') }}" method="POST">
        @csrf

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">

                {{-- BARIS 1 --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control shadow-sm" value="{{ old('tanggal') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Shift</label>
                        <select name="shift" id="shift" class="form-select shadow-sm" required>
                            <option value="">-- Pilih Shift --</option>
                            <option value="Pagi" {{ old('shift') == 'Pagi' ? 'selected' : '' }}>Pagi</option>
                            <option value="Middle" {{ old('shift') == 'Middle' ? 'selected' : '' }}>Middle</option>
                            <option value="Sore" {{ old('shift') == 'Sore' ? 'selected' : '' }}>Sore</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <span class="text-muted small">Format waktu 24 jam, misal: 08:00, 15:00, 23:59</span>
                    </div>
                </div>

                {{-- BARIS 2 --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Jam In (Mulai)</label>
                        <input type="time" name="jam_in_mulai" class="form-control shadow-sm" step="60" value="{{ old('jam_in_mulai') }}" placeholder="08:00">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Jam In (Selesai)</label>
                        <input type="time" name="jam_in_selesai" class="form-control shadow-sm" step="60" value="{{ old('jam_in_selesai') }}" placeholder="12:00">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Jam Out (Mulai)</label>
                        <input type="time" name="jam_out_mulai" class="form-control shadow-sm" step="60" value="{{ old('jam_out_mulai') }}" placeholder="13:00">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Jam Out (Selesai)</label>
                        <input type="time" name="jam_out_selesai" class="form-control shadow-sm" step="60" value="{{ old('jam_out_selesai') }}" placeholder="23:59">
                    </div>
                </div>

                {{-- BARIS 3 --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Kegiatan di Meja / Spesialis</label>
                        <textarea name="kegiatan_in" rows="4" class="form-control shadow-sm"
                            placeholder="- Perbaikan Teramedik&#10;- Design Foto / Editing Video&#10;- Perbaikan EForm Service">{{ old('kegiatan_in') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Kegiatan di Lapangan / Luar Kantor</label>
                        <textarea name="kegiatan_out" rows="4" class="form-control shadow-sm"
                            placeholder="- Aktivasi Windows / Microsoft Office&#10;- Pengecekan Hardware&#10;- Pemasangan Hardware">{{ old('kegiatan_out') }}</textarea>
                    </div>
                </div>

                {{-- BARIS 4 --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Nama Petugas</label>
                        <input type="text" class="form-control bg-light shadow-sm" readonly value="{{ auth()->user()->nama }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Jabatan</label>
                        <input type="text" class="form-control bg-light shadow-sm" readonly value="{{ optional(auth()->user()->jabatan)->nama }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tanda Tangan</label>
                        <div class="border rounded p-3 bg-light text-center shadow-sm" style="min-height: 150px;">
                            @if (auth()->user()->ttd_path)
                                <img src="{{ asset('storage/' . auth()->user()->ttd_path) }}" height="80" class="mb-2">
                                <br>
                                <a href="{{ route('user.hapus_ttd', auth()->user()->id) }}" class="btn btn-sm btn-danger">
                                    Hapus TTD
                                </a>
                            @else
                                <em class="text-muted">Belum ada tanda tangan.</em>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- BARIS 5 --}}
                <div class="mb-4">
                    <label class="form-label fw-bold">Catatan / Komentar Staff (Opsional)</label>
                    <textarea name="komentar_staff" rows="3" class="form-control shadow-sm"
                        placeholder="Tulis catatan jika perlu...">{{ old('komentar_staff') }}</textarea>
                </div>

                {{-- Tombol Simpan --}}
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-success px-4 py-2 shadow-sm">
                        <i class="fas fa-save me-2"></i> Simpan Laporan
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>

{{-- JS Placeholder Otomatis Berdasarkan Shift --}}
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const shift = document.getElementById('shift');
        const placeholders = {
            Pagi: ['08:00', '12:00', '13:00', '15:00'],
            Middle: ['12:00', '15:00', '15:30', '18:00'],
            Sore: ['15:00', '18:00', '18:30', '23:59'],
        };

        shift.addEventListener('change', function () {
            const [inMulai, inSelesai, outMulai, outSelesai] = placeholders[this.value] || ['','','',''];
            document.querySelector('input[name="jam_in_mulai"]').placeholder = inMulai;
            document.querySelector('input[name="jam_in_selesai"]').placeholder = inSelesai;
            document.querySelector('input[name="jam_out_mulai"]').placeholder = outMulai;
            document.querySelector('input[name="jam_out_selesai"]').placeholder = outSelesai;
        });
    });
</script>
@endpush

{{-- CSS Tambahan --}}
<style>
    label.form-label {
        font-size: 0.95rem;
        color: #212529;
    }

    .form-control, .form-select, textarea {
        border-radius: .4rem;
        color: #212529;
    }

    .form-control::placeholder,
    textarea::placeholder {
        color: #6c757d;
        opacity: 0.8;
    }

    .form-control:focus,
    .form-select:focus,
    textarea:focus {
        border-color: #198754;
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, .25);
    }
</style>
@endsection
