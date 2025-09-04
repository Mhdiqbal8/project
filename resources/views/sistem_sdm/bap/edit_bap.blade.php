@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3 class="text-primary mb-3"><i class="fas fa-edit"></i> Edit Form BAP</h3>

    <form action="{{ route('bap.update', $form->id) }}" method="POST">
        @csrf
        @method('PATCH')

        <!-- Pilih Divisi Verifikasi -->
<div class="mb-3">
    <label for="divisi_verifikasi" class="form-label fw-bold">Divisi yang Akan Memverifikasi *</label>
    <select name="divisi_verifikasi" id="divisi_verifikasi" class="form-select" required>
        <option value="">-- Pilih Divisi --</option>
       <option value="IT" {{ $form->divisi_verifikasi == 'IT' ? 'selected' : '' }}>IT</option>
        <option value="Maintenance" {{ $form->divisi_verifikasi == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
        <option value="Lainnya" {{ $form->divisi_verifikasi == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
    </select>
</div>


        <!-- Bagian 1: Perbaikan Sistem -->
        <div class="mb-3">
            <label class="fw-bold mb-2">1️⃣ Perbaikan Sistem *</label>
            @php
                $perbaikan = json_decode($form->perbaikan, true) ?? [];
            @endphp
            @foreach (['Dokumentasi/penulisan SOAP', 'Laporan Operasi', 'Input Obat Pasien', 'Tidak Menulis SOAP', 'Input Penunjang (Laboratorium/Radiologi)'] as $item)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="perbaikan[]" value="{{ $item }}" id="{{ Str::slug($item) }}"
                        {{ in_array($item, $perbaikan) ? 'checked' : '' }}>
                    <label class="form-check-label" for="{{ Str::slug($item) }}">{{ $item }}</label>
                </div>
            @endforeach
            <div class="mb-2 mt-2">
                <label for="tindakan_medis" class="form-label">Tindakan Medis (jika ada)</label>
                <input type="text" class="form-control" name="tindakan_medis" id="tindakan_medis" value="{{ $form->tindakan_medis }}">
            </div>
            <div class="mb-2">
                <label for="lain_lain" class="form-label">Lain-lain</label>
                <input type="text" class="form-control" name="lain_lain" id="lain_lain" value="{{ $form->lain_lain }}">
            </div>
        </div>

        <!-- Bagian 2: Permasalahan Lain -->
        <div class="mb-3">
            <label for="permasalahan_lain" class="fw-bold mb-2">2️⃣ Permasalahan Lain ***</label>
            <textarea name="permasalahan_lain" id="permasalahan_lain" rows="3" class="form-control">{{ $form->permasalahan_lain }}</textarea>
        </div>

        <!-- Bagian 3: Penyelesaian -->
        <div class="mb-3">
            <label class="fw-bold mb-2">3️⃣ Penyelesaian</label>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label for="kendala" class="form-label">Kendala</label>
                    <textarea name="kendala" id="kendala" rows="3" class="form-control" {{ auth()->user()->role != 'verifikator' ? 'readonly' : '' }}>{{ $form->kendala }}</textarea>
                </div>
                <div class="col-md-6 mb-2">
                    <label class="form-label">Petugas Verifikasi</label>
                    <input type="text" class="form-control" value="{{ $form->petugas_verifikasi ?? 'Akan otomatis saat diverifikasi Unit Terkait' }}" {{ auth()->user()->role != 'verifikator' ? 'readonly' : '' }} name="petugas_verifikasi">
                </div>
            </div>
        </div>

        <!-- Keterangan -->
        <div class="alert alert-secondary">
            <strong>Keterangan:</strong><br>
            * Beri tanda ✔️ untuk perbaikan yang diinginkan.<br>
            ** Coret salah satu jika ada pilihan.<br>
            *** Diisi jika ada masalah lain.<br>
            **** Penyelesaian hanya diisi oleh pihak Verifikator.
        </div>

        <!-- Tombol Submit -->
        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save"></i> Update Form BAP</button>
    </form>
</div>
@endsection
