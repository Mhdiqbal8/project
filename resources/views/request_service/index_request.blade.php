@extends('layouts.app')

@section('content')
<!-- ===== Header ===== -->
<div class="header bg-primary pb-6">
  <div class="px-4">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Permohonan Service</h6>
        </div>
        <div class="col-lg-6 col-5 text-right">
          <a href="{{ route('request_service.export') }}" class="btn btn-success btn-sm">
            Export Excel
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ===== Content ===== -->
<div class="container-fluid mt--6">
  <div class="card shadow">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>No</th>
              <th>No Tiket</th>
              <th>Nama Pemohon</th>
              <th>Department</th>
              <th>Unit Asal</th>
              <th>Unit Tujuan</th>
              <th>Tgl Permohonan</th>
              <th>Status</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($service_requests as $sr)
              @php
                // PRIORITAS status dari request_services
                $statusId   = $sr->status_id ?? optional($sr->service)->status_id;
                $statusText = $sr->status_name ?? '-';

                $badge = match((int) $statusId) {
                  6 => 'info',       // Disetujui SPV/Manager (masuk antrean unit tujuan)
                  7 => 'primary',    // Sedang dikerjakan
                  8 => 'success',    // Closed
                  9 => 'secondary',  // Selesai & dilaporkan
                  10 => 'danger',    // Ditolak
                  default => 'light'
                };

                $user       = auth()->user();
                $unitTujuan = optional($sr->service)->unit_tujuan_id;
                $deptTujuan = \App\Models\User::where('unit_id', $unitTujuan)->value('department_id');

                // rule: boleh aksi kalau (unit user = unit tujuan) ATAU (SPV/Manager & departemen sama)
                $canApprove = in_array((int)$statusId, [6,7]) && (
                                $user->unit_id == $unitTujuan ||
                                (in_array($user->jabatan_id, [3,4]) && $deptTujuan && $user->department_id == $deptTujuan)
                              );

                // === URGENT FLAG ===
                // Ambil dari relasi service (utama). Kalau tabel request_services juga punya kolom sama, pakai fallback.
                $urgentFromService  = (int) (optional($sr->service)->type_permohonan ?? 0) === 1;
                $urgentFromRequest  = property_exists($sr, 'type_permohonan') ? ((int)$sr->type_permohonan === 1) : false;
                $isUrgent = $urgentFromService || $urgentFromRequest;
              @endphp

              <tr @if($isUrgent)
                    style="color:#dc3545;font-weight:600;background:rgba(220,53,69,.06);"
                  @endif>
                <td>{{ $loop->iteration }}</td>
                <td>{{ optional($sr->service)->no_tiket ?? '-' }}</td>
                <td>{{ optional(optional($sr->service)->user)->nama ?? '-' }}</td>
                <td>{{ optional(optional(optional($sr->service)->user)->department)->nama ?? '-' }}</td>
                <td>{{ optional(optional(optional($sr->service)->user)->unit)->nama_unit ?? '-' }}</td>
                <td>{{ optional(optional($sr->service)->unitTujuan)->nama_unit ?? '-' }}</td>
                <td>{{ optional($sr->created_at)->format('d-M-Y H:i') }} WIB</td>

                <td>
                  <span class="badge badge-{{ $badge }} text-white">{{ $statusText }}</span>
                  @if($isUrgent)
                    <span class="badge badge-danger ml-1">URGENT</span>
                  @endif
                </td>

                <td class="text-nowrap text-center">
                  <a href="{{ route('request_service.show', $sr->id) }}" class="btn btn-info btn-sm" title="Detail">
                    <i class="fas fa-eye"></i>
                  </a>

                  @if($canApprove)
                    @if((int)$statusId === 6)
                      {{-- Mulai dikerjakan --}}
                      <button type="button" class="btn btn-warning btn-sm"
                              data-toggle="modal" data-target="#modalProgress{{ $sr->id }}"
                              title="Mulai Dikerjakan">
                        <i class="fas fa-play"></i>
                      </button>
                      {{-- Tolak --}}
                      <button type="button" class="btn btn-danger btn-sm"
                              data-toggle="modal" data-target="#modalTolak{{ $sr->id }}"
                              title="Tolak">
                        <i class="fas fa-times"></i>
                      </button>

                    @elseif((int)$statusId === 7)
                      {{-- Sedang dikerjakan --}}
                      <button type="button" class="btn btn-success btn-sm"
                              data-toggle="modal" data-target="#modalSelesai{{ $sr->id }}"
                              title="Selesai">
                        <i class="fas fa-check"></i>
                      </button>
                    @endif
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center">Tidak ada data permohonan service</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- ===== Modal Aksi per Baris ===== --}}
@foreach($service_requests as $sr)
  @include('request_service.partials.modal_action', ['sr' => $sr])
@endforeach
@endsection
