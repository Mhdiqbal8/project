@extends('layouts.app')

@section('content')
<!-- Main content -->
<div class="header bg-primary pb-6">
  <div class="px-4">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Detail Request Service</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item"><a href="{{ url('home') }}"><i class="fas fa-home"></i></a></li>
              <li class="breadcrumb-item"><a href="{{ route('request_service.index') }}">Request Service</a></li>
              <li class="breadcrumb-item active" aria-current="page">Detail</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Page content -->
<div class="container-fluid mt--6">
  <div class="row">
    <div class="col">
      <div class="card shadow">

        {{-- Flash & validation --}}
        @if(session('success'))
          <div class="alert alert-success m-4 mb-0">{{ session('success') }}</div>
        @endif
        @if($errors->any())
          <div class="alert alert-danger m-4 mb-0">{{ $errors->first() }}</div>
        @endif

        <div class="card-header border-0">
          <h3 class="mb-0 text-dark">Detail Request Service</h3>
        </div>

        @if($request->type_permohonan == 1)
          <div class="text-center text-danger font-weight-bold mb-3">
            <i class="fas fa-exclamation-triangle"></i> URGENT
          </div>
        @endif

        <div class="p-4 bg-light">
          @php $userPemohon = optional($request->service->user); @endphp

          <div class="row mb-3">
            <div class="col-md-3 font-weight-bold">Nama Pemohon</div>
            <div class="col-md-9 bg-white p-2 rounded">{{ $userPemohon->nama ?? '-' }}</div>
          </div>

          <div class="row mb-3">
            <div class="col-md-3 font-weight-bold">Jabatan</div>
            <div class="col-md-9 bg-white p-2 rounded">{{ $userPemohon->jabatan->nama ?? '-' }}</div>
          </div>

          <div class="row mb-3">
            <div class="col-md-3 font-weight-bold">Dari Unit</div>
            <div class="col-md-9 bg-white p-2 rounded">{{ $userPemohon->unit->nama_unit ?? '-' }}</div>
          </div>

          <div class="row mb-3">
            <div class="col-md-3 font-weight-bold">Dari Departemen</div>
            <div class="col-md-9 bg-white p-2 rounded">{{ $userPemohon->department->nama ?? '-' }}</div>
          </div>

          <div class="row mb-3">
            <div class="col-md-3 font-weight-bold">Tanggal Permohonan</div>
            <div class="col-md-9 bg-white p-2 rounded">{{ optional($request->created_at)->format('D, d M Y H:i') ?? '-' }} WIB</div>
          </div>

          <div class="row mb-3">
            <div class="col-md-3 font-weight-bold">Jenis Inventaris</div>
            <div class="col-md-9 bg-white p-2 rounded">
              {{ optional(optional($request->service)->inventaris)->jenis_inventaris->jenis_inventaris ?? '-' }}
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-3 font-weight-bold">Inventaris</div>
            <div class="col-md-9 bg-white p-2 rounded">
              {{ optional($request->service->inventaris)->nama ?? '-' }}
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-3 font-weight-bold">Service/Perbaikan</div>
            <div class="col-md-9 bg-white p-2 rounded">
              {{ $request->service->service ?? '-' }}
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-3 font-weight-bold">Keterangan</div>
            <div class="col-md-9 bg-white p-2 rounded">
              {{ $request->service->keterangan ?? '-' }}
            </div>
          </div>

          @php
            // PRIORITAS status dari request_services (fallback ke service)
            $statusId   = $request->status_id ?? optional($request->service)->status_id;
            $statusText = config("status_label.{$statusId}") 
                          ?? optional($request->service->status)->status 
                          ?? '-';
            $color = match($statusId) {
              6 => 'info',
              7 => 'primary',
              8 => 'success',
              9 => 'secondary',
              10 => 'danger',
              default => 'light'
            };
          @endphp

          <div class="row mb-3">
            <div class="col-md-3 font-weight-bold">Status</div>
            <div class="col-md-9">
              <span class="badge badge-{{ $color }} p-2">{{ $statusText }}</span>
            </div>
          </div>

          @if($request->teknisi_id || $request->teknisi_umum_id)
            <div class="row mb-3">
              <div class="col-md-3 font-weight-bold">Teknisi</div>
              <div class="col-md-9 bg-white p-2 rounded">
                {{ $request->teknisi->nama ?? ($teknisi_umum->nama ?? '-') }}
              </div>
            </div>
          @endif

          @if(optional($request->service)->tgl_teknisi)
            <div class="row mb-3">
              <div class="col-md-3 font-weight-bold">Tanggal Dikerjakan</div>
              <div class="col-md-9 bg-white p-2 rounded">
                {{ \Carbon\Carbon::parse($request->service->tgl_teknisi)->format('D, d M Y H:i') }} WIB
              </div>
            </div>
          @endif

          @if($keterangan_service && $keterangan_service->count())
            <div class="mt-4">
              <h5>Keterangan Lanjutan:</h5>
              <table class="table table-bordered bg-white">
                <thead class="thead-light">
                  <tr>
                    <th>Aktivitas</th>
                    <th>User</th>
                    <th>Keterangan</th>
                    <th>Tanggal</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($keterangan_service as $val)
                    @php
                      $raw = $val->keterangan;
                      $role = null;
                      $keterangan = $raw;

                      if (Str::contains($raw, '[CREATE]')) {
                        $role = '📝 Dibuat';
                        $keterangan = str_replace('[CREATE]', 'Permohonan dibuat:', $raw);
                      } elseif (Str::contains($raw, 'APPROVE MODAL')) {
                        $role = '✅ Disetujui Unit';
                        $keterangan = 'Permohonan disetujui oleh unit terkait';
                      } elseif (Str::contains(Str::lower($raw), 'dimulai')) {
                        $role = '🛠️ Dikerjakan';
                        $keterangan = preg_replace('/^\[progress\]\s*/i', '', $raw);
                      } elseif (Str::contains(Str::lower($raw), 'selesai')) {
                        $role = '✔️ Selesai';
                        $keterangan = preg_replace('/^service\s*selesai:\s*/i', '', $raw);
                      } elseif (Str::contains($raw, 'REJECT')) {
                        $role = '❌ Ditolak';
                        $keterangan = str_replace('[REJECT]', 'Permohonan ditolak:', $raw);
                      }
                    @endphp

                    @if($role)
                      <tr>
                        <td>{{ $role }}</td>
                        <td>{{ $val->user->nama ?? '-' }}</td>
                        <td>{{ $keterangan }}</td>
                        <td>{{ $val->created_at->format('d M Y H:i') }}</td>
                      </tr>
                    @endif
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>

        @php
          $user       = auth()->user();
          $status     = $statusId; // pakai status yang ditentukan di atas
          $unitTujuan = $request->service->unit_tujuan_id ?? null;
          $deptTujuan = \App\Models\User::where('unit_id', $unitTujuan)->value('department_id');

          $canApprove = in_array($status, [6, 7]) && (
                          $user->unit_id == $unitTujuan ||
                          (in_array($user->jabatan_id, [3,4]) && $deptTujuan && $user->department_id == $deptTujuan)
                        );
        @endphp

        @if($canApprove)
          <div class="card-footer d-flex justify-content-end">
            @if($status == 6)
              {{-- Mulai dikerjakan langsung (PATCH approveProgress) --}}
              <form method="POST"
                    action="{{ route('request_service.approveProgress', ['id' => $request->id]) }}"
                    onsubmit="return confirm('Mulai dikerjakan sekarang?')">
                @csrf
                @method('PATCH')
                <button class="btn btn-warning mr-2">
                  <i class="fas fa-play"></i> Mulai Dikerjakan
                </button>
              </form>

              {{-- Tolak pakai modal --}}
              <button class="btn btn-danger mr-2" data-toggle="modal" data-target="#modalTolak{{ $request->id }}">
                <i class="fas fa-times"></i> Tolak
              </button>

            @elseif($status == 7)
              {{-- Sedang dikerjakan → hanya bisa "Tandai Selesai" (tanpa Closed) --}}
              <button class="btn btn-success mr-2"
                      data-toggle="modal"
                      data-target="#modalSelesai{{ $request->id }}">
                <i class="fas fa-check"></i> Tandai Selesai
              </button>
            @endif
          </div>
        @endif

        <div class="card-footer text-right">
          <a href="{{ route('request_service.index') }}" class="btn btn-secondary btn-lg">
            <i class="fa fa-arrow-left mr-2"></i> Kembali ke List
          </a>
        </div>

        {{-- Modal aksi --}}
        @include('request_service.partials.modal_action', ['sr' => $request])

      </div>
    </div>
  </div>
</div>
@endsection
