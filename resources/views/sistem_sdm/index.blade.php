@php
    $startNumber = ($formHistories->currentPage() - 1) * $formHistories->perPage();
@endphp

@extends('layouts.app')

@section('content')
<style>
  /* ===== Soft Theme ===== */
  :root{
    --bg-soft:#f7f9fc;
    --card:#ffffff;
    --text:#1f2937;
    --muted:#6b7280;
    --primary:#2563eb;
    --primary-600:#1d4ed8;
    --ring:#e5e7eb;
    --success:#16a34a;
    --warning:#f59e0b;
    --info:#0ea5e9;
  }

  body { background: var(--bg-soft); }
  /* Full width container */
  .container{ max-width: 100% !important; }

  /* Heading */
  .page-title{
    display:flex; align-items:center; gap:.6rem;
    color:#065f46; margin-bottom:.2rem
  }
  .page-sub{ color:var(--muted); margin-bottom:1rem }

  /* Card */
  .card-soft{
    background:var(--card);
    border:1px solid var(--ring);
    border-radius:16px;
    box-shadow:0 8px 24px rgba(0,0,0,.04);
  }

  /* Tabs */
  .nav-tabs{ border:0 }
  .nav-tabs .nav-link{
    border:none; color:var(--muted); font-weight:600;
    border-radius:999px; padding:.45rem .9rem;
  }
  .nav-tabs .nav-link.active{
    color:#0f172a; background:#eef6ff; border:1px solid #dbeafe;
  }

  /* Rekap bar */
  .rekap{
    display:flex; justify-content:space-between; align-items:center; gap:1rem;
    font-size:.95rem; color:#0f172a;
  }
  .rekap .meta{ color:var(--muted) }

  /* Filters */
  .filter .form-control{
    border-radius:999px; border:1px solid var(--ring); box-shadow:none;
  }
  .filter .btn-primary{
    background:var(--primary); border-color:var(--primary);
    border-radius:999px; font-weight:600;
  }
  .filter .btn-primary:hover{ background:var(--primary-600); border-color:var(--primary-600) }
  .btn-reset{ border-radius:999px }

  /* ===== Table: no horizontal scroll, fit all ===== */
  .table-wrap{ border-radius:14px; overflow:hidden }
  .table{
    margin:0;
    table-layout: fixed;     /* Paksa tiap kolom ikut bagi ruang */
    width: 100%;
    font-size: .92rem;       /* Sedikit kecil biar muat */
  }
  .table thead th{
    background:#f3f6fb !important;
    color:#0f172a;
    border-bottom:1px solid var(--ring)!important;
    vertical-align:middle;
    padding:.55rem .6rem;
    font-weight:700;
    white-space: normal;
  }
  .table tbody td{
    vertical-align:top;
    padding:.55rem .6rem;
    white-space: normal;     /* IJINKAN WRAP */
    word-break: break-word;  /* Patahkan kalau kepanjangan */
    overflow-wrap: anywhere; /* Force wrap kalau perlu */
  }

  /* Kolom spesifik: sempitkan angka & aksi, biar kolom judul dapat ruang */
  th.col-no, td.col-no { width: 46px; text-align:center; }
  th.col-aksi, td.col-aksi { width: 110px; text-align:center; }
  th.col-tgl,  td.col-tgl  { width: 110px; text-align:center; }
  th.col-status, td.col-status { width: 130px; text-align:center; }
  /* kolom judul fleksibel, biar ambil sisa lebar */
  .col-judul{ white-space: normal !important; }

  /* Badges soft & ramping */
  .badge{ border-radius:999px; font-weight:600; padding:.28rem .5rem; }
  .b-soft-success{ background:#e8f8ee; color:#0f7a34; border:1px solid #d2f1dd }
  .b-soft-info{ background:#e8f6ff; color:#035b91; border:1px solid #cfeaff }
  .b-soft-primary{ background:#eef6ff; color:#0b4bc2; border:1px solid #dbeafe }
  .b-soft-warning{ background:#fff4e5; color:#7c4a00; border:1px solid #ffe1b3 }
  .b-soft-secondary{ background:#eef2f7; color:#374151; border:1px solid #e5e7eb }
  .b-dark{ background:#111827; color:#f9fafb }

  /* Action buttons compact */
  .btn-circle{ border-radius:999px; padding:.3rem .48rem; line-height:1; }
  .btn-circle i{ font-size:.85rem }

  /* Pagination info */
  .paging-info{ color:var(--muted); font-size:.9rem }

  /* Compact tweaks per breakpoint untuk cegah scroll */
  @media (max-width: 1600px){
    .table{ font-size:.90rem }
  }
  @media (max-width: 1366px){
    .table{ font-size:.88rem }
    th.col-aksi, td.col-aksi { width: 100px; }
  }
  @media (max-width: 1200px){
    .table{ font-size:.86rem }
    th.col-status, td.col-status { width: 120px; }
    th.col-tgl, td.col-tgl { width: 100px; }
  }
  @media (max-width: 992px){
    .table{ font-size:.84rem }
    .btn-circle{ padding:.28rem .44rem }
  }

  /* PDF action: outline merah biar kontras */
.btn-outline-danger.btn-circle{ border-width:2px; }
.btn-outline-danger.btn-circle:hover{ background:#dc2626; color:#fff; }
/* biar ikon ngikut warna tombol (bukan dipaksa putih) */
.btn-circle i{ color: inherit; }
</style>

<div class="container mt-4">

  <h3 class="page-title">
    <i class="fas fa-file-alt text-success"></i>
    Formulir BAP & Kronologis
  </h3>
  <div class="page-sub">Kelola dan pantau pengajuan dengan tampilan yang nyaman & tanpa geser.</div>

  <!-- Tabs Navigasi -->
  <ul class="nav nav-tabs mb-3">
    @unless(auth()->user()->hasRole('mutu'))
      <li class="nav-item">
        <a class="nav-link {{ request()->is('bap/form-bap') ? 'active' : '' }}" href="{{ route('bap.form_bap') }}">
          📝 TAMBAH BAP
        </a>
      </li>
    @endunless
  </ul>

  <!-- Statistik Rekap -->
  <div class="card-soft p-3 mb-3">
    <div class="rekap">
      <div>
        <strong>Total Hari Ini:</strong> {{ $totalToday }}
        <span class="mx-2">|</span>
        <strong>Selesai:</strong> {{ $totalSelesai }}
        <span class="mx-2">|</span>
        <strong>Pending:</strong> {{ $totalPending }}
      </div>
      <div class="meta">
        Update terakhir: {{ now()->format('d/m/Y H:i') }}
      </div>
    </div>
  </div>

  <!-- Filter -->
  <form method="GET" action="{{ route('bap.index') }}" class="row g-2 align-items-center mb-4 filter">
    <div class="col-6 col-md-2">
      <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
    </div>
    <div class="col-6 col-md-2">
      <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
    </div>
    <div class="col-12 col-md-3">
      <input type="text" name="keyword" class="form-control" placeholder="🔍 Cari judul / user..." value="{{ request('keyword') }}">
    </div>
    <div class="col-6 col-md-2">
      <select name="status" class="form-control">
        <option value="">-- Semua Status --</option>
        <option value="Dibuat" {{ request('status') == 'Dibuat' ? 'selected' : '' }}>Dibuat</option>
        <option value="Disetujui Unit" {{ request('status') == 'Disetujui Unit' ? 'selected' : '' }}>Disetujui Kepala Unit</option>
        <option value="Disetujui Supervision" {{ request('status') == 'Disetujui Supervision' ? 'selected' : '' }}>Disetujui Supervision</option>
        <option value="Disetujui Manager" {{ request('status') == 'Disetujui Manager' ? 'selected' : '' }}>Disetujui Manager</option>
        <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
      </select>
    </div>
    <div class="col-6 col-md-2">
      <select name="unit_id" id="filter-unit" class="form-control">
        <option value="">-- Semua Unit --</option>
        @foreach(($units ?? []) as $u)
          <option value="{{ $u->id }}" {{ (string)$u->id === (string)request('unit_id') ? 'selected' : '' }}>{{ $u->nama_unit }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-6 col-md-1 d-grid">
      <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i> Filter</button>
    </div>
    @if(request()->hasAny(['start_date','end_date','keyword','status','unit_id']))
      <div class="col-12 mt-1">
        <a href="{{ route('bap.index') }}" class="btn btn-sm btn-outline-secondary btn-reset">Reset</a>
      </div>
    @endif
  </form>

  <!-- Tabel Data -->
  <div class="table-wrap card-soft">
    <table class="table table-striped table-bordered table-sm align-middle">
      <thead class="text-center">
        <tr>
          <th class="col-no">No</th>
          <th>Dibuat Oleh</th>
          <th>Divisi Verifikasi</th>
          <th class="col-judul">Judul / Pasien</th>
          <th class="col-tgl">Tanggal Dibuat</th>
          <th class="col-status">Status</th>
          <th>Terakhir Disetujui Oleh</th>
          <th class="col-aksi">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($formHistories as $history)
          <tr>
            <td class="col-no">{{ $loop->iteration + $startNumber }}</td>
            <td>{{ $history->creator->nama ?? '-' }}</td>
            <td class="text-center">{{ $history->divisi_verifikasi ?? '-' }}</td>
            <td class="text-start col-judul">{{ $history->judul ?? '-' }}</td>
            <td class="text-center col-tgl">{{ $history->created_at ? \Carbon\Carbon::parse($history->created_at)->format('d/m/Y') : '-' }}</td>
            <td class="text-center col-status">
              @php
                $statusLabel = $history->status ?? 'Pending';
                $badgeClass = 'b-soft-secondary';
                if (str_contains($statusLabel, 'Selesai'))        $badgeClass = 'b-soft-success';
                elseif (str_contains($statusLabel, 'Dikerjakan')) $badgeClass = 'b-soft-primary';
                elseif (str_contains($statusLabel, 'Disetujui'))  $badgeClass = 'b-soft-info';
                elseif (str_contains($statusLabel, 'Dibuat'))     $badgeClass = 'b-soft-warning';
                elseif (str_contains($statusLabel, 'Pending'))    $badgeClass = 'b-soft-warning';
              @endphp
              <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
            </td>
            <td class="text-center">
              @if (!empty($history->latest_approval))
                @php
                  $badgeColor = 'b-soft-success';
                  if (str_contains($history->latest_approval, 'Unit Terkait'))  $badgeColor = 'b-dark';
                  elseif (str_contains($history->latest_approval, 'Manager'))   $badgeColor = 'b-soft-primary';
                  elseif (str_contains($history->latest_approval, 'Kepala Unit')) $badgeColor = 'b-soft-info';
                  elseif (str_contains($history->latest_approval, 'Supervision')) $badgeColor = 'b-soft-warning';

                  $matches = [];
                  preg_match('/^(.*\)) (.*)$/', $history->latest_approval, $matches);
                  $namaJabatan = $matches[1] ?? $history->latest_approval;
                  $tanggalJam  = $matches[2] ?? null;
                @endphp
                <span class="badge {{ $badgeColor }} text-wrap d-inline-block" style="line-height:1.2; white-space:normal;">
                  {{ $namaJabatan }}
                  @if ($tanggalJam)
                    <br><small class="text-muted">{{ $tanggalJam }}</small>
                  @endif
                </span>
              @else
                <span class="text-muted">Belum ada approval</span>
              @endif
            </td>
            <td class="col-aksi">
              <a href="{{ route('bap.detail', $history->id) }}" class="btn btn-info btn-circle mb-1" title="Lihat">
                <i class="fas fa-eye text-white"></i>
              </a>
              <a href="{{ route('bap.cetak', $history->id) }}" target="_blank"
   class="btn btn-outline-danger btn-circle mb-1" title="PDF">
  <i class="fas fa-file-pdf"></i>
</a>
              @if (!auth()->user()->hasRole('mutu') && $history->status == 'Pending' && $history->user_id == auth()->id() && user_can('edit_bap'))
                <a href="{{ route('bap.edit', $history->id) }}" class="btn btn-warning btn-circle mb-1" title="Edit">
                  <i class="fas fa-edit text-white"></i>
                </a>
                <form action="{{ route('bap.destroy', $history->id) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Yakin hapus form ini?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-danger btn-circle mb-1" title="Hapus"><i class="fas fa-trash text-white"></i></button>
                </form>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted py-4">Belum ada data.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div class="paging-info">
      Menampilkan {{ $formHistories->firstItem() }} sampai {{ $formHistories->lastItem() }} dari total {{ $formHistories->total() }} data
    </div>
    <div>
      {{ $formHistories->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>
@endsection
