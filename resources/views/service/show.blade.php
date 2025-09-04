@extends('layouts.app')

@section('content')
<div class="header bg-primary pb-6">
  <div class="px-4">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Detail Service</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item"><a href="{{ url('home') }}"><i class="fas fa-home"></i></a></li>
              <li class="breadcrumb-item"><a href="{{ url('service') }}">Service</a></li>
              <li class="breadcrumb-item active" aria-current="page">Detail</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid mt--6">
  <div class="row">
    <div class="col">
      <div class="card shadow">
        <div class="card-header border-0">
          <h3 class="mb-0 text-dark">Detail Service</h3>
        </div>

        <div class="p-4 bg-light">
          @php
            $userLogin        = auth()->user();
            $pemohon          = optional($service->user);
            $pemohonDept      = $pemohon->department_id;
            $pemohonUnitId    = $pemohon->unit_id;

            // Hak approve (lintas unit tapi 1 departemen)
            $canApproveSpv     = $userLogin->jabatan_id == 3 && $userLogin->department_id == $pemohonDept;
            $canApproveManager  = $userLogin->jabatan_id == 4 && $userLogin->department_id == $pemohonDept;

            // Kepala unit hanya view
            $isKepalaUnit = $userLogin->jabatan_id == 2;
            $isUnitYangDipimpin = \App\Models\Unit::where('id', $pemohonUnitId)
                ->where('kepala_unit_id', $userLogin->id)
                ->exists();

            // Sudah ada approve?
            $sudahApprove = $service->keterangan_service->contains(function ($k) {
              return str_contains($k->keterangan, '[APPROVE]');
            });
          @endphp

          @if($isKepalaUnit && $isUnitYangDipimpin)
            <div class="alert alert-info">
              Anda adalah Kepala Unit dari <strong>{{ $pemohon->unit->nama_unit ?? 'Unit' }}</strong> dan dapat melihat permohonan ini. Namun, tidak memiliki akses untuk menyetujui.
            </div>
          @endif

          <div class="form-group">
            <label>Nama Pemohon</label>
            <input type="text" class="form-control" value="{{ $pemohon->nama ?? '-' }}" readonly>
          </div>

          <div class="form-group">
            <label>Jabatan</label>
            <input type="text" class="form-control" value="{{ $pemohon->jabatan->nama ?? '-' }}" readonly>
          </div>

          <div class="form-group">
            <label>Dari Departemen</label>
            <input type="text" class="form-control" value="{{ $pemohon->department->nama ?? '-' }}" readonly>
          </div>

          <div class="form-group">
            <label>Tanggal Permohonan</label>
            <input type="text" class="form-control" value="{{ optional($service->created_at)->format('D, d M Y H:i') }} WIB" readonly>
          </div>

          <div class="form-group">
            <label>Jenis Inventaris</label>
            <input type="text" class="form-control" value="{{ $service->inventaris->jenis_inventaris->jenis_inventaris ?? '-' }}" readonly>
          </div>

          <div class="form-group">
            <label>Inventaris</label>
            <input type="text" class="form-control" value="{{ $service->inventaris->nama ?? '-' }}" readonly>
          </div>

          <div class="form-group">
            <label>Service/Perbaikan</label>
            <textarea class="form-control bg-success bg-opacity-25" rows="4" readonly>{{ $service->service }}</textarea>
          </div>

          <div class="form-group">
            <label>Perkiraan Biaya</label>
            <input type="text" class="form-control" value="Rp {{ number_format($service->biaya_service ?? 0, 0, ',', '.') }}" readonly>
          </div>

          <div class="form-group">
            <label>Keterangan</label>
            <textarea class="form-control bg-success bg-opacity-25" rows="4" readonly>{{ $service->keterangan }}</textarea>
          </div>

          <div class="form-group">
            <label>Status</label><br>
            @php
              $statusText = config("status_label.{$service->status_id}", optional($service->status)->status ?? '-');
              $badgeClass = match($service->status_id) {
                3, 4 => 'badge-info',
                5 => 'badge-warning',
                6, 7 => 'badge-success',
                8 => 'badge-secondary',
                default => 'badge-danger'
              };
            @endphp
            <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
            @if(($service->type_permohonan ?? 0) == 1)
              <span class="badge badge-danger ml-1">URGENT</span>
            @endif
          </div>

          @if($service->teknisi_id)
            <div class="form-group">
              <label>Teknisi</label>
              <input type="text" class="form-control" value="{{ $service->teknisi->nama ?? '-' }}" readonly>
            </div>
          @endif

          @if($service->teknisi_umum_id)
            <div class="form-group">
              <label>Teknisi Umum</label>
              <input type="text" class="form-control" value="{{ $service->teknisi_umum->nama ?? '-' }}" readonly>
            </div>
          @endif

          @if($keterangan_service && $keterangan_service->count())
            <div class="mt-4">
              <h5>Keterangan Lanjutan:</h5>
              <table class="table table-bordered bg-white">
                <thead class="thead-light">
                  <tr>
                    <th>User</th>
                    <th>Keterangan</th>
                    <th>Tanggal</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($keterangan_service as $val)
                    <tr>
                      <td>{{ $val->user->nama ?? '-' }}</td>
                      <td>
                        @php $keterangan = $val->keterangan; @endphp

                        @if(Str::contains($keterangan, '[APPROVE MODAL]'))
                          ✅ Disetujui oleh {{ $val->user->nama ?? '-' }} - {{ Str::after($keterangan, '[APPROVE MODAL]') }}
                        @elseif(Str::contains($keterangan, '[APPROVE URGENT]'))
                          ✅ Disetujui sebagai Urgent - {{ Str::after($keterangan, '[APPROVE URGENT]') }}
                        @elseif(Str::contains($keterangan, '[APPROVE]'))
                          ✅ Disetujui oleh {{ trim(Str::after($keterangan, '[APPROVE]')) }}
                        @elseif(Str::contains($keterangan, '[REJECT]'))
                          ❌ Ditolak - {{ Str::after($keterangan, '[REJECT]') }}
                        @elseif(Str::contains($keterangan, 'Service dimulai'))
                          🛠️ {{ $keterangan }}
                        @elseif(Str::contains($keterangan, 'Service selesai'))
                          🏋️ {{ $keterangan }}
                        @else
                          {{ $keterangan }}
                        @endif
                      </td>
                      <td>{{ $val->created_at->format('D, d M Y H:i') }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>

        {{-- ===== Footer aksi ===== --}}
        <div class="card-footer d-flex justify-content-end">
          @unless(auth()->user()->hasRole('mutu'))

            {{-- Tombol approve via MODAL (bukan confirm) --}}
            @if(!$sudahApprove && $service->status_id == 3)
              @if($canApproveSpv)
                <button type="button"
                        class="btn btn-success btn-lg mr-2"
                        data-toggle="modal"        {{-- Jika BS5: data-bs-toggle --}}
                        data-target="#modalApprove{{ $service->id }}"> {{-- BS5: data-bs-target --}}
                  ✅ Approve SPV
                </button>
              @elseif($canApproveManager)
                <button type="button"
                        class="btn btn-success btn-lg mr-2"
                        data-toggle="modal"
                        data-target="#modalApprove{{ $service->id }}">
                  ✅ Approve Manager
                </button>
              @endif
            @endif

          @endunless

          <a href="{{ route('service.index') }}" class="btn btn-secondary btn-lg">🔙 Kembali</a>
        </div>

        {{-- ================== MODALS (DI DALAM SECTION) ================== --}}
        @include('service._modal_approve', ['service' => $service])
        @include('service._modal_tolak', ['service' => $service])
        {{-- =============================================================== --}}

      </div>
    </div>
  </div>
</div>
@endsection
