@extends('layouts.app')

@section('content')
@php
    // Deteksi Mutu (selaras dengan logic di AuthServiceProvider)
    $authUser = auth()->user();
    $isMutu = function_exists('isUserMutu')
        ? isUserMutu()
        : (
            (method_exists($authUser, 'hasRole') && $authUser->hasRole('mutu')) ||
            (method_exists($authUser, 'hasAccess') && (
                $authUser->hasAccess('acc_mutu_bap') ||
                $authUser->hasAccess('approve_mutu') ||
                $authUser->hasAccess('mutu') ||
                $authUser->hasAccess('mutu_read')
            ))
        );
@endphp

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-success mb-0">
            <i class="fas fa-file-medical-alt me-2"></i> Detail Kronologis Pasien
        </h4>

        <div class="d-flex gap-2">
            {{-- Tombol "Sudah Dibaca Mutu" dipindahkan ke sini (hanya Mutu & jika belum ditandai) --}}
            @if ($isMutu)
                @if (empty($form->mutu_checked_at))
                    <form action="{{ route('kronologis.mutuCheck', $form->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary rounded-pill me-2">
                            <i class="fas fa-check me-1"></i> Sudah Dibaca Mutu
                        </button>
                    </form>
                @else
                    <span class="badge bg-success align-self-center me-2">
                        Sudah dibaca Mutu {{ \Carbon\Carbon::parse($form->mutu_checked_at)->format('d-m-Y H:i') }}
                    </span>
                @endif
            @endif

            <a href="{{ route('kronologis.cetak', $form->id) }}" target="_blank" class="btn btn-outline-primary rounded-pill">
                <i class="fas fa-download me-2"></i> Download PDF
            </a>
        </div>
    </div>

    <div class="card shadow-lg p-5 rounded-4 border-0 bg-white">

        {{-- Judul Kronologis --}}
        <div class="mb-4">
            <label class="fw-semibold text-dark">📝 Judul Kronologis:</label>
            <div class="fs-5 text-dark">
                {{ $form->judul ?? '-' }}
            </div>
        </div>

        {{-- Deskripsi --}}
        <div class="mb-4">
            <label class="fw-semibold text-dark">📋 Deskripsi:</label>
            <div class="border rounded bg-light p-3 text-dark" style="min-height: 80px;">
                {!! $form->deskripsi ?? '<em>(Belum ada deskripsi)</em>' !!}
            </div>
        </div>

        {{-- Informasi Pasien --}}
        <div class="mb-4">
            <label class="fw-semibold text-dark">👤 Informasi Pasien:</label>
            <ul class="list-unstyled ms-2 text-dark">
                <li><strong>👨‍⚕️ Nama:</strong> {{ $form->nama_pasien ?? '-' }}</li>
                <li><strong>📄 No. RM:</strong> {{ $form->no_rm ?? '-' }}</li>
                <li><strong>📝 Diagnosa:</strong> {{ $form->diagnosa ?? '-' }}</li>
                <li><strong>🏥 Ruangan:</strong> {{ $form->ruangan ?? '-' }}</li>
                <li><strong>🎂 Usia:</strong> {{ $form->usia ?? '-' }}</li>
                <li><strong>📅 Tanggal Kejadian:</strong> {{ \Carbon\Carbon::parse($form->tanggal)->format('d-m-Y') }}</li>

                {{-- Status (tetap dimatikan seperti file asli) --}}
                {{--
                <li><strong>📌 Status:</strong>
                    <span class="badge rounded-pill {{ $form->status == 'Selesai' ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ $form->status }}
                    </span>
                </li>
                --}}
            </ul>
        </div>

        {{-- Tombol Kembali --}}
        <div class="mt-4">
            <a href="{{ route('bap.detail', $form->bapForm->id) }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Form BAP
            </a>
        </div>
    </div>
</div>
@endsection
