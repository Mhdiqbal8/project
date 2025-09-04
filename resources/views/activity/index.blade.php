@extends('layouts.app')

@section('content')
@php
  use Illuminate\Support\Str;

  // map warna untuk action & method
  function actionBadgeClass($a){
    $a = strtolower($a ?? '');
    return match (true) {
      Str::contains($a, ['create','store','tag'])      => 'bg-success',
      Str::contains($a, ['approve','acc','final'])     => 'bg-primary',
      Str::contains($a, ['update','edit'])             => 'bg-warning text-dark',
      Str::contains($a, ['delete','destroy','reject']) => 'bg-danger',
      default                                          => 'bg-secondary'
    };
  }
  function methodBadgeClass($m){
    return match (strtoupper($m ?? '')) {
      'GET'    => 'bg-secondary',
      'POST'   => 'bg-primary',
      'PUT', 'PATCH' => 'bg-warning text-dark',
      'DELETE' => 'bg-danger',
      default  => 'bg-dark'
    };
  }
@endphp

<style>
  /* polish kecil biar enak dilihat */
  .card-sticky { position: sticky; top: 0; z-index: 2; }
  .table td, .table th { vertical-align: middle; }
  .nowrap { white-space: nowrap; }
  .muted-12 { font-size:.775rem; color:#6c757d; }
  .ip-chip { font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }

  /* Hard block komponen DataTables kalau ada init global (darurat di halaman ini saja) */
  .dt-container .dataTables_info,
  .dt-container .dataTables_paginate,
  .dt-container .dataTables_length,
  .dt-container .dataTables_filter { display: none !important; }
</style>

<div class="container mt-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <h4 class="fw-bold mb-0">🗒️ Activity Logs</h4>
      <div class="text-muted">Pantau semua aksi pengguna—filter cepat & klik subject buat lompat ke detail.</div>
    </div>
  </div>

  {{-- FILTERS --}}
  <div class="card shadow-sm card-sticky mb-3">
    <div class="card-body">
      <form method="GET" id="filterForm" class="row g-2 align-items-end">
        <div class="col-12 col-md-3">
          <label class="form-label muted-12 mb-1">Cari</label>
          <input type="text" name="q" value="{{ request('q') }}" class="form-control"
            placeholder="User / action / deskripsi / URL / IP">
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label muted-12 mb-1">Dari</label>
          <input type="date" name="from" value="{{ request('from') }}" class="form-control">
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label muted-12 mb-1">Sampai</label>
          <input type="date" name="to" value="{{ request('to') }}" class="form-control">
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label muted-12 mb-1">Action</label>
          <select name="action" class="form-select">
            <option value="">Semua Action</option>
            @foreach($actions as $a)
              <option value="{{ $a }}" @selected(request('action')===$a)>{{ $a }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-6 col-md-2">
          <label class="form-label muted-12 mb-1">User ID</label>
          <input type="number" name="user_id" value="{{ request('user_id') }}" class="form-control" placeholder="#id">
        </div>
        <div class="col-12 col-md-1 d-grid">
          <button class="btn btn-primary">Filter</button>
        </div>
      </form>

      {{-- Quick ranges --}}
      <div class="d-flex gap-2 mt-2">
        <button class="btn btn-sm btn-outline-secondary quick-range" data-range="today">Hari ini</button>
        <button class="btn btn-sm btn-outline-secondary quick-range" data-range="7d">7 hari</button>
        <button class="btn btn-sm btn-outline-secondary quick-range" data-range="month">Bulan ini</button>
        <a href="{{ route('activity.index') }}" class="btn btn-sm btn-link text-danger ms-auto">Reset</a>
      </div>
    </div>
  </div>

  {{-- TABLE --}}
  <div class="card shadow-sm">
    <div class="table-responsive dt-container">
      <table id="logsTable" class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th class="nowrap" style="width:160px">Waktu</th>
            <th style="width:200px">User</th>
            <th style="width:160px">Action</th>
            <th style="width:220px">Subject</th>
            <th>Deskripsi</th>
            <th style="width:260px">Request</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($logs as $log)
            @php
              $hasSubject = !empty($log->subject_type);
              $type = $hasSubject ? class_basename($log->subject_type) : null;
              $id   = $log->subject_id ?? null;

              $link = null;
              if ($hasSubject) {
                $link = match ($type) {
                  'BapForm'        => route('bap.detail', $id),
                  'KronologisForm' => route('kronologis.view', $id),
                  'Service'        => (Route::has('service.show') ? route('service.show', $id) : null),
                  'RequestService' => (Route::has('request_service.show') ? route('request_service.show', $id) : null),
                  'LaporanKerja'   => (Route::has('laporan-kerja.show') ? route('laporan-kerja.show', $id) : null),
                  default          => null,
                };
              }

              $actionClass = actionBadgeClass($log->action);
              $methodClass = methodBadgeClass($log->method);
            @endphp

            <tr>
              <td class="nowrap">
                <div>{{ $log->created_at->format('d-m-Y H:i') }}</div>
                <div class="muted-12">#{{ $log->id }}</div>
              </td>

              <td>
                <div class="fw-semibold">{{ $log->user->nama ?? $log->user->username ?? '—' }}</div>
                <div class="muted-12">ID: {{ $log->user_id ?? '—' }}</div>
              </td>

              <td>
                <span class="badge {{ $actionClass }}">{{ $log->action }}</span>
              </td>

              <td>
                @if($hasSubject)
                  @if($link)
                    <a href="{{ $link }}" class="small text-decoration-none">
                      {{ $type }} <span class="text-muted">#{{ $id }}</span>
                    </a>
                  @else
                    <span class="small">{{ $type }} #{{ $id }}</span>
                  @endif
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>

              <td>{{ $log->description ?? '—' }}</td>

              <td>
                <div class="d-flex align-items-center gap-2">
                  <span class="badge {{ $methodClass }}">{{ strtoupper($log->method ?? '—') }}</span>
                  <span class="ip-chip">{{ $log->ip_address ?? '—' }}</span>
                  @if($log->url)
                    <button class="btn btn-sm btn-outline-secondary px-2 py-1 copy-url"
                            data-url="{{ $log->url }}" title="Copy URL"
                            data-bs-toggle="tooltip" data-bs-title="Copy URL">
                      <i class="bi bi-clipboard"></i>
                    </button>
                  @endif
                </div>
                <div class="muted-12" title="{{ $log->url ?? '' }}">
                  {{ Str::limit($log->url ?? '', 54) }}
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-5">
                <div class="text-muted">
                  <div class="mb-1">Belum ada log untuk filter saat ini.</div>
                  <a href="{{ route('activity.index') }}" class="btn btn-sm btn-outline-primary">Tampilkan semua</a>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($logs->hasPages())
      <div class="card-footer d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <div class="muted-12">
          Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ $logs->total() }}
        </div>
        <div class="ms-auto">
          {{ $logs->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
      </div>
    @endif
  </div>
</div>

{{-- JS kecil: quick ranges, debounce search, copy URL, tooltips, kill DataTables --}}
@push('scripts')
<script>
(function(){
  // Jika ada init DataTables global, pastikan tabel ini tidak pakai
  if (window.jQuery && $.fn.DataTable && $.fn.DataTable.isDataTable('#logsTable')) {
    $('#logsTable').DataTable().destroy();
  }

  // Bootstrap tooltip
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  tooltipTriggerList.map(el => new bootstrap.Tooltip(el));

  // Quick range
  const form = document.getElementById('filterForm');
  const from = form.querySelector('input[name="from"]');
  const to   = form.querySelector('input[name="to"]');
  document.querySelectorAll('.quick-range').forEach(btn=>{
    btn.addEventListener('click', e=>{
      e.preventDefault();
      const now = new Date();
      const pad = n => String(n).padStart(2,'0');
      const toStr = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}`;
      let fromDate = new Date(now);
      const r = btn.dataset.range;
      if(r==='today'){ /* same day */ }
      else if(r==='7d'){ fromDate.setDate(now.getDate()-6); }
      else if(r==='month'){ fromDate = new Date(now.getFullYear(), now.getMonth(), 1); }
      const fromStr = `${fromDate.getFullYear()}-${pad(fromDate.getMonth()+1)}-${pad(fromDate.getDate())}`;
      from.value = fromStr; to.value = toStr; form.submit();
    });
  });

  // Debounce search typing
  let t=null;
  const q = form.querySelector('input[name="q"]');
  q.addEventListener('input', ()=>{
    clearTimeout(t);
    t = setTimeout(()=>form.submit(), 500);
  });

  // Copy URL
  document.querySelectorAll('.copy-url').forEach(btn=>{
    btn.addEventListener('click', async ()=>{
      try {
        await navigator.clipboard.writeText(btn.dataset.url);
        btn.setAttribute('data-bs-title','Copied!');
        bootstrap.Tooltip.getInstance(btn).show();
        setTimeout(()=>{
          btn.setAttribute('data-bs-title','Copy URL');
          bootstrap.Tooltip.getInstance(btn).hide();
        }, 900);
      } catch(e){}
    });
  });
})();
</script>
@endpush
@endsection
