@extends('layouts.app')

@section('content')
<div class="header bg-primary pb-6">
  <div class="px-4">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Permohonan Service</h6>
        </div>
        <div class="col-lg-6 col-5 text-right">
          @unless(auth()->user()->hasRole('mutu'))
            <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#tambah">
              Tambah data
            </button>
          @endunless
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid mt--6">
  <div class="card shadow-sm">
    @include('components.alert')

    {{-- FILTER --}}
    <form method="GET" class="p-4 pt-2 pb-3">
      <h5 class="mb-3">Filter Permohonan</h5>
      <div class="row align-items-end">
        <div class="col-md-3 mb-2">
          <label>Cari Tiket / Nama</label>
          <input type="text" name="search" class="form-control" value="{{ request('search') }}">
        </div>

        <div class="col-md-2 mb-2">
          <label>Belum Disetujui</label>
          <select name="belum_approve" class="form-control">
            <option value="">Semua</option>
            <option value="1" {{ request('belum_approve') == '1' ? 'selected' : '' }}>Belum Disetujui</option>
          </select>
        </div>

        <div class="col-md-2 mb-2">
          <label>Dari Tanggal</label>
          <input type="date" name="from" class="form-control" value="{{ request('from') }}">
        </div>

        <div class="col-md-2 mb-2">
          <label>Sampai Tanggal</label>
          <input type="date" name="to" class="form-control" value="{{ request('to') }}">
        </div>

        <div class="col-md-1 mb-2">
          <label>Tampil</label>
          <select name="limit" class="form-control">
            @foreach([5, 10, 25, 50, 100] as $limit)
              <option value="{{ $limit }}" {{ request('limit', 10) == $limit ? 'selected' : '' }}>{{ $limit }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-2 mb-2">
          <button type="submit" class="btn btn-primary btn-block" style="margin-top: 8px;">
            <i class="fas fa-filter mr-1"></i> Terapkan
          </button>
        </div>
      </div>
    </form>

    {{-- TABEL --}}
    <div class="table-responsive p-4">
      <table class="table table-bordered table-hover">
        <thead class="thead-light">
          <tr>
            <th>No</th>
            <th>No. Tiket</th>
            <th>Nama Pemohon</th>
            <th>Department</th>
            <th>Unit</th>
            <th>Tgl. Permohonan</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
    <tbody>
  @forelse($services as $service)
    @php
      $user = $service->user;
      $unitPemohon = optional($user)->unit;
      $dept = optional($user)->department;

      $auth = auth()->user();
      $jabatan_id = $auth->jabatan_id;

      $isKepalaUnit = $jabatan_id == 2;
      $isSPV = $jabatan_id == 3;
      $isManager = $jabatan_id == 4;

      $isUnitYangDipimpin = \App\Models\Unit::where('kepala_unit_id', $auth->id)
                                ->where('id', $user->unit_id)
                                ->exists();

      $canApproveSPV = $isSPV && $user->department_id == $auth->department_id;
      $canApproveManager = $isManager && $user->department_id == $auth->department_id;

      $canReject = ($canApproveSPV && $service->status_id == 3) || ($canApproveManager && $service->status_id < 6);
      $isUrgent = $service->type_permohonan == 1;
    @endphp
    <tr @if($isUrgent && $service->status_id <= 7) class="text-danger" @endif>
      <td>{{ ($services->currentPage() - 1) * $services->perPage() + $loop->iteration }}</td>
      <td>{{ $service->no_tiket }}</td>
      <td>{{ $user->nama ?? '-' }}</td>
      <td>{{ $dept->nama ?? '-' }}</td>
      <td>{{ $unitPemohon->nama_unit ?? '-' }}</td>
      <td>{{ optional($service->created_at)->format('d M Y H:i') }} WIB</td>
      <td>
        <span class="badge
          @if(in_array($service->status_id,[3,4])) badge-info
          @elseif($service->status_id==5) badge-warning
          @elseif($service->status_id==6) badge-primary
          @elseif($service->status_id==7) badge-success
          @elseif($service->status_id==8) badge-secondary
          @else badge-danger @endif">
          {{ $service->status_name }}
        </span>
        @if($isUrgent)
          <span class="badge badge-danger ml-1">URGENT</span>
        @endif
      </td>
      <td>
        <a href="{{ route('service.show', $service->id) }}" class="btn btn-sm btn-info" title="Detail">
          <i class="fa fa-eye"></i>
        </a>

        @if($canApproveSPV && $service->status_id == 3)
          <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalApprove{{ $service->id }}" title="Approve SPV">
            <i class="fa fa-check"></i>
          </button>
        @endif

        @if($canApproveManager && $service->status_id < 6)
          <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalApprove{{ $service->id }}" title="Approve Manager">
            <i class="fa fa-check"></i>
          </button>
        @endif

        @if($canReject)
          <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modalTolak{{ $service->id }}" title="Tolak">
            <i class="fa fa-times"></i>
          </button>
        @endif

        @include('service._modal_approve', ['service' => $service])
        @include('service._modal_tolak', ['service' => $service])
      </td>
    </tr>
  @empty
    <tr>
      <td colspan="8" class="text-center">Tidak ada data ditemukan.</td>
    </tr>
  @endforelse
</tbody>

      </table>
    </div>

    {{-- PAGINATION --}}
    <div class="card-footer">
      {{ $services->appends(request()->query())->links('pagination::bootstrap-4') }}
    </div>
  </div>
</div>

{{-- Modal Tambah --}}
@unless(auth()->user()->hasRole('mutu'))
  @include('service._modal_tambah')
@endunless
@endsection

@push('styles')
<style>
  .form-control, .btn, select {
    border-radius: 6px;
  }

  label {
    font-size: 0.85rem;
    font-weight: 500;
  }

  .table th, .table td {
    vertical-align: middle !important;
    font-size: 0.88rem;
  }

  .btn-filter {
    background-color: #5e72e4;
    color: white;
    border-radius: 6px;
    font-weight: bold;
    transition: 0.3s ease;
  }

  .btn-filter:hover {
    background-color: #324cdd;
  }

  .card {
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  }
</style>
@endpush

@push('scripts')
<script>
  $(document).ready(function () {
    $(document).on('change', '#jenis_inventaris_id', function () {
      var jenisId = $(this).val();
      var inventarisSelect = $('#inventaris_id');
      inventarisSelect.html('<option value="">Memuat...</option>');

      if (jenisId) {
        $.ajax({
          url: '/get-inventaris/' + jenisId,
          type: 'GET',
          success: function (data) {
            inventarisSelect.empty().append('<option value="">-- Pilih Inventaris --</option>');
            $.each(data, function (key, value) {
              inventarisSelect.append('<option value="' + value.id + '">' + value.nama + '</option>');
            });
          },
          error: function () {
            inventarisSelect.html('<option value="">Gagal memuat</option>');
          }
        });
      } else {
        inventarisSelect.html('<option value="">-- Pilih Inventaris --</option>');
      }
    });
  });
</script>
@endpush